<?php

namespace Leeuwenkasteel\Setup\Console\Commands;

use Illuminate\Console\Command;

class ShowOptionalPackages extends Command
{
    protected $signature = 'github-private:optional-packages';
    protected $description = 'Toon optionele private packages die kunnen worden geïnstalleerd';

    public function handle()
    {
        $this->info("Beschikbare optionele private packages:");
        $modules = [
            "analytics",
            "auth",
            "contact",
            "domains",
			"error-logger",
            "languages",
            "media",
			"menu",
            "schema",
            "seo",
            "scholen",
            "templates",
			"under-construction",
            "webshop"
        ];

        foreach ($modules as $module) {
            $this->line("- leeuwenkasteel/$module (composer require leeuwenkasteel/$module)");
        }
    }
}