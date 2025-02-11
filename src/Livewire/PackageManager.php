<?php
namespace Leeuwenkasteel\Setup\Livewire;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;
use Livewire\Component;

class PackageManager extends Component
{
    public $packages;
    public $installing = [];

    public function mount()
    {
        $this->packages = Config::get('packages.packages', []);
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
        return view('setup::package');
    }
}
