<?php

namespace Leeuwenkasteel\Setup\Livewire;

use Livewire\Component;
use Symfony\Component\Process\Process;
use Config;

class Pull extends Component
{
    public $packagesStatus = [];
    public $packages = [];
    public $currentPackage = '';
    public $isProcessing = false;
    public $table = [];
    public $processingIndex = 0; // To track which package we're processing

    protected $listeners = [
        'checkPackageStatus' => 'checkSinglePackage',
    ];

    public function mount()
    {
        // Haal de lijst van packages op
        $this->packages = array_diff(scandir(base_path('packages/leeuwenkasteel')), ['.', '..']);

        // Controleer of de array van packages leeg is
        if (empty($this->packages)) {
            session()->flash('error', 'Geen packages gevonden.');
            return;
        }
        $this->progressTable(); // Start de verwerking van de packages
    }

    public function progressTable(){
        $config = Config::get('config-setup.packages', []);
		
		foreach($this->packages as $p){
			$this->table[$p] = [
                'description' => Config::get("config-setup.packages.$p.description", 'No description available'),
                'status' => 'Verwerken...',
                'class' => 'text-muted',
            ];
			$status = $this->checkPackageStatus(base_path("packages/leeuwenkasteel/$p"));
			
			$this->table[$p] = [
                'status' => $status['status'],
                'class' => $status['class'],
            ];
			//break;
		}
    }

    private function checkPackageStatus($packagePath)
    {
        $fetchProcess = $this->runGitCommand($packagePath, ['git', 'fetch']);
        if (!$fetchProcess->isSuccessful()) {
            return $this->generateErrorStatus("Probleem met 'git fetch' in '$packagePath'.");
        }

        $statusProcess = $this->runGitCommand($packagePath, ['git', 'status', '-b', '--porcelain']);
        $output = trim($statusProcess->getOutput());

        if (preg_match('/behind (\d+)/', $output, $matches)) {
            return $this->generateStatus('pull_needed', "Pull nodig ({$matches[1]} commits achter).", 'text-warning');
        }

        return $this->generateStatus('up_to_date', 'Up-to-date.', 'text-success');
    }

    private function runGitCommand($packagePath, $command)
    {
        $process = new Process($command);
        $process->setWorkingDirectory($packagePath);
        $process->run();

        $this->logGitOutput($process);

        return $process;
    }

    private function generateStatus($status, $text, $class)
    {
        return [
            'status' => $status,
            'text' => $text,
            'small' => $status === 'up_to_date'
                ? 'De repository is volledig gesynchroniseerd.'
                : 'Er zijn nieuwe wijzigingen beschikbaar in de remote repository.',
            'class' => $class,
        ];
    }

    private function generateErrorStatus($message)
    {
        return [
            'status' => 'error',
            'text' => 'Git fout.',
            'small' => $message,
            'class' => 'text-danger',
        ];
    }

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
