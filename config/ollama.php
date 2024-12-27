<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ollama API Endpoint
    |--------------------------------------------------------------------------
    |
    | La URL base para la API de Ollama
    |
    */
    'endpoint' => env('OLLAMA_ENDPOINT', 'http://localhost:11434/api'),

    /*
    |--------------------------------------------------------------------------
    | Modelo por defecto
    |--------------------------------------------------------------------------
    |
    | El modelo de Ollama a utilizar por defecto
    |
    */
    'model_name' => env('OLLAMA_MODEL', 'codellama'),

    /*
    |--------------------------------------------------------------------------
    | Directorios a escanear
    |--------------------------------------------------------------------------
    |
    | Lista de directorios que el package analizará para contexto
    |
    */
    'scan_directories' => [
        'app',
        'database/migrations',
        'routes',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de archivo soportados
    |--------------------------------------------------------------------------
    |
    | Extensiones de archivo que serán analizadas
    |
    */
    'supported_extensions' => [
        'php',
        'json',
    ],
];