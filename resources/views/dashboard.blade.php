@extends('ollama::layout')

@php
    $databaseColumns = ['users', 'posts', 'comments'];
    $models = ['User', 'Post', 'Comment'];
    $controllers = ['UserController', 'AuthController'];
@endphp

@section('content')
<div class="flex flex-wrap justify-center gap-4">
    <!-- Widget for Database Columns -->
    <div class="w-64 p-4 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center justify-between">
            Database
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
        </h3>
        <ul class="mt-2">
            @foreach($databaseColumns as $column)
                <li class="text-gray-600 dark:text-gray-400">{{ $column }}</li>
            @endforeach
        </ul>
    </div>

    <!-- Widget for Models -->
    <div class="w-64 p-4 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center justify-between">
            Models
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
        </h3>
        <ul class="mt-2">
            @foreach($models as $model)
                <li class="text-gray-600 dark:text-gray-400">{{ $model }}</li>
            @endforeach
        </ul>
    </div>

    <!-- Widget for Controllers -->
    <div class="w-64 p-4 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center justify-between">
        Controllers
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
        </h3>
        <ul class="mt-2">
            @foreach($controllers as $controller)
                <li class="text-gray-600 dark:text-gray-400">{{ $controller }}</li>
            @endforeach
        </ul>
    </div>

    <!-- Add more widgets as needed -->
</div>
@endsection