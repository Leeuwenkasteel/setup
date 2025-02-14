<?php

namespace Leeuwenkasteel\Setup\Livewire;

use Livewire\Component;
use Symfony\Component\Process\Process;

class Pull extends Component
{
    public $packagesStatus = [];
    public $packages = [];

    protected $listeners = ['packageInfo' => 'packageInfo'];

    public function mount()
    {
        $this->packageInfo();
    }

    public function packageInfo()
    {
        // Haal alle packages op
        $this->packages = array_diff(scandir(base_path('packages/leeuwenkasteel')), ['.', '..']);
        
        // Verwerk de status van elk package
        $this->packagesStatus = collect($this->packages)
            ->mapWithKeys(function ($package) {
                return [$package => $this->checkPackageStatus(base_path("packages/leeuwenkasteel/$package"))];
            })
            ->toArray();
    }

    private function checkPackageStatus($packagePath)
    {
        // Voer git fetch uit en controleer of de repository up-to-date is
        $fetchProcess = $this->runGitCommand($packagePath, ['git', 'fetch']);
        if (!$fetchProcess->isSuccessful()) {
            return $this->generateErrorStatus("Probleem met 'git fetch' in '$packagePath'.");
        }

        // Controleer de status van de git repository
        $statusProcess = $this->runGitCommand($packagePath, ['git', 'status', '-b', '--porcelain']);
        $output = trim($statusProcess->getOutput());

        // Controleer of de branch achterloopt
        if (preg_match('/behind (\d+)/', $output, $matches)) {
            return $this->generateStatus('pull_needed', "Pull nodig ({$matches[1]} commits achter).", 'text-warning');
        }

        return $this->generateStatus('up_to_date', 'Up-to-date.', 'text-success');
    }

    // Helper functie om een git command uit te voeren
    private function runGitCommand($packagePath, $command)
    {
        $process = new Process($command);
        $process->setWorkingDirectory($packagePath);
        $process->run();

        // Debugging: Log de uitvoer van het git commando voor diagnostische doeleinden
        $this->logGitOutput($process);

        return $process;
    }

    // Helper functie om een status array te genereren
    private function generateStatus($status, $text, $class)
    {
        return [
            'status' => $status,
            'text' => $text,
            'small' => $status === 'up_to_date' ? 'De repository is volledig gesynchroniseerd.' : 'Er zijn nieuwe wijzigingen beschikbaar in de remote repository.',
            'class' => $class,
        ];
    }

    // Helper functie om een error status te genereren
    private function generateErrorStatus($message)
    {
        return [
            'status' => 'error',
            'text' => 'Git fout.',
            'small' => $message,
            'class' => 'text-danger',
        ];
    }

    // Pull het package (geeft de status na een pull weer)
    public function pullPackage($package)
    {
        $packagePath = base_path("packages/leeuwenkasteel/$package");
        $this->runGitCommand($packagePath, ['git', 'pull']);

        // Verfris de status na het pullen
        $this->packageInfo();
    }

    // Logging van git output voor debugging
    private function logGitOutput($process)
    {
        if (!$process->isSuccessful()) {
            \Log::error("Git command failed: " . $process->getErrorOutput());
        } else {
            \Log::info("Git command output: " . $process->getOutput());
        }
    }

    public function render()
    {
        return view('setup::livewire.pull');
    }
}
