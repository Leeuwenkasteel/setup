<?php
namespace Leeuwenkasteel\Setup\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class InstallPackages extends Command {
    protected $signature = 'setup:install {package}';
    protected $description = 'Installeer een specifiek pakket';

    public function handle() {
		dd(session('github_token'));
        $package = $this->argument('package');
        $token = env('GITHUB_TOKEN');

        // Ophalen laatste versie
        $response = Http::withToken($token)->get("https://api.github.com/repos/your-org/$package/releases/latest");

        if ($response->failed()) {
            $this->error("Fout bij ophalen versie van $package");
            return;
        }

        $version = $response->json()['tag_name'];

        // Installeren met Composer
        $command = "composer require your-org/$package:$version --prefer-dist";
        shell_exec($command);

        $this->info("$package succesvol geïnstalleerd!");
    }
}
