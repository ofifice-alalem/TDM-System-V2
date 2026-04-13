<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - TDM System</title>
    <x-premium-theme />
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
</head>
<body class="bg-[#f1f5f9] dark:bg-[#0b1121] font-sans antialiased text-gray-900 dark:text-gray-100 transition-colors duration-300">

    {{-- NAVBAR --}}
    <header class="h-20 px-8 flex items-center justify-between sticky top-0 z-40 bg-white/90 dark:bg-[#0b1121]/90 backdrop-blur-md border-b border-gray-200 dark:border-[#2a354c] shadow-sm">
        <div class="flex items-center gap-3">
            <img src="/logo.png" alt="Logo" class="h-9 object-contain">
            <span class="text-xs font-black bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2.5 py-1 rounded-lg">Super Admin</span>
        </div>
        <div class="flex items-center gap-4">
            <button id="theme-toggle" class="w-10 h-10 bg-white dark:bg-dark-card rounded-full flex items-center justify-center text-amber-500 dark:text-accent-400 hover:bg-amber-50 dark:hover:bg-accent-500/10 shadow-sm border border-gray-200 dark:border-dark-border transition-all">
                <i data-lucide="sun" class="w-5 h-5 fill-current dark:hidden"></i>
                <i data-lucide="moon" class="w-5 h-5 hidden dark:block fill-current"></i>
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="flex items-center gap-2 px-4 py-2 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-600 dark:text-gray-300 rounded-xl font-bold text-sm hover:bg-gray-50 dark:hover:bg-dark-bg transition-all shadow-sm">
                    <i data-lucide="log-out" class="w-4 h-4"></i> خروج
                </button>
            </form>
        </div>
    </header>

    {{-- CONTENT --}}
    <main class="max-w-4xl mx-auto px-4 py-8 animate-fade-in">
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 rounded-2xl flex items-center gap-3 shadow-sm">
                <i data-lucide="check-circle" class="w-5 h-5 shrink-0"></i>
                <p class="font-bold text-sm">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-500/30 text-red-700 dark:text-red-400 rounded-2xl flex items-center gap-3 shadow-sm">
                <i data-lucide="alert-circle" class="w-5 h-5 shrink-0"></i>
                <p class="font-bold text-sm">{{ session('error') }}</p>
            </div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;

        const themeToggleBtn = document.getElementById('theme-toggle');
        themeToggleBtn?.addEventListener('click', () => {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.theme = isDark ? 'dark' : 'light';
        });
    </script>
    @stack('scripts')
</body>
</html>
