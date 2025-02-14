<?php
namespace Leeuwenkasteel\Setup\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Artisan;

class InstallCommand extends Command {
    protected $signature = 'install:setup';
    protected $description = 'Install setup';

    public function handle() {
		Artisan::call('menu:new packages settings bi-boxes');
		
		Artisan::call('template:widget', [
            'path' => 'setup::includes.count',
            'title' => 'count packages updates',
            'col' => 3,
            'permission' => 'packages.index'
        ]);
    }
}
