<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - TDM System</title>
    
    {{-- Include Premium Theme & Tailwind --}}
    <x-premium-theme />

    <style>
        /* Custom Scrollbar for Sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>
</head>
<body class="bg-[#f8fafc] font-sans antialiased text-gray-900 selection:bg-amber-100 selection:text-amber-900">

    {{-- SIDEBAR --}}
    <aside class="fixed top-0 right-0 h-screen w-72 bg-white/80 backdrop-blur-2xl border-l border-gray-100/50 z-50 flex flex-col transition-all hidden lg:flex shadow-[0_0_50px_-10px_rgba(0,0,0,0.05)]">
        
        {{-- Logo Section --}}
        <div class="h-28 flex items-center px-8 border-b border-gray-50">
            <div class="flex items-center gap-4 group cursor-pointer">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-amber-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-200/50 transform group-hover:rotate-6 transition-all duration-300">
                    <i data-lucide="zap" class="w-6 h-6 fill-current"></i>
                </div>
                <div>
                    <h1 class="font-black text-2xl text-gray-900 tracking-tight leading-none group-hover:text-amber-600 transition-colors">تقنية</h1>
                    <span class="text-[0.65rem] text-gray-400 font-bold tracking-[0.2em] uppercase mt-1 block">Distribution Sys</span>
                </div>
            </div>
        </div>

        {{-- Navigation Links --}}
        <nav class="flex-1 overflow-y-auto sidebar-scroll py-8 px-5 space-y-2">
            
            {{-- Dashboard --}}
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->is('dashboard') ? 'bg-amber-50 text-amber-700 shadow-sm ring-1 ring-amber-100' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <i data-lucide="layout-dashboard" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->is('dashboard') ? 'text-amber-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                <span>لوحة التحكم</span>
                @if(request()->is('dashboard'))
                    <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500"></div>
                @endif
            </a>

            {{-- Order Management --}}
            <a href="{{ route('marketer.requests.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('marketer.requests.*') ? 'bg-amber-50 text-amber-700 shadow-sm ring-1 ring-amber-100' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <i data-lucide="package" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('marketer.requests.*') ? 'text-amber-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                <span>إدارة الطلبات</span>
                @if(request()->routeIs('marketer.requests.*'))
                    <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500"></div>
                @endif
            </a>

            {{-- Real Inventory --}}
            @if(Route::has('marketer.stock.index'))
            <a href="{{ route('marketer.stock.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('marketer.stock.*') ? 'bg-amber-50 text-amber-700 shadow-sm ring-1 ring-amber-100' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900' }}">
                <i data-lucide="shopping-cart" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('marketer.stock.*') ? 'text-amber-600' : 'text-gray-400 group-hover:text-gray-600' }}"></i>
                <span>مخزوني الفعلي</span>
                @if(request()->routeIs('marketer.stock.*'))
                    <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500"></div>
                @endif
            </a>
            @endif

            {{-- Divider --}}
            <div class="pt-6 pb-2 px-5">
                <div class="h-px bg-gradient-to-r from-transparent via-gray-100 to-transparent"></div>
            </div>

            {{-- Placeholder Links --}}
            <div class="px-5 text-[0.65rem] font-black text-gray-300 uppercase tracking-widest mb-1">أخرى</div>
            <a href="#" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold text-gray-400 transition-all opacity-60 cursor-not-allowed hover:bg-gray-50">
                <i data-lucide="settings" class="w-[1.35rem] h-[1.35rem]"></i>
                <span>الإعدادات</span>
            </a>
           
        </nav>

        {{-- Footer Card --}}
        <div class="p-6">
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-3xl p-6 border border-gray-100 text-center relative overflow-hidden group hover:shadow-lg hover:shadow-amber-100/50 transition-all duration-500">
                <div class="absolute top-0 right-0 w-24 h-24 bg-amber-400/10 rounded-full filter blur-2xl opacity-50 -translate-y-1/2 translate-x-1/2 transition-transform group-hover:scale-125"></div>
                
                <h4 class="text-gray-900 font-black text-sm tracking-widest uppercase mb-1">Marketer Panel</h4>
                <p class="text-[0.65rem] text-gray-400 font-bold">Taqnia Distribution 2026 ©</p>
                <div class="mt-3 text-[0.6rem] bg-white border border-gray-100 text-gray-400 py-1 px-3 rounded-full inline-flex items-center gap-1 shadow-sm">
                    <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                    Online v2.5.0
                </div>
            </div>
        </div>
    </aside>

    {{-- MAIN CONTENT WRAPPER --}}
    <div class="lg:mr-72 min-h-screen flex flex-col transition-all bg-[#f8fafc]">
        
        {{-- NAVBAR --}}
        <header class="h-24 px-8 flex items-center justify-between sticky top-0 z-40 bg-white/80 backdrop-blur-xl border-b border-gray-100/50 transition-all">
            
            {{-- Right: Mobile Menu & Search --}}
            <div class="flex items-center gap-4">
                <button class="lg:hidden p-2.5 text-gray-600 bg-white rounded-xl shadow-sm border border-gray-100 hover:bg-gray-50 transition-colors">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>

            {{-- Left: User Profile & Actions --}}
            <div class="flex items-center gap-6">
                
                {{-- Actions --}}
                <div class="flex items-center gap-3">
                    <button class="w-11 h-11 bg-white rounded-full flex items-center justify-center text-amber-500 hover:bg-amber-50 hover:scale-105 shadow-sm border border-gray-100 transition-all duration-300">
                        <i data-lucide="sun" class="w-5 h-5 fill-current opacity-20"></i>
                        <i data-lucide="sun" class="w-5 h-5 absolute"></i>
                    </button>
                    <button class="w-11 h-11 bg-white rounded-full flex items-center justify-center text-gray-400 hover:text-amber-600 hover:bg-amber-50 hover:scale-105 shadow-sm border border-gray-100 transition-all duration-300 relative group">
                        <i data-lucide="bell" class="w-5 h-5 group-hover:animate-swing"></i>
                        <span class="absolute top-2.5 right-2.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>
                </div>

                <div class="w-px h-8 bg-gray-200/60"></div>

                {{-- User Info --}}
                <div class="flex items-center gap-3 pl-2 p-1.5 pr-4 rounded-full hover:bg-white hover:shadow-sm hover:ring-1 hover:ring-gray-100 transition-all cursor-pointer group">
                    <div class="hidden md:block text-left">
                        <div class="text-sm font-black text-gray-800 group-hover:text-amber-600 transition-colors">
                            {{ Auth::user()->full_name ?? 'المسوق' }}
                        </div>
                        <div class="text-[0.65rem] text-gray-400 font-bold text-right uppercase tracking-wider">Top Seller</div>
                    </div>
                    <div class="w-10 h-10 bg-gradient-to-br from-amber-100 to-orange-100 rounded-full flex items-center justify-center text-amber-600 border-2 border-white shadow-md ring-1 ring-gray-100 group-hover:ring-amber-100 transition-all">
                        <i data-lucide="user" class="w-5 h-5"></i>
                    </div>
                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors"></i>
                </div>

            </div>
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center transform transition-all hover:scale-[1.01] shadow-sm">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600 ml-4 shrink-0">
                        <i data-lucide="check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm">تمت العملية بنجاح!</h4>
                        <p class="text-xs opacity-80 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl flex items-center transform transition-all hover:scale-[1.01] shadow-sm">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center text-red-600 ml-4 shrink-0">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    </div>
                     <div>
                        <h4 class="font-bold text-sm">تنبيه!</h4>
                        <p class="text-xs opacity-80 mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

    </div>

    {{-- Mobile Overlay --}}
    <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity" id="mobile-overlay"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add subtle swing animation to Tailwind config effectively
        tailwind.config.theme.extend.keyframes.swing = {
            '0%, 100%': { transform: 'rotate(0deg)' },
            '20%': { transform: 'rotate(15deg)' },
            '40%': { transform: 'rotate(-10deg)' },
            '60%': { transform: 'rotate(5deg)' },
            '80%': { transform: 'rotate(-5deg)' },
        }
        tailwind.config.theme.extend.animation.swing = 'swing 1s ease-in-out infinite';
    </script>
    @stack('scripts')
</body>
</html>
