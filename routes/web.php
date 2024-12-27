<?php

use Illuminate\Support\Facades\Route;

Route::get('/ollama', function () {
    $ollama = app('ollama');
    // $result = 'package working';
    $result = $ollama->executeQuery('Borra la tabla mascotas.');
    return $result;
});
