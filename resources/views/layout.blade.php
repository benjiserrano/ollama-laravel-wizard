<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ollama Laravel Wizard Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>
<body class="bg-gray-100 dark:bg-gray-900">
    <!-- Navbar -->
    <nav class="bg-white dark:bg-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800 dark:text-white">Ollama Laravel Wizard</span>
                </div>
                <!-- Dark mode toggle -->
                <div class="flex items-center">
                    <button onclick="toggleDarkMode()" class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white">
                        <!-- Sun icon -->
                        <svg class="w-6 h-6 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Moon icon -->
                        <svg class="w-6 h-6 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Sidebar + Content Layout -->
    <div class="flex">
        
        <!-- Sidebar -->
        <aside class="w-64 bg-white dark:bg-gray-800 shadow-lg h-screen fixed lg:block hidden">
            <nav class="mt-5">
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                    Dashboard
                </a>
                <!-- File Generation Section -->
                <div class="px-4 py-2">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase">File Generation</h3>
                    <a href="#" class="block pl-2 py-1 text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white">Models</a>
                    <a href="#" class="block pl-2 py-1 text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white">Controllers</a>
                    <a href="#" class="block pl-2 py-1 text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white">Migrations</a>
                    <a href="#" class="block pl-2 py-1 text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white">Views</a>
                    <a href="#" class="block pl-2 py-1 text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white">Tests</a>
                </div>
                <!-- Database Tools -->
                <div class="px-4 py-2">
                    <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase">Database Tools</h3>
                    <a href="#" class="block pl-2 py-1 text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white">Schema Viewer</a>
                    <a href="{{ route('query-builder') }}" class="block pl-2 py-1 text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white">Query Generator</a>
                </div>
            </nav>
        </aside>

        <!-- Mobile menu button -->
        <div class="lg:hidden fixed bottom-4 right-4 z-50">
            <button onclick="document.querySelector('aside').classList.toggle('hidden')" class="p-2 rounded-full bg-blue-600 text-white shadow-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <!-- Main Content -->
        <main class="lg:ml-64 flex-1 p-8">
            @yield('content')
        </main>
    </div>

    <script>
        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        }
    </script>
</body>
</html>
