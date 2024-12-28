<?php

namespace Bjserranoweb\OllamaLaravelWizard;

use Illuminate\Support\ServiceProvider;
use Bjserranoweb\OllamaLaravelWizard\Services\OllamaSqlService;
use Bjserranoweb\OllamaLaravelWizard\Services\OllamaFileService;

class LaravelOllamaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/ollama.php', 'ollama');
        
        $this->app->singleton('ollama.file', function ($app) {
            return new OllamaFileService(config('ollama'));
        });

        $this->app->singleton('ollama.sql', function ($app) {
            return new OllamaSqlService(config('ollama'));
        });
    }
    
    public function boot()
    {
        // Routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        // Views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'ollama');

        // Publishes
        $this->publishes([
            __DIR__.'/../config/ollama.php' => config_path('ollama.php'),
        ], 'config');
        
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/ollama'),
        ], 'views');
    }
}
