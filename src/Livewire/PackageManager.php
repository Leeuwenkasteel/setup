<?php
namespace Leeuwenkasteel\Setup\Livewire;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Livewire\Component;
use Artisan;

class PackageManager extends Component
{
    public $packages;
    public $installing = [];
	public $step = 1;
	public $token;
	public $notifications = [];
	public $extra = [];
	public $folder = 'test';
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
		$env = env('SETUP');
		if (!isset($env)) {
            $envPath = base_path('.env');

            $envContent = file_get_contents($envPath);
            if (strpos($envContent, 'SETUP=') === false) {
                file_put_contents($envPath, PHP_EOL . 'SETUP=false', FILE_APPEND);
            }
        }
        
        if ($env == 1) {
            return redirect()->to('/');
        }
		$token = env('GITHUB_TOKEN');
		if (!isset($token)) {
            $envPath = base_path('.env');

            $envContent = file_get_contents($envPath);
            if (strpos($envContent, 'GITHUB_TOKEN=') === false) {
                file_put_contents($envPath, PHP_EOL . 'GITHUB_TOKEN=', FILE_APPEND);
            }
        }
        $token = env('GITHUB_TOKEN');
        if (!empty($token)) {
			$this->step = 2;
			$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] token bestaat al";
            
        }
		
		if($this->step == 2){
			if($this->checkComposer('auth')){
				$this->step = 3;
			}
		}
		
		$composerFile = base_path('composer.json');
		$composerJson = json_decode(File::get($composerFile), true);

		// Zoek naar alle "leeuwenkasteel/*" packages in "require"
		$iPackages = collect($composerJson['require'] ?? [])
			->keys() // Pak de package-namen
			->filter(fn($package) => str_starts_with($package, 'leeuwenkasteel/') && $package !== 'leeuwenkasteel/setup')
			->map(fn($package) => str_replace('leeuwenkasteel/', '', $package)) // Verwijder "leeuwenkasteel/"
			->toArray();
			
			$this->installPackages = $iPackages;
			
		$this->DB_CONNECTION = env('DB_CONNECTION');
		
		if($this->DB_CONNECTION == 'mysql'){
			$this->showDbDetails = true;
		}else{
			$this->showDbDetails = false;
		}
    }
	
	public function addToken(){
		$file = file_get_contents(base_path('.env'));
		$file = str_replace('GITHUB_TOKEN='.env('GITHUB_TOKEN'), 'GITHUB_TOKEN='.$this->token, $file);
		$file = file_put_contents(base_path('.env'), $file);
		$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] token is opgeslagen";
		$this->step = 2;
	}
	
	public function installGeneral(){
		foreach($this->packages as $pack){
			if($pack['kind'] == 'basic'){
				$this->installPackage($pack['name']);
				$this->systemLink($pack['name']);
			}
		}
		$this->addComposer('auth');
		$this->step = 3;
	}
	
	public function installExtra(){
		foreach($this->extra as $k => $v){
			$this->installPackage($k);
			$this->addComposer($k);
			$this->systemLink($k);
		}
		
		$this->step = 4;
	}
	
	public function installPackage($name){
		$token = env('GITHUB_TOKEN');
		$repoUrl = "https://".$token."@github.com/Leeuwenkasteel/".$name.".git";
		$cloneDir = base_path("packages/$this->folder/$name");

		// Verwijder bestaande installatie indien nodig
		if (File::exists($cloneDir)) {
			$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] Verwijderen van bestaande map $name...";
			File::deleteDirectory($cloneDir);
		}

		// Stap 1: Clone de repository met Process
		$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] Clonen van $name...";
		$cloneProcess = new Process(["git", "clone", $repoUrl, $cloneDir]);
		$cloneProcess->run();

		if (!$cloneProcess->isSuccessful()) {
			$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] Git Clone Mislukt: " . $cloneProcess->getErrorOutput();
		}

		$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] $name succesvol geïnstalleerd!";
	}
	public function checkComposer($name){
		$composerPackage = base_path('packages/leeuwenkasteel/'.$name.'/composer.json');
		$composerPackageJson = json_decode(File::get($composerPackage), true);
		$version = $composerPackageJson['version'];
		
		$composerFile = base_path('composer.json');

		// JSON inlezen
		$composerJson = json_decode(File::get($composerFile), true);
		if (isset($composerJson['require']['leeuwenkasteel/'.$name])) {
			return true;
		}else{
			return false;
		}
	}
	public function addComposer($name){
		if ($this->checkComposer($name)) {
			$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] Package '$name' is al geïnstalleerd!";
		}else{
			$composerJson['require']['leeuwenkasteel/'.$name] = '^'.$version;
			File::put($composerFile, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
			$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] Package '$name' is toegevoegd aan composer!";
		}
		
	}
	
	public function systemLink($name){
		$composerFile = base_path('composer.json');
		$composerJson = json_decode(File::get($composerFile), true);
		$localPath = "packages/$this->folder/$name";

		// Controleer of de repository-sectie al bestaat
		if (!isset($composerJson['repositories'])) {
			$composerJson['repositories'] = [];
		}

		// Controleer of de package al in repositories staat
		foreach ($composerJson['repositories'] as $repo) {
			if ($repo['type'] == 'path' && $repo['url'] == $localPath) {
				$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] Symlink voor $name bestaat al!";
				$exists = true;
				break;
			}
		}
		if (!$exists) {
			$composerJson['repositories'][] = [
				"type" => "path",
				"url" => $localPath,
				"options" => ["symlink" => true]
			];

			// Voeg de package toe aan de 'require' sectie
			$composerJson['require'][$name] = "*";

			// Update composer.json
			File::put($composerFile, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

			$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] Symlink toegevoegd voor $name!";
		}
	}
	
	public function composerUpdate() {
		$projectPath = base_path();
		$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] ⏳ Composer update gestart...";

		$process = new Process(['composer', 'update']);
		$process->setWorkingDirectory($projectPath);
		$process->setEnv(['COMPOSER_HOME' => $projectPath]);

		// Voer het proces uit en vang live output op
		$process->run(function ($type, $buffer) {
			if (Process::ERR === $type) {
				$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] ❌ Fout: " . trim($buffer);
			} else {
				$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] 🔄 " . trim($buffer);
			}
		});

		// Controleer of Composer succesvol was
		if (!$process->isSuccessful()) {
			$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] ❌ Composer update mislukt!";
		} else {
			$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] ✅ Composer update succesvol!";
			$this->step = 6;
		}
	}
	
	public function install($name){
		$command = ['php', 'artisan', 'install:'.$name];  // Het commando dat je wilt uitvoeren

		// Maak een nieuw Process-object
		$process = new Process($command);
		$process->setWorkingDirectory(base_path());
		// Voer het proces uit
		$process->run();

		// Controleer of het proces succesvol was
		if (!$process->isSuccessful()) {
			// Foutmelding ophalen en weergeven
			$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] Fout bij uitvoering van het commando: " . $process->getErrorOutput();
		} else {
			// Succesvolle uitvoering
			$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] " . $process->getOutput();
			$this->notifications[] = "[" . date("Y-m-d H:i:s") . "] $name is geinstalleerd: ";
		}
			
	}
	
	public function changeEvent($item){
		if($item == 'mysql'){
			$this->showDbDetails = true;
		}else{
			$this->showDbDetails = false;
		}
	}
	
	public function saveDb(){
		if($this->DB_CONNECTION == 'mysql'){
			$file = file_get_contents(base_path('.env'));
			$file = str_replace('DB_CONNECTION='.env('DB_CONNECTION'), 'DB_CONNECTION='.$this->DB_CONNECTION, $file);
			$file = str_replace('# DB_HOST='.env('DB_HOST'), 'DB_HOST='.$this->DB_HOST, $file);
			$file = str_replace('# DB_PORT='.env('DB_PORT'), 'DB_PORT='.$this->DB_PORT, $file);
			$file = str_replace('# DB_DATABASE='.env('DB_DATABASE'), 'DB_DATABASE='.$this->DB_DATABASE, $file);
			$file = str_replace('# DB_USERNAME='.env('DB_USERNAME'), 'DB_USERNAME='.$this->DB_USERNAME, $file);
			$file = str_replace('# DB_PASSWORD='.env('DB_PASSWORD'), 'DB_PASSWORD='.$this->DB_PASSWORD, $file);
			$file = file_put_contents(base_path('.env'), $file);
		}
		$this->step = 6;
	}
	
	public function finish(){
		$file = file_get_contents(base_path('.env'));
		$file = str_replace('SETUP='.env('SETUP'), 'SETUP=true', $file);
		$file = file_put_contents(base_path('.env'), $file);
		
		return redirect()->route('setup.instructions');
	}
	
	public function changeStep($step){
		$this->step = $step;
	}

    public function render()
    {
        return view('setup::packages');
    }
}
