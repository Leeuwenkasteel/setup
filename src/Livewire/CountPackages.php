<?php

namespace Leeuwenkasteel\Setup\Livewire;

use Livewire\Component;
use Symfony\Component\Process\Process;
class CountPackages extends Component{

	public $count = 0;
	
	public function mount(){
		$this->countpackages();
	}
	
	public function countpackages(){
		$this->packages = array_diff(scandir(base_path('packages/leeuwenkasteel')), ['.', '..']);
        foreach ($this->packages as $package) {
            $packagePath = base_path("packages/leeuwenkasteel/$package");
            $this->checkPackageStatus($packagePath, $package);
        }
	}
	
	private function checkPackageStatus($packagePath, $package){
        // Voer 'git status -uno' uit om de status op te halen
        $statusProcess = new Process(['git', 'status', '-uno'], $packagePath);
        $statusProcess->run();

        $output = $statusProcess->getOutput();
		
		if (str_contains($output, 'Your branch is behind')) {
			$this->count++;
		}
		
	}
	
    public function render(){
        return view('setup::livewire.count');
    }
}