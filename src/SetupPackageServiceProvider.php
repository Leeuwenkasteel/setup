<?php

namespace Leeuwenkasteel\Setup;

use Illuminate\Support\ServiceProvider;
use Leeuwenkasteel\Setup\Console\Commands\InstallCommand;
use Leeuwenkasteel\Setup\Livewire\PackageManager;
use Leeuwenkasteel\Setup\Livewire\CountPackages;
use Leeuwenkasteel\Setup\View\Components\LayoutComponent;
use Illuminate\Support\Facades\Blade;
use Livewire;

class SetupPackageServiceProvider extends ServiceProvider{
  public function register(): void{
    //
  }

  public function boot(): void{
	  $this->loadViewsFrom(__DIR__.'/../resources/views', 'setup');
		$this->loadRoutesFrom(__DIR__.'/../routes/web.php');
		
		Livewire::component('setup::package', PackageManager::class);
		Livewire::component('setup::count', CountPackages::class);
		
		$this->mergeConfigFrom(__DIR__.'/../config/setup.php', 'config-setup');
		Blade::component('setup::layout', LayoutComponent::class);
	
	$this->loadOptionalPackages();
	if ($this->app->runningInConsole()) {
      $this->commands([
		  InstallCommand::class
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
            'leeuwenkasteel\\languages\\LanguagesServiceProvider',
            'leeuwenkasteel\\media\\MediaServiceProvider',
		'leeuwenkasteel\\domains\\ErrorLoggerServiceProvider',
            'leeuwenkasteel\\languages\\LanguagesServiceProvider',
            'leeuwenkasteel\\media\\MediaServiceProvider',
		'leeuwenkasteel\\domains\\MenuServiceProvider',
            'leeuwenkasteel\\schema\\SchemaServiceProvider',
            'leeuwenkasteel\\seo\\SeoServiceProvider',
            'leeuwenkasteel\\scholen\\ScholenServiceProvider',
            'leeuwenkasteel\\templates\\TemplatesServiceProvider',
		'leeuwenkasteel\\domains\\UnderConstuctionServiceProvider',
            'leeuwenkasteel\\webshop\\WebshopServiceProvider'
        ];

        foreach ($packages as $provider) {
            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }
}


