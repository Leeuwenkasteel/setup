<?php

namespace Leeuwenkasteel\Setup;

use Illuminate\Support\ServiceProvider;
use Leeuwenkasteel\Setup\Console\Commands\ShowOptionalPackages;

class SetupPackageServiceProvider extends ServiceProvider{
  public function register(): void{
    //
  }

  public function boot(): void{
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
