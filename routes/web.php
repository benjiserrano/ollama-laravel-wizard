<?php

use Illuminate\Support\Facades\Route;

Route::get('/ollama', function () {
    $ollama = app('ollama');
    // $result = 'package working';
    $result = $ollama->executeQuery('Create a table called "products" with the columns you need.');
    return $result;
});
