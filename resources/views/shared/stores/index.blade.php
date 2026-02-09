@extends('layouts.app')

@section('title', 'المتاجر')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="animate-fade-in-down">
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                    إدارة المتاجر
                </span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                المتاجر
            </h1>
        </div>

        {{-- Search Bar --}}
        <div class="animate-fade-in">
            <form method="GET" action="{{ request()->routeIs('marketer.*') ? route('marketer.stores.index') : (request()->routeIs('warehouse.*') ? route('warehouse.stores.index') : route('admin.stores.index')) }}" class="max-w-2xl mr-auto">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search ?? '' }}"
                        placeholder="ابحث عن متجر..." 
                        class="w-full px-6 py-4 pr-14 bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-dark-border rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all shadow-sm"
                    >
                    <button type="submit" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center text-primary-600 dark:text-primary-400 hover:bg-primary-100 dark:hover:bg-primary-500/20 transition-all">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>
                </div>
            </form>
        </div>

        {{-- Stores Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-slide-up">
            @forelse($stores as $store)
                <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                    
                    {{-- Store Header --}}
                    <div class="flex items-start justify-between mb-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-blue-200 dark:from-blue-900/40 dark:to-blue-800/40 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400 shadow-md group-hover:scale-110 transition-transform">
                                <i data-lucide="store" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-1">{{ $store->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-1">
                                    <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                    {{ $store->owner_name }}
                                </p>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-bold border border-emerald-100 dark:border-emerald-500/30 flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                            نشط
                        </span>
                    </div>

                    {{-- Store Info --}}
                    @if($store->location)
                    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400 flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-4 h-4 text-gray-400"></i>
                        {{ $store->location }}
                    </div>
                    @endif

                    {{-- Stats --}}
                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-4 text-center">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 flex items-center justify-center gap-1">
                                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                المالك
                            </div>
                            <div class="text-sm font-black text-gray-900 dark:text-white truncate">{{ Str::limit($store->owner_name, 15) }}</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-4 text-center">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 flex items-center justify-center gap-1">
                                <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                                الرصيد
                            </div>
                            <div class="text-sm font-black {{ $store->total_debt > 0 ? 'text-red-600 dark:text-red-400' : ($store->total_debt < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white') }}">
                                {{ number_format(abs($store->total_debt), 2) }} د.ل
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2">
                        <a href="{{ request()->routeIs('marketer.*') ? route('marketer.stores.show', $store) : (request()->routeIs('warehouse.*') ? route('warehouse.stores.show', $store) : route('admin.stores.show', $store)) }}" class="flex-1 px-5 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            التفاصيل
                        </a>
                        @if($store->phone)
                        <a href="tel:{{ $store->phone }}" class="w-12 h-12 bg-gray-100 dark:bg-dark-bg rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </a>
                        @endif
                        <button class="w-12 h-12 bg-gray-100 dark:bg-dark-bg rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            <i data-lucide="message-circle" class="w-5 h-5"></i>
                        </button>
                        <a href="{{ request()->routeIs('marketer.*') ? route('marketer.stores.show', $store) : (request()->routeIs('warehouse.*') ? route('warehouse.stores.show', $store) : route('admin.stores.show', $store)) }}" class="w-12 h-12 bg-gray-100 dark:bg-dark-bg rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            <i data-lucide="eye" class="w-5 h-5"></i>
                        </a>
                    </div>

                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="w-24 h-24 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="store" class="w-12 h-12 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">لا توجد متاجر</h3>
                    <p class="text-gray-500 dark:text-dark-muted">لم يتم إضافة أي متاجر بعد</p>
                </div>
            @endforelse
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush

@endsection
