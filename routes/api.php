<?php

use Illuminate\Support\Facades\Route;

Route::prefix('olw-api')->group(function () {

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

        // Tests
        Route::get('tests', function () {
            
        });
    });

    // SQL
    Route::prefix('sql')->group(function () {
        
        // Query
        Route::get('query', function () {
            $ollama = app('ollama.sql');

            $result = $ollama->executeQuery('Muestrame las columnas de la tabla "users".');

            return $result;
        });
        
    });    
});


