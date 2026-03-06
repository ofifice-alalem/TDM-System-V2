@extends('layouts.app')

@section('title', 'المتاجر')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="animate-fade-in-down">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-3">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة المتاجر
                    </span>
                </div>
                @if(request()->routeIs('admin.*') || request()->routeIs('warehouse.*'))
                <a href="{{ request()->routeIs('admin.*') ? route('admin.stores.create') : route('warehouse.stores.create') }}" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                    <i data-lucide="plus" class="w-5 h-5"></i>
                    إضافة متجر جديد
                </a>
                @endif
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                المتاجر
            </h1>
        </div>

        {{-- Search Bar & View Toggle --}}
        <div class="animate-fade-in flex items-center gap-4">
            <form method="GET" action="{{ request()->routeIs('marketer.*') ? route('marketer.stores.index') : (request()->routeIs('warehouse.*') ? route('warehouse.stores.index') : route('admin.stores.index')) }}" class="flex-1 max-w-2xl">
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
            <div class="flex gap-2">
                <button onclick="setView('table')" id="tableViewBtn" class="w-12 h-12 bg-primary-600 text-white rounded-xl flex items-center justify-center hover:bg-primary-700 transition-all">
                    <i data-lucide="table" class="w-5 h-5"></i>
                </button>
                <button onclick="setView('grid')" id="gridViewBtn" class="w-12 h-12 bg-gray-100 dark:bg-dark-bg text-gray-600 dark:text-gray-400 rounded-xl flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                    <i data-lucide="grid" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        {{-- Stats Summary --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in">
            <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-red-100 dark:bg-red-900/30 rounded-2xl flex items-center justify-center text-red-600 dark:text-red-400">
                        <i data-lucide="trending-up" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-bold uppercase tracking-wider">إجمالي الديون</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($totalDebt, 2) }} <span class="text-sm text-gray-500">دينار</span></p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <i data-lucide="check-circle" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-bold uppercase tracking-wider">إجمالي المدفوع</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($totalPayments, 2) }} <span class="text-sm text-gray-500">دينار</span></p>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-amber-100 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <i data-lucide="alert-circle" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-bold uppercase tracking-wider">إجمالي المتبقي</p>
                        <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($totalRemaining, 2) }} <span class="text-sm text-gray-500">دينار</span></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stores Table View --}}
        <div id="tableView" class="bg-white dark:bg-dark-card rounded-3xl shadow-lg border border-gray-200 dark:border-dark-border overflow-hidden animate-slide-up">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">#</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">اسم المتجر</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">المالك</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">الموقع</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">الدين</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                        @forelse($stores as $index => $store)
                        <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-bold">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">{{ $store->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $store->owner_name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $store->location ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold {{ $store->total_debt > 0 ? 'text-red-600 dark:text-red-400' : ($store->total_debt < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white') }}">
                                    {{ number_format(abs($store->total_debt), 2) }} دينار
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($store->is_active)
                                <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-bold border border-emerald-100 dark:border-emerald-500/30 inline-flex items-center gap-1">
                                    <i data-lucide="check-circle" class="w-3 h-3"></i>
                                    نشط
                                </span>
                                @else
                                <span class="px-3 py-1 bg-gray-50 dark:bg-gray-500/10 text-gray-600 dark:text-gray-400 rounded-full text-xs font-bold border border-gray-100 dark:border-gray-500/30 inline-flex items-center gap-1">
                                    <i data-lucide="x-circle" class="w-3 h-3"></i>
                                    غير نشط
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ request()->routeIs('marketer.*') ? route('marketer.stores.show', $store) : (request()->routeIs('warehouse.*') ? route('warehouse.stores.show', $store) : route('admin.stores.show', $store)) }}" class="w-9 h-9 bg-primary-100 dark:bg-primary-500/10 rounded-lg flex items-center justify-center text-primary-600 dark:text-primary-400 hover:bg-primary-200 dark:hover:bg-primary-500/20 transition-all">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    @if(request()->routeIs('admin.*') || request()->routeIs('warehouse.*'))
                                    <a href="{{ request()->routeIs('admin.*') ? route('admin.stores.edit', $store) : route('warehouse.stores.edit', $store) }}" class="w-9 h-9 bg-amber-100 dark:bg-amber-500/10 rounded-lg flex items-center justify-center text-amber-600 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-amber-500/20 transition-all">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center">
                                <div class="w-24 h-24 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="store" class="w-12 h-12 text-gray-400 dark:text-gray-600"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">لا توجد متاجر</h3>
                                <p class="text-gray-500 dark:text-dark-muted">لم يتم إضافة أي متاجر بعد</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($stores->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-dark-border">
                {{ $stores->links() }}
            </div>
            @endif
        </div>

        {{-- Stores Grid View --}}
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-slide-up hidden">
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
                        @if($store->is_active)
                        <span class="px-3 py-1.5 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-bold border border-emerald-100 dark:border-emerald-500/30 flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                            نشط
                        </span>
                        @else
                        <span class="px-3 py-1.5 bg-gray-50 dark:bg-gray-500/10 text-gray-600 dark:text-gray-400 rounded-full text-xs font-bold border border-gray-100 dark:border-gray-500/30 flex items-center gap-1">
                            <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                            غير نشط
                        </span>
                        @endif
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
                                الدين
                            </div>
                            <div class="text-sm font-black {{ $store->total_debt > 0 ? 'text-red-600 dark:text-red-400' : ($store->total_debt < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white') }}">
                                {{ number_format(abs($store->total_debt), 2) }} دينار
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2">
                        <a href="{{ request()->routeIs('marketer.*') ? route('marketer.stores.show', $store) : (request()->routeIs('warehouse.*') ? route('warehouse.stores.show', $store) : route('admin.stores.show', $store)) }}" class="flex-1 px-5 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            التفاصيل
                        </a>
                        @if(request()->routeIs('admin.*') || request()->routeIs('warehouse.*'))
                        <a href="{{ request()->routeIs('admin.*') ? route('admin.stores.edit', $store) : route('warehouse.stores.edit', $store) }}" class="w-12 h-12 bg-amber-100 dark:bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-600 dark:text-amber-400 hover:bg-amber-200 dark:hover:bg-amber-500/20 transition-all">
                            <i data-lucide="edit" class="w-5 h-5"></i>
                        </a>
                        @endif
                        @if($store->phone)
                        <a href="tel:{{ $store->phone }}" class="w-12 h-12 bg-gray-100 dark:bg-dark-bg rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            <i data-lucide="phone" class="w-5 h-5"></i>
                        </a>
                        @endif
                        <button class="w-12 h-12 bg-gray-100 dark:bg-dark-bg rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            <i data-lucide="message-circle" class="w-5 h-5"></i>
                        </button>
                        @if($store->address)
                        <a href="{{ $store->address }}" target="_blank" class="w-12 h-12 bg-gray-100 dark:bg-dark-bg rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                            <i data-lucide="map-pin" class="w-5 h-5"></i>
                        </a>
                        @endif
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

        @if($stores->hasPages())
        <div class="flex justify-center mt-6">
            {{ $stores->links() }}
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
    function setView(view) {
        const tableView = document.getElementById('tableView');
        const gridView = document.getElementById('gridView');
        const tableBtn = document.getElementById('tableViewBtn');
        const gridBtn = document.getElementById('gridViewBtn');
        
        if (view === 'table') {
            tableView.classList.remove('hidden');
            gridView.classList.add('hidden');
            tableBtn.classList.add('bg-primary-600', 'text-white');
            tableBtn.classList.remove('bg-gray-100', 'dark:bg-dark-bg', 'text-gray-600', 'dark:text-gray-400');
            gridBtn.classList.remove('bg-primary-600', 'text-white');
            gridBtn.classList.add('bg-gray-100', 'dark:bg-dark-bg', 'text-gray-600', 'dark:text-gray-400');
            localStorage.setItem('storesView', 'table');
        } else {
            tableView.classList.add('hidden');
            gridView.classList.remove('hidden');
            gridBtn.classList.add('bg-primary-600', 'text-white');
            gridBtn.classList.remove('bg-gray-100', 'dark:bg-dark-bg', 'text-gray-600', 'dark:text-gray-400');
            tableBtn.classList.remove('bg-primary-600', 'text-white');
            tableBtn.classList.add('bg-gray-100', 'dark:bg-dark-bg', 'text-gray-600', 'dark:text-gray-400');
            localStorage.setItem('storesView', 'grid');
        }
        lucide.createIcons();
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const savedView = localStorage.getItem('storesView') || 'table';
        setView(savedView);
        lucide.createIcons();
    });
</script>
@endpush

@endsection
