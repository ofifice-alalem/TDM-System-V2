<!DOCTYPE html>
<html lang="ar" dir="rtl" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Cairo Font -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --bg-color: #0d0d0d;
                --card-bg: rgba(23, 23, 23, 0.6);
                --input-bg: rgba(40, 40, 40, 0.4);
                --primary-glow: radial-gradient(circle at center, rgba(60, 60, 60, 0.15) 0%, transparent 70%);
            }
            body {
                font-family: 'Cairo', sans-serif;
                background-color: var(--bg-color);
                color: #ffffff;
                margin: 0;
            }
            .stars-container {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: 
                    radial-gradient(1px 1px at 20px 30px, #eee, rgba(0,0,0,0)),
                    radial-gradient(1px 1px at 40px 70px, #fff, rgba(0,0,0,0)),
                    radial-gradient(1.5px 1.5px at 100px 150px, #ddd, rgba(0,0,0,0)),
                    radial-gradient(1px 1px at 150px 50px, #fff, rgba(0,0,0,0)),
                    radial-gradient(1px 1px at 250px 200px, #ccc, rgba(0,0,0,0));
                background-size: 300px 300px;
                background-repeat: repeat;
                opacity: 0.15;
                z-index: -2;
            }
            .main-glow {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                width: 100vw;
                height: 100vh;
                background: var(--primary-glow);
                z-index: -1;
            }
            .glass-card {
                background: var(--card-bg);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 2px solid rgba(249, 115, 22, 0.3);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 30px rgba(249, 115, 22, 0.2);
                border-radius: 2rem;
                animation: cardGlow 3s ease-in-out infinite;
            }
            @keyframes cardGlow {
                0%, 100% { 
                    border-color: rgba(249, 115, 22, 0.3);
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 30px rgba(249, 115, 22, 0.2);
                }
                50% { 
                    border-color: rgba(249, 115, 22, 0.6);
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 50px rgba(249, 115, 22, 0.4);
                }
            }
            .organic-line {
                position: fixed;
                top: 50%;
                width: 50%;
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
                z-index: -1;
            }
            .line-left { left: 0; transform: translateY(-50%) perspective(100px) rotateY(10deg); }
            .line-right { right: 0; transform: translateY(-50%) perspective(100px) rotateY(-10deg); }
            
            .sparkle-icon {
                width: 48px;
                height: 48px;
                background: rgba(255, 255, 255, 0.03);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1.5rem;
                border: 1px solid rgba(255, 255, 255, 0.05);
            }
        </style>
    </head>
    <body class="antialiased">
        <div class="stars-container"></div>
        <div class="main-glow"></div>
        <div class="organic-line line-left"></div>
        <div class="organic-line line-right"></div>

        <div class="min-h-screen flex flex-col justify-center items-center p-4 sm:p-6 relative z-10">
            <!-- Theme Toggle Button -->
            <button id="theme-toggle" class="fixed top-6 left-6 w-10 h-10 bg-gray-800/50 hover:bg-gray-700/50 border border-gray-700 rounded-lg flex items-center justify-center transition-all z-20">
                <svg id="moon-icon" class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                </svg>
                <svg id="sun-icon" class="w-5 h-5 text-gray-300 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </button>

            <div class="w-full px-6 sm:px-8 py-6 sm:py-8 glass-card" style="max-width: min(450px, 90%);">
                <div class="sparkle-icon mb-4 sm:mb-6" style="width: 240px; height: 80px; border-radius: 30px;">
                    <img src="/logo.png" alt="Logo" class="object-contain" style="width: 180px; scale: 1.05;">
                </div>
                
                {{ $slot }}
            </div>
        </div>

        <script>
            const themeToggle = document.getElementById('theme-toggle');
            const html = document.documentElement;
            const moonIcon = document.getElementById('moon-icon');
            const sunIcon = document.getElementById('sun-icon');
            
            themeToggle.addEventListener('click', function() {
                html.classList.toggle('dark');
                moonIcon.classList.toggle('hidden');
                sunIcon.classList.toggle('hidden');
                localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
            });
        </script>
    </body>
</html>
