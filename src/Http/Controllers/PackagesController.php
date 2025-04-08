<?php

namespace Leeuwenkasteel\Setup\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\Process\Process;
use Config;

class PackagesController extends Controller{  
	public function index(){
		$packages = array_diff(scandir(base_path('packages/leeuwenkasteel')), ['.', '..']);
		$config = Config::get('config-setup.packages', []);
		$table = [];
		
		foreach($packages as $p){
			$table[$p] = [
				'name' => $p,
				'description' => Config::get("config-setup.packages.$p.description", 'No description available'),
			];
		}
        return view('setup::packages.index', compact('table'));
    }
	
	public function getStatus($packageName)
    {
		
        $packagePath = base_path("packages/leeuwenkasteel/$packageName");

        if (!is_dir($packagePath)) {
            return response()->json([
                'statusText' => 'Package niet gevonden',
                'statusClass' => 'text-danger'
            ]);
        }

        $status = $this->getGitStatus($packagePath);

        return response()->json($status);
    }

    private function getGitStatus($packagePath)
    {
        $fetchProcess = $this->runGitCommand($packagePath, ['git', 'fetch']);
        if (!$fetchProcess->isSuccessful()) {
            return $this->generateErrorStatus("Probleem met 'git fetch'.");
        }

        $statusProcess = $this->runGitCommand($packagePath, ['git', 'status', '-b', '--porcelain']);
        $output = trim($statusProcess->getOutput());

        if (preg_match('/behind (\d+)/', $output, $matches)) {
            return $this->generateStatus("Pull nodig ({$matches[1]} commits achter).", 'text-warning');
        }

        return $this->generateStatus('Up-to-date.', 'text-success');
    }

    private function runGitCommand($packagePath, $command)
    {
        $process = new Process($command);
        $process->setWorkingDirectory($packagePath);
        $process->run();

        return $process;
    }

    private function generateStatus($text, $class)
    {
        return [
            'statusText' => $text,
            'statusClass' => $class
        ];
    }

    private function generateErrorStatus($message)
    {
        return [
            'statusText' => 'Git fout.',
            'statusClass' => 'text-danger'
        ];
    }
	
	public function pull($package)
    {
        $packagePath = base_path("packages/leeuwenkasteel/$package");

        if (!is_dir($packagePath)) {
            return redirect()->back()->with('error', "Package '$package' niet gevonden.");
        }

        // Voer de git pull uit
        $process = $this->runGitCommand($packagePath, ['git', 'pull']);

        if (!$process->isSuccessful()) {
            return redirect()->back()->with('error', "Er is een fout opgetreden bij het uitvoeren van 'git pull' voor '$package'.");
        }

        // Succesvolle pull
        return redirect()->back()->with('success', "Pull succesvol uitgevoerd voor '$package'.");
    }
}