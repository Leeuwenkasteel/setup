<?php
namespace Leeuwenkasteel\Setup\Livewire;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Livewire\Component;


class PackageManager extends Component
{
    public $packages;
    public $installing = [];
	public $step = 1;
	public $token;
	public $notifications = [];
	public $extra = [];

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
			}
		}
		$this->step = 3;
	}
	
	public function installExtra(){
		foreach($this->extra as $k => $v){
			$this->installPackage($k);
		}
		$this->step = 4;
	}
	
	public function installPackage($name){
		$token = env('GITHUB_TOKEN');
		$repoUrl = "https://".$token."@github.com/Leeuwenkasteel/".$name.".git";
		$cloneDir = base_path("packages/test/$name");

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
	
	public function changeStep($step){
		$this->step = $step;
	}

    public function install($packageKey)
    {
        if (isset($this->installing[$packageKey])) {
            return;
        }

        $this->installing[$packageKey] = true;

        // Haal package details op
        $package = $this->packages[$packageKey] ?? null;

        if (!$package) {
            session()->flash('error', "Package niet gevonden.");
            return;
        }

        // Installeer afhankelijkheden eerst
        foreach ($package['dependencies'] as $dependency) {
            $this->install($dependency);
        }

        // Installeer het package via Composer
        $command = "COMPOSER_AUTH='{ \"github-oauth\": { \"github.com\": \"" . config('services.github.token') . "\" } }' composer require " . $package['name'];
        
        Process::run($command, function ($type, $output) {
            $this->dispatch('package-installed', ['message' => $output]);
        });

        session()->flash('success', "Package {$package['name']} geïnstalleerd!");
    }

    public function render()
    {
        return view('setup::packages');
    }
}
