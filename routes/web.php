<?php

use Illuminate\Support\Facades\Route;

Route::prefix('ollama')->group(function () {
    // Home
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Dashboard
    Route::get('dashboard', function () {        
        return view('ollama::dashboard');
    })->name('dashboard');

    // Database
    Route::prefix('database')->group(function () {
        // Tables
        Route::get('query-builder', function () {
            return view('ollama::database.query-builder');
        })->name('query-builder');
    });
    
    // Files
    Route::prefix('files')->group(function () {
        // Models
        Route::get('models', function () {
            $ollama = app('ollama.file');

            $result = $ollama->generateFile('model', 'Product');

            return $result;
        });

        // Controllers
        Route::get('controllers', function () {
            
        });

        // Migrations
        Route::get('migrations', function () {
            
        });

        // Views
        Route::get('views', function () {
            
        });

        // Routes
        Route::get('routes', function () {
            
        });

        // Tests
        Route::get('tests', function () {
            
        });

        // Seeds
        Route::get('seeds', function () {
            
        });

        // Factories
        Route::get('factories', function () {
            
        });

        // Providers
        Route::get('providers', function () {
            
        });

        // Config
        Route::get('config', function () {
            
        });
    });

});


