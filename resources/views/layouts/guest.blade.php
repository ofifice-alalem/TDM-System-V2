<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

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
                font-family: 'Inter', sans-serif;
                background-color: var(--bg-color);
                color: #ffffff;
                margin: 0;
                overflow: hidden;
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
                border: 1px solid rgba(255, 255, 255, 0.05);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                border-radius: 2rem;
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

        <div class="min-h-screen flex flex-col sm:justify-center items-center p-4 relative z-10">
            <div class="w-full sm:max-w-[420px] px-8 py-10 glass-card">
                <div class="sparkle-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white opacity-80">
                        <path d="M12 3l1.912 5.813a2 2 0 001.275 1.275L21 12l-5.813 1.912a2 2 0 00-1.275 1.275L12 21l-1.912-5.813a2 2 0 00-1.275-1.275L3 12l5.813-1.912a2 2 0 001.275-1.275L12 3z"></path>
                    </svg>
                </div>
                
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
