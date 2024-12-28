@extends('ollama::layout')

@section('content')
<div class="flex justify-center gap-4">
    <!-- Column for Current Tables on Database -->
    <div class="w-1/2 p-4 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center justify-between">
            Tables on Database
        </h3>
        <ul class="mt-2">
            @php
                $tables = DB::select('SHOW TABLES');
            @endphp
            @foreach($tables as $table)
                <li class="text-gray-600 dark:text-gray-400">{{ $table->Tables_in_ollamaintegration }}</li>
            @endforeach
        </ul>
    </div>
    <div class="w-1/2 p-4 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center justify-between">
            Columns on Users
        </h3>
        <ul class="mt-2">
            
            
            @php
                $columns = DB::select('SHOW COLUMNS FROM users');
            @endphp
            @foreach($columns as $column)
                <li class="text-gray-600 dark:text-gray-400">{{ $column->Field }}</li>
            @endforeach
        </ul>
    </div>
</div>
<div class="flex mt-4 justify-center gap-4">
    <!-- Column for Chatbot like ChatGPT -->
    <div class="w-full p-4 bg-white dark:bg-gray-800 shadow-lg rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white flex items-center justify-between">
            Query Builder AI
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
            </svg>
        </h3>
        <div class="mt-2">
            <textarea class="w-full p-2 text-gray-600 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md" placeholder="Ask your database query..."></textarea>
            <button class="mt-2 w-full p-2 text-white bg-blue-500 hover:bg-blue-600 rounded-md">Ask</button>
        </div>
    </div>
</div>
@endsection