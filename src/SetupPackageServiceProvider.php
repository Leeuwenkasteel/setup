<?php

namespace Leeuwenkasteel\Setup;

use Illuminate\Support\ServiceProvider;
use Leeuwenkasteel\Setup\Console\Commands\ShowOptionalPackages;
use Leeuwenkasteel\Setup\Livewire\PackageManager;
use Livewire;

class SetupPackageServiceProvider extends ServiceProvider{
  public function register(): void{
    //
  }

  public function boot(): void{
<<<<<<< HEAD
	  $this->loadViewsFrom(__DIR__.'/../resources/views', 'setup');
		$this->loadRoutesFrom(__DIR__.'/../routes/web.php');
		
		Livewire::component('setup::package', PackageManager::class);
		$this->mergeConfigFrom(__DIR__.'/../config/setup.php', 'config-setup');
	
=======
>>>>>>> d8217ff83b6e5707ceaa3f5e7a07d58ded025151
	$this->loadOptionalPackages();
	if ($this->app->runningInConsole()) {
      $this->commands([
          ShowOptionalPackages::class
      ]);
    }
  }
  
  private function loadOptionalPackages()
    {
        $packages = [
            'leeuwenkasteel\\analytics\\AnalyticsServiceProvider',
            'leeuwenkasteel\\auth\\AuthServiceProvider',
            'leeuwenkasteel\\contact\\ContactServiceProvider',
            'leeuwenkasteel\\domains\\DomainsServiceProvider',
<<<<<<< HEAD
            'leeuwenkasteel\\languages\\LanguagesServiceProvider',
            'leeuwenkasteel\\media\\MediaServiceProvider',
=======
		'leeuwenkasteel\\domains\\ErrorLoggerServiceProvider',
            'leeuwenkasteel\\languages\\LanguagesServiceProvider',
            'leeuwenkasteel\\media\\MediaServiceProvider',
		'leeuwenkasteel\\domains\\MenuServiceProvider',
>>>>>>> d8217ff83b6e5707ceaa3f5e7a07d58ded025151
            'leeuwenkasteel\\schema\\SchemaServiceProvider',
            'leeuwenkasteel\\seo\\SeoServiceProvider',
            'leeuwenkasteel\\scholen\\ScholenServiceProvider',
            'leeuwenkasteel\\templates\\TemplatesServiceProvider',
<<<<<<< HEAD
=======
		'leeuwenkasteel\\domains\\UnderConstuctionServiceProvider',
>>>>>>> d8217ff83b6e5707ceaa3f5e7a07d58ded025151
            'leeuwenkasteel\\webshop\\WebshopServiceProvider'
        ];

        foreach ($packages as $provider) {
            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }
<<<<<<< HEAD
}
=======
}
>>>>>>> d8217ff83b6e5707ceaa3f5e7a07d58ded025151
