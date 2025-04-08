<?php
namespace Leeuwenkasteel\Setup\Livewire;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class PackageManager extends Component
{
    public $packages;
    public $installing = [];
    public $step = 1;
    public $token;
    public $notifications = [];
    public $extra = [];
    public $folder = 'leeuwenkasteel';
    public $installPackages = [];

    public $showDbDetails = false;

    public $DB_CONNECTION;
    public $DB_HOST = '127.0.0.1';
    public $DB_PORT = '3306';
    public $DB_DATABASE;
    public $DB_USERNAME;
    public $DB_PASSWORD;

    public function mount()
    {
        $this->packages = Config::get('config-setup.packages', []);
        $this->token = env('GITHUB_TOKEN');

        if (!env('SETUP')) {
            $this->updateEnvIfMissing('SETUP', 'false');
        } else {
            return redirect()->to('/');
        }

        if (!$this->token) {
            $this->updateEnvIfMissing('GITHUB_TOKEN', '');
        } else {
            $this->step = 2;
            $this->notify('Token bestaat al');
        }

        $this->DB_CONNECTION = env('DB_CONNECTION');
        $this->showDbDetails = $this->DB_CONNECTION === 'mysql';
    }

    public function loadInstallPackages()
    {
        $composerJson = $this->readComposerJson();

        $this->installPackages = collect($composerJson['require'] ?? [])
            ->keys()
            ->filter(fn($package) => str_starts_with($package, 'leeuwenkasteel/') && $package !== 'leeuwenkasteel/setup')
            ->map(fn($package) => str_replace('leeuwenkasteel/', '', $package))
            ->toArray();
    }

    public function addToken()
    {
        $this->updateEnv('GITHUB_TOKEN', $this->token);
        $this->notify('Token is opgeslagen');
        $this->step = 2;
    }

    public function installGeneral()
    {
        foreach ($this->packages as $pack) {
            if ($pack['kind'] === 'basic') {
                $this->installPackage($pack['name']);
                $this->systemLink($pack['name']);
            }
        }
        $this->addComposer('auth');
        $this->step = 3;
    }

    public function installExtra()
    {
        foreach ($this->extra as $k => $v) {
            $this->installPackage($k);
            $this->addComposer($k);
            $this->systemLink($k);
        }
        $this->step = 4;
    }

    public function installPackage($name)
    {
        $token = env('GITHUB_TOKEN');
        $repoUrl = "https://$token@github.com/Leeuwenkasteel/$name.git";
        $cloneDir = base_path("packages/$this->folder/$name");

        if (File::exists($cloneDir)) {
            $this->notify("Verwijderen van bestaande map $name...");
            File::deleteDirectory($cloneDir);
        }

        $this->notify("Clonen van $name...");
        $process = new Process(['git', 'clone', $repoUrl, $cloneDir]);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->notify("Git Clone Mislukt: " . $process->getErrorOutput());
            return;
        }

        $this->notify("$name succesvol geïnstalleerd!");
    }

    public function checkComposer($name)
    {
        $composerJson = $this->readComposerJson();
        return isset($composerJson['require']['leeuwenkasteel/' . $name]);
    }

    public function addComposer($name)
    {
        if ($this->checkComposer($name)) {
            $this->notify("Package '$name' is al geïnstalleerd!");
            return;
        }

        $packageJsonPath = base_path("packages/leeuwenkasteel/$name/composer.json");
        if (!File::exists($packageJsonPath)) return;

        $packageJson = json_decode(File::get($packageJsonPath), true);
        $version = $packageJson['version'] ?? '*';

        $composerJson = $this->readComposerJson();
        $composerJson['require']['leeuwenkasteel/' . $name] = "^$version";

        $this->writeComposerJson($composerJson);
        $this->notify("Package '$name' is toegevoegd aan composer!");
    }

    public function systemLink($name)
    {
        $composerJson = $this->readComposerJson();
        $localPath = "packages/$this->folder/$name";

        if (!isset($composerJson['repositories'])) {
            $composerJson['repositories'] = [];
        }

        $exists = collect($composerJson['repositories'])
            ->contains(fn($repo) => $repo['type'] === 'path' && $repo['url'] === $localPath);

        if (!$exists) {
            $composerJson['repositories'][] = [
                "type" => "path",
                "url" => $localPath,
                "options" => ["symlink" => true]
            ];
            $composerJson['require'][$name] = "*";
            $this->writeComposerJson($composerJson);
            $this->notify("Symlink toegevoegd voor $name!");
        } else {
            $this->notify("Symlink voor $name bestaat al!");
        }
    }

    public function composerUpdate()
    {
        $this->notify("⏳ Composer update gestart...");

        $process = new Process(['composer', 'update']);
        $process->setWorkingDirectory(base_path());
        $process->run(function ($type, $buffer) {
            $this->notify(trim($buffer));
        });

        $this->step = $process->isSuccessful() ? 6 : $this->step;
        $this->notify($process->isSuccessful() ? "✅ Composer update succesvol!" : "❌ Composer update mislukt!");
    }

    public function install($name)
    {
        $process = new Process(['php', 'artisan', 'install:' . $name]);
        $process->setWorkingDirectory(base_path());
        $process->run();

        if (!$process->isSuccessful()) {
            $this->notify("Fout bij installatie: " . $process->getErrorOutput());
        } else {
            $this->notify("$name is geïnstalleerd.");
        }
    }

    public function changeEvent($item)
    {
        $this->showDbDetails = $item === 'mysql';
    }

    public function saveDb()
    {
        if ($this->DB_CONNECTION === 'mysql') {
            $this->updateEnv('DB_CONNECTION', 'mysql');
            $this->updateEnv('DB_HOST', $this->DB_HOST);
            $this->updateEnv('DB_PORT', $this->DB_PORT);
            $this->updateEnv('DB_DATABASE', $this->DB_DATABASE);
            $this->updateEnv('DB_USERNAME', $this->DB_USERNAME);
            $this->updateEnv('DB_PASSWORD', $this->DB_PASSWORD);
        }
        $this->step = 6;
    }

    public function finish()
    {
        $this->updateEnv('SETUP', 'true');
        return redirect()->route('setup.instructions');
    }

    public function changeStep($step)
    {
        $this->step = $step;
    }

    public function render()
    {
        return view('setup::packages');
    }

    private function readComposerJson()
    {
        return Cache::remember('composer_json', 60, fn() => json_decode(File::get(base_path('composer.json')), true));
    }

    private function writeComposerJson($data)
    {
        File::put(base_path('composer.json'), json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        Cache::forget('composer_json');
    }

    private function updateEnv($key, $value)
    {
        $file = file_get_contents(base_path('.env'));
        $pattern = "/^{$key}=.*$/m";
        $replacement = "{$key}={$value}";

        if (preg_match($pattern, $file)) {
            $file = preg_replace($pattern, $replacement, $file);
        } else {
            $file .= "\n{$key}={$value}";
        }

        file_put_contents(base_path('.env'), $file);
    }

    private function updateEnvIfMissing($key, $default)
    {
        if (!env($key)) {
            $this->updateEnv($key, $default);
        }
    }

    private function notify($message)
    {
        $this->notifications[] = "[" . date("Y-m-d H:i:s") . "] {$message}";
    }
}