<?php

namespace Leeuwenkasteel\Setup;

use Illuminate\Support\ServiceProvider;
use Leeuwenkasteel\Setup\Console\Commands\ShowOptionalPackages;

class SetupPackageServiceProvider extends ServiceProvider{
  public function register(): void{
    //
  }

  public function boot(): void{

	if ($this->app->runningInConsole()) {
      $this->commands([
          ShowOptionalPackages::class
      ]);
    }
  }
}