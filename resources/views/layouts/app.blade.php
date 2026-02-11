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
    <script>
        // Immediately check theme to prevent white flash
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-[#f1f5f9] dark:bg-[#0b1121] font-sans antialiased text-gray-900 dark:text-gray-100 transition-colors duration-300 overflow-x-hidden">

    {{-- SIDEBAR --}}
    <aside id="sidebar" class="fixed top-0 right-0 h-screen w-80 lg:w-72 bg-white dark:bg-[#151f32] border-l border-gray-100 dark:border-[#2a354c] z-50 flex flex-col transition-transform duration-300 translate-x-full lg:translate-x-0 shadow-sm dark:shadow-none">
        
        {{-- Logo Section --}}
        <div class="h-28 flex items-center justify-between px-8 border-b border-gray-50 dark:border-dark-border">
            <div class="flex items-center gap-4 group cursor-pointer">
                <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-amber-600 dark:from-accent-500 dark:to-accent-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-amber-200/50 dark:shadow-accent-500/20 transform group-hover:rotate-6 transition-all duration-300">
                    <i data-lucide="zap" class="w-6 h-6 fill-current"></i>
                </div>
                <div>
                    <h1 class="font-black text-2xl text-gray-900 dark:text-white tracking-tight leading-none group-hover:text-amber-600 dark:group-hover:text-accent-400 transition-colors">تقنية</h1>
                    <span class="text-[0.65rem] text-gray-400 dark:text-dark-muted font-bold tracking-[0.2em] uppercase mt-1 block">Distribution Sys</span>
                </div>
            </div>
            <button id="sidebar-close-btn" class="lg:hidden w-10 h-10 bg-gray-100 dark:bg-dark-bg rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-dark-border transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- Navigation Links --}}
        <nav class="flex-1 overflow-y-auto sidebar-scroll py-8 px-5 space-y-2">
            
            {{-- Dashboard --}}
            <a href="{{ url('/dashboard') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->is('dashboard') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->is('dashboard') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                <span>لوحة التحكم</span>
                @if(request()->is('dashboard'))
                    <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                @endif
            </a>

            @if(request()->routeIs('marketer.*'))
                @if(Route::has('marketer.stock.index'))
                <a href="{{ route('marketer.stock.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('marketer.stock.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="shopping-cart" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('marketer.stock.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>مخزوني الفعلي</span>
                    @if(request()->routeIs('marketer.stock.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>
                @endif

                {{-- Divider --}}
                <div class="pt-6 pb-2 px-5">
                    <div class="h-px bg-gradient-to-r from-transparent via-gray-100 dark:via-gray-700 to-transparent"></div>
                </div>
            @endif

            @if(request()->routeIs('marketer.*'))
                {{-- Section Title --}}
                <div class="px-5 text-[0.65rem] font-black text-gray-300 dark:text-gray-600 uppercase tracking-widest mb-1">العمليات الأساسية</div>
                {{-- Marketer Links --}}
                <a href="{{ route('marketer.requests.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('marketer.requests.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="package" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('marketer.requests.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>طلبات البضاعة</span>
                    @if(request()->routeIs('marketer.requests.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('marketer.returns.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('marketer.returns.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="package-x" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('marketer.returns.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>إرجاع البضاعة</span>
                    @if(request()->routeIs('marketer.returns.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('marketer.sales.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('marketer.sales.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="shopping-cart" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('marketer.sales.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>فواتير البيع</span>
                    @if(request()->routeIs('marketer.sales.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('marketer.sales-returns.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('marketer.sales-returns.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="undo-2" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('marketer.sales-returns.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>إرجاع من المتاجر</span>
                    @if(request()->routeIs('marketer.sales-returns.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('marketer.payments.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('marketer.payments.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="banknote" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('marketer.payments.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>إيصالات القبض</span>
                    @if(request()->routeIs('marketer.payments.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('marketer.commissions.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('marketer.commissions.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="wallet" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('marketer.commissions.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>أرباحي</span>
                    @if(request()->routeIs('marketer.commissions.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('marketer.withdrawals.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('marketer.withdrawals.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="hand-coins" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('marketer.withdrawals.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>طلبات السحب</span>
                    @if(request()->routeIs('marketer.withdrawals.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                {{-- Divider --}}
                <div class="pt-6 pb-2 px-5">
                    <div class="h-px bg-gradient-to-r from-transparent via-gray-100 dark:via-gray-700 to-transparent"></div>
                </div>

                {{-- Section Title --}}
                <div class="px-5 text-[0.65rem] font-black text-gray-300 dark:text-gray-600 uppercase tracking-widest mb-1">الخصومات والعروض</div>

                <a href="{{ route('marketer.discounts.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('marketer.discounts.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="percent" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('marketer.discounts.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>خصومات الفواتير</span>
                    @if(request()->routeIs('marketer.discounts.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                @if(Route::has('marketer.promotions.index'))
                <a href="{{ route('marketer.promotions.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('marketer.promotions.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="gift" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('marketer.promotions.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>العروض الترويجية</span>
                    @if(request()->routeIs('marketer.promotions.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>
                @endif
            @endif

            @if(request()->routeIs('warehouse.*'))
                {{-- Warehouse Links --}}
                <a href="{{ route('warehouse.main-stock.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('warehouse.main-stock.*') && !request()->routeIs('warehouse.factory-invoices.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="warehouse" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('warehouse.main-stock.*') && !request()->routeIs('warehouse.factory-invoices.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>المخزن الرئيسي</span>
                    @if(request()->routeIs('warehouse.main-stock.*') && !request()->routeIs('warehouse.factory-invoices.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('warehouse.factory-invoices.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('warehouse.factory-invoices.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="package-plus" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('warehouse.factory-invoices.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>فواتير المصنع</span>
                    @if(request()->routeIs('warehouse.factory-invoices.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('warehouse.requests.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('warehouse.requests.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="package" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('warehouse.requests.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>طلبات المسوقين</span>
                    @if(request()->routeIs('warehouse.requests.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('warehouse.returns.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('warehouse.returns.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="package-x" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('warehouse.returns.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>إرجاعات المسوقين</span>
                    @if(request()->routeIs('warehouse.returns.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('warehouse.sales.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('warehouse.sales.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="shopping-cart" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('warehouse.sales.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>فواتير البيع</span>
                    @if(request()->routeIs('warehouse.sales.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('warehouse.payments.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('warehouse.payments.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="banknote" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('warehouse.payments.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>إيصالات القبض</span>
                    @if(request()->routeIs('warehouse.payments.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('warehouse.sales-returns.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('warehouse.sales-returns.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="undo-2" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('warehouse.sales-returns.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>إرجاع من المتاجر</span>
                    @if(request()->routeIs('warehouse.sales-returns.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>
            @endif

            {{-- Divider --}}
            <div class="pt-6 pb-2 px-5">
                <div class="h-px bg-gradient-to-r from-transparent via-gray-100 to-transparent"></div>
            </div>

            @if(request()->routeIs('admin.*'))
                {{-- Admin Links --}}
                <a href="{{ route('admin.main-stock.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('admin.main-stock.*') && !request()->routeIs('admin.factory-invoices.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="warehouse" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('admin.main-stock.*') && !request()->routeIs('admin.factory-invoices.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>المخزن الرئيسي</span>
                    @if(request()->routeIs('admin.main-stock.*') && !request()->routeIs('admin.factory-invoices.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('admin.factory-invoices.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('admin.factory-invoices.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="package-plus" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('admin.factory-invoices.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>فواتير المصنع</span>
                    @if(request()->routeIs('admin.factory-invoices.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('admin.withdrawals.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('admin.withdrawals.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="hand-coins" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('admin.withdrawals.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>طلبات السحب</span>
                    @if(request()->routeIs('admin.withdrawals.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                <a href="{{ route('admin.discounts.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('admin.discounts.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="percent" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('admin.discounts.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>خصومات الفواتير</span>
                    @if(request()->routeIs('admin.discounts.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>

                @if(Route::has('admin.promotions.index'))
                <a href="{{ route('admin.promotions.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('admin.promotions.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                    <i data-lucide="gift" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('admin.promotions.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                    <span>العروض الترويجية</span>
                    @if(request()->routeIs('admin.promotions.*'))
                        <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                    @endif
                </a>
                @endif
            @endif

            {{-- Divider --}}
            <div class="pt-6 pb-2 px-5">
                <div class="h-px bg-gradient-to-r from-transparent via-gray-100 to-transparent"></div>
            </div>

            {{-- Placeholder Links --}}
            <div class="px-5 text-[0.65rem] font-black text-gray-300 uppercase tracking-widest mb-1">أخرى</div>
            
            {{-- Users Link - Admin only --}}
            @if(request()->routeIs('admin.*'))
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('admin.users.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                <i data-lucide="users" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('admin.users.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                <span>المستخدمين</span>
                @if(request()->routeIs('admin.users.*'))
                    <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                @endif
            </a>
            @endif
            
            {{-- Stores Link - Available for all users --}}
            <a href="{{ request()->routeIs('marketer.*') ? route('marketer.stores.index') : (request()->routeIs('warehouse.*') ? route('warehouse.stores.index') : route('admin.stores.index')) }}" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold transition-all duration-300 group {{ request()->routeIs('*.stores.*') ? 'bg-amber-50 dark:bg-accent-500/10 text-amber-700 dark:text-accent-400 shadow-sm ring-1 ring-amber-100 dark:ring-accent-500/20' : 'text-gray-500 dark:text-dark-muted hover:bg-gray-50 dark:hover:bg-dark-bg hover:text-gray-900 dark:hover:text-white' }}">
                <i data-lucide="store" class="w-[1.35rem] h-[1.35rem] transition-colors {{ request()->routeIs('*.stores.*') ? 'text-amber-600 dark:text-accent-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white' }}"></i>
                <span>المتاجر</span>
                @if(request()->routeIs('*.stores.*'))
                    <div class="mr-auto w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-accent-400 shadow-[0_0_10px_currentColor]"></div>
                @endif
            </a>
            
            <a href="#" class="flex items-center gap-4 px-5 py-4 rounded-2xl font-bold text-gray-400 transition-all opacity-60 cursor-not-allowed hover:bg-gray-50">
                <i data-lucide="settings" class="w-[1.35rem] h-[1.35rem]"></i>
                <span>الإعدادات</span>
            </a>
           
        </nav>

    </aside>

    {{-- MAIN CONTENT WRAPPER --}}
    <div class="lg:mr-72 min-h-screen flex flex-col transition-all bg-[#f1f5f9] dark:bg-[#0b1121]">
        
        {{-- NAVBAR --}}
        <header class="h-24 px-8 flex items-center justify-between sticky top-0 z-40 bg-white/90 dark:bg-[#0b1121]/90 backdrop-blur-md border-b border-gray-200 dark:border-[#2a354c] transition-all shadow-sm dark:shadow-none">
            
            {{-- Right: Mobile Menu & Search --}}
            <div class="flex items-center gap-4">
                <button id="mobile-menu-btn" class="lg:hidden p-2.5 text-gray-600 dark:text-gray-300 bg-white dark:bg-dark-card rounded-xl shadow-sm border border-gray-100 dark:border-dark-border hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                    <i data-lucide="menu" class="w-6 h-6"></i>
                </button>
            </div>

            {{-- Left: User Profile & Actions --}}
            <div class="flex items-center gap-6">
                
                {{-- Actions --}}
                <div class="flex items-center gap-3">
                    <button id="theme-toggle" class="w-11 h-11 bg-white dark:bg-dark-card rounded-full flex items-center justify-center text-amber-500 dark:text-accent-400 hover:bg-amber-50 dark:hover:bg-accent-500/10 hover:scale-105 shadow-sm hover:shadow-md border border-gray-200 dark:border-dark-border transition-all duration-300">
                        <i data-lucide="sun" class="w-5 h-5 fill-current opacity-100 dark:hidden"></i>
                        <i data-lucide="moon" class="w-5 h-5 hidden dark:block fill-current"></i>
                    </button>
                    <button class="w-11 h-11 bg-white dark:bg-dark-card rounded-full flex items-center justify-center text-gray-400 dark:text-gray-500 hover:text-amber-600 dark:hover:text-accent-400 hover:bg-amber-50 dark:hover:bg-accent-500/10 hover:scale-105 shadow-sm hover:shadow-md border border-gray-200 dark:border-dark-border transition-all duration-300 relative group">
                        <i data-lucide="bell" class="w-5 h-5 group-hover:animate-swing"></i>
                        <span class="absolute top-2.5 right-2.5 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white dark:border-dark-card"></span>
                    </button>
                </div>

                <div class="w-px h-8 bg-gray-200/60"></div>

                {{-- User Info --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-3 pl-2 p-1.5 pr-4 rounded-full hover:bg-gray-50 dark:hover:bg-dark-bg hover:shadow-md border border-transparent hover:border-gray-200 dark:hover:border-dark-border transition-all group">
                        <div class="hidden md:block text-left">
                            <div class="text-sm font-black text-gray-800 dark:text-gray-200 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition-colors">
                                {{ Auth::user()->full_name ?? 'المسوق' }}
                            </div>
                            <div class="text-[0.65rem] text-gray-400 font-bold text-right uppercase tracking-wider">Top Seller</div>
                        </div>
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-100 to-orange-100 dark:from-amber-900/40 dark:to-orange-900/40 rounded-full flex items-center justify-center text-amber-600 dark:text-amber-400 border-2 border-white dark:border-dark-card shadow-md ring-1 ring-gray-100 dark:ring-gray-700 group-hover:ring-amber-200 transition-all">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-300 group-hover:text-gray-500 transition-colors"></i>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition class="absolute left-0 mt-2 w-48 bg-white dark:bg-dark-card rounded-xl shadow-lg border border-gray-200 dark:border-dark-border py-2 z-50">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-right px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors flex items-center gap-2">
                                <i data-lucide="log-out" class="w-4 h-4"></i>
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        {{-- CONTENT --}}
        <main class="flex-1 px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 rounded-2xl flex items-center transform transition-all hover:scale-[1.01] shadow-sm">
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-500/20 rounded-full flex items-center justify-center text-emerald-600 dark:text-emerald-400 ml-4 shrink-0 shadow-sm">
                        <i data-lucide="check" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm">تمت العملية بنجاح!</h4>
                        <p class="text-xs opacity-90 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-500/30 text-red-700 dark:text-red-400 rounded-2xl flex items-center transform transition-all hover:scale-[1.01] shadow-sm">
                    <div class="w-10 h-10 bg-red-100 dark:bg-red-500/20 rounded-full flex items-center justify-center text-red-600 dark:text-red-400 ml-4 shrink-0 shadow-sm">
                        <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    </div>
                     <div>
                        <h4 class="font-bold text-sm">تنبيه!</h4>
                        <p class="text-xs opacity-90 mt-0.5">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>

    </div>

    {{-- Mobile Overlay --}}
    <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 hidden transition-opacity" id="mobile-overlay"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Theme Toggle Logic
        const themeToggleBtn = document.getElementById('theme-toggle');
        const html = document.documentElement;
        
        // Initial Theme Check
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            html.classList.add('dark');
        } else {
            html.classList.remove('dark');
        }

        themeToggleBtn.addEventListener('click', () => {
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                html.classList.add('dark');
                localStorage.theme = 'dark';
            }
        });

        // Mobile Menu Toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobile-overlay');
        const sidebarCloseBtn = document.getElementById('sidebar-close-btn');

        mobileMenuBtn?.addEventListener('click', () => {
            sidebar.classList.toggle('translate-x-full');
            mobileOverlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        });

        mobileOverlay?.addEventListener('click', () => {
            sidebar.classList.add('translate-x-full');
            mobileOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });

        sidebarCloseBtn?.addEventListener('click', () => {
            sidebar.classList.add('translate-x-full');
            mobileOverlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });
    </script>
    @stack('scripts')
</body>
</html>
