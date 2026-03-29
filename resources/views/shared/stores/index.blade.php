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
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-bold uppercase tracking-wider">إجمالي المدفوع والمرتجع</p>
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

        {{-- Search Bar & View Toggle --}}
        <div class="animate-fade-in flex flex-col sm:flex-row items-stretch sm:items-center gap-3" style="z-index: 1000 !important; position: relative;">
            {{-- Search + toggle in one row on mobile --}}
            <form method="GET" action="{{ request()->routeIs('marketer.*') ? route('marketer.stores.index') : (request()->routeIs('warehouse.*') ? route('warehouse.stores.index') : route('admin.stores.index')) }}" class="flex-1 flex gap-2">
                <div class="relative flex-1" id="search-wrapper">
                    <input 
                        type="text" 
                        name="search"
                        id="quick-search"
                        value="{{ $search ?? '' }}"
                        placeholder="ابحث عن متجر..." 
                        class="w-full px-4 py-3 bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-dark-border rounded-2xl text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all shadow-sm"
                        autocomplete="off"
                    >
                    <div id="search-dropdown" class="hidden absolute z-[9999] w-full mt-2 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-2xl shadow-xl max-h-72 overflow-y-auto"></div>
                </div>
                <button type="submit" class="px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-2xl flex items-center gap-2 font-bold transition-all shrink-0">
                    <i data-lucide="search" class="w-4 h-4"></i>
                    <span>بحث</span>
                </button>
                {{-- View toggle: same row on mobile --}}
                <div class="flex gap-1 sm:hidden hidden">
                    <button type="button" onclick="setView('table')" id="tableViewBtn-mobile" class="w-11 h-11 bg-primary-600 text-white rounded-xl flex items-center justify-center hover:bg-primary-700 transition-all">
                        <i data-lucide="table" class="w-4 h-4"></i>
                    </button>
                    <button type="button" onclick="setView('grid')" id="gridViewBtn-mobile" class="w-11 h-11 bg-gray-100 dark:bg-dark-bg text-gray-600 dark:text-gray-400 rounded-xl flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                        <i data-lucide="grid" class="w-4 h-4"></i>
                    </button>
                </div>
            </form>
            @if(!request()->routeIs('marketer.*'))
            <form method="GET" action="{{ request()->routeIs('warehouse.*') ? route('warehouse.stores.index') : route('admin.stores.index') }}" class="flex gap-2">
                <select name="marketer_id" class="px-4 py-3 bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 dark:focus:border-primary-500 transition-all">
                    <option value="">جميع المسوقين</option>
                    @foreach(\App\Models\User::where('role_id', 3)->where('is_active', true)->get() as $marketer)
                        <option value="{{ $marketer->id }}" {{ request('marketer_id') == $marketer->id ? 'selected' : '' }}>
                            {{ $marketer->full_name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all">
                    فلترة
                </button>
            </form>
            @endif
            {{-- View toggle: desktop only --}}
            <div class="hidden sm:flex gap-2">
                <button onclick="setView('table')" id="tableViewBtn" class="w-12 h-12 bg-primary-600 text-white rounded-xl flex items-center justify-center hover:bg-primary-700 transition-all">
                    <i data-lucide="table" class="w-5 h-5"></i>
                </button>
                <button onclick="setView('grid')" id="gridViewBtn" class="w-12 h-12 bg-gray-100 dark:bg-dark-bg text-gray-600 dark:text-gray-400 rounded-xl flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                    <i data-lucide="grid" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        {{-- Stores Table View --}}
        <div id="tableView" class="bg-white dark:bg-dark-card rounded-3xl shadow-lg border border-gray-200 dark:border-dark-border animate-slide-up">
            {{-- Mobile Cards --}}
            <div class="md:hidden p-2 space-y-2">
                @forelse($stores as $store)
                @php $net = $store->confirmed_debt + $store->pending_net; @endphp
                <div class="store-mobile border border-gray-200 dark:border-dark-border rounded-2xl overflow-hidden bg-white dark:bg-dark-card" data-search="{{ strtolower($store->name . ' ' . $store->owner_name . ' ' . $store->location) }}">
                    {{-- Header --}}
                    <div class="px-4 py-3 bg-gray-700 dark:bg-gray-900 flex items-center justify-between">
                        <p class="text-sm font-black text-white truncate">{{ $store->name }}</p>
                        @if($store->is_active)
                            <span class="text-xs font-bold text-emerald-400 shrink-0">نشط</span>
                        @else
                            <span class="text-xs font-bold text-gray-400 shrink-0">غير نشط</span>
                        @endif
                    </div>
                    <div class="px-4 py-3 space-y-2.5">
                        {{-- Marketer + badge --}}
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $store->marketer->full_name ?? '-' }}</span>
                            @if($net > 0)
                                <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-2 py-0.5 rounded-lg text-xs font-black">مدين</span>
                            @elseif($net < 0)
                                <span class="bg-emerald-500 text-white px-2 py-0.5 rounded-lg text-xs font-black">دائن</span>
                            @else
                                <span class="text-gray-400 text-xs">--</span>
                            @endif
                        </div>
                        {{-- معتمد + معلق في سطر --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div class="bg-gray-50 dark:bg-dark-bg rounded-xl px-3 py-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-bold mb-0.5">معتمد</p>
                                <p class="text-sm font-black text-amber-600 dark:text-amber-400">{{ number_format($store->confirmed_debt, 2) }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-dark-bg rounded-xl px-3 py-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-bold mb-0.5">معلق</p>
                                <p class="text-sm font-black {{ $store->pending_net > 0 ? 'text-blue-600 dark:text-blue-400' : ($store->pending_net < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400') }}">{{ $store->pending_net != 0 ? ($store->pending_net > 0 ? '+' : '') . number_format($store->pending_net, 2) : '--' }}</p>
                            </div>
                        </div>
                        {{-- الاجمالي في سطر --}}
                        <div class="{{ $net < 0 ? 'bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-300 dark:border-emerald-700' : 'bg-gray-50 dark:bg-dark-bg' }} rounded-xl px-3 py-2 flex items-center justify-between">
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold">الاجمالي</p>
                            <p class="text-sm font-black {{ $net > 0 ? 'text-red-600 dark:text-red-400' : ($net < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400') }}">{{ number_format($net, 2) }} دينار</p>
                        </div>
                    </div>
                    {{-- Footer --}}
                    <div class="px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border-t border-gray-100 dark:border-dark-border">
                        <a href="{{ request()->routeIs('marketer.*') ? route('marketer.stores.show', $store) : (request()->routeIs('warehouse.*') ? route('warehouse.stores.show', $store) : route('admin.stores.show', $store)) }}" class="flex items-center justify-center gap-2 text-primary-600 dark:text-primary-400 text-sm font-bold">
                            <i data-lucide="eye" class="w-4 h-4"></i>
                            التفاصيل
                        </a>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center">
                    <i data-lucide="inbox" class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400">لا توجد متاجر</p>
                </div>
                @endforelse
                {{-- Mobile Pagination --}}
                @if($stores->hasPages() || $stores->total() > 0)
                <div class="py-3 space-y-3">
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">عرض {{ $stores->firstItem() }} إلى {{ $stores->lastItem() }} من {{ $stores->total() }} نتيجة</p>
                    @if($stores->hasPages())
                    <div class="flex items-center justify-between gap-2">
                        @if($stores->onFirstPage())
                            <span class="flex-1 py-2 text-center text-sm font-bold text-gray-300 dark:text-gray-600 bg-gray-100 dark:bg-dark-bg rounded-xl">السابق</span>
                        @else
                            <a href="{{ $stores->previousPageUrl() }}" class="flex-1 py-2 text-center text-sm font-bold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-dark-bg hover:bg-gray-200 rounded-xl transition-colors">السابق</a>
                        @endif
                        <span class="text-xs font-bold text-gray-500 shrink-0">{{ $stores->currentPage() }} / {{ $stores->lastPage() }}</span>
                        @if($stores->hasMorePages())
                            <a href="{{ $stores->nextPageUrl() }}" class="flex-1 py-2 text-center text-sm font-bold text-white bg-primary-600 hover:bg-primary-700 rounded-xl transition-colors">التالي</a>
                        @else
                            <span class="flex-1 py-2 text-center text-sm font-bold text-gray-300 dark:text-gray-600 bg-gray-100 dark:bg-dark-bg rounded-xl">التالي</span>
                        @endif
                    </div>
                    @endif
                </div>
                @endif
            </div>
            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">#</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">اسم المتجر</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">المسوق</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">معتمد</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">معلق</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">الاجمالي</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">دائن / مدين</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                        @forelse($stores as $index => $store)
                        <tr class="store-row hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors" data-search="{{ strtolower($store->name . ' ' . $store->owner_name . ' ' . $store->location) }}">
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-bold">{{ $index + 1 }}</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">{{ $store->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $store->marketer->full_name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold {{ $store->confirmed_debt > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400 dark:text-gray-500' }}">
                                    {{ number_format($store->confirmed_debt, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($store->pending_net != 0)
                                <span class="text-sm font-bold {{ $store->pending_net > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                    {{ $store->pending_net > 0 ? '+' : '' }}{{ number_format($store->pending_net, 2) }}
                                </span>
                                @else
                                <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php $net = $store->confirmed_debt + $store->pending_net; @endphp
                                <span class="text-sm font-black {{ $net > 0 ? 'text-red-600 dark:text-red-400' : ($net < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400') }}">
                                    {{ number_format($net, 2) }} دينار
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($net > 0)
                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-2 py-0.5 rounded-lg text-xs font-black">مدين</span>
                                @elseif($net < 0)
                                    <span class="bg-emerald-500 text-white px-2 py-0.5 rounded-lg text-xs font-black">دائن</span>
                                @else
                                    <span class="text-gray-400 text-xs">--</span>
                                @endif
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
                            <td colspan="8" class="px-6 py-16 text-center">
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
                <div class="hidden md:block">{{ $stores->links() }}</div>
            </div>
            @endif
            </div>{{-- end hidden md:block --}}
        </div>

        {{-- Stores Grid View --}}
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-slide-up hidden">
            @forelse($stores as $store)
                <div class="store-card bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group" data-search="{{ strtolower($store->name . ' ' . $store->owner_name . ' ' . $store->location) }}">
                    
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
                        @php $net = $store->confirmed_debt + $store->pending_net; @endphp
                        <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-4 text-center {{ $net < 0 ? 'border-2 border-emerald-500' : '' }}">
                            <div class="text-sm font-black {{ $net > 0 ? 'text-red-600 dark:text-red-400' : ($net < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white') }}">
                                {{ number_format($net, 2) }} دينار
                            </div>
                            <div class="mt-1">
                                @if($net > 0)
                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-2 py-0.5 rounded-lg text-xs font-black">مدين</span>
                                @elseif($net < 0)
                                    <span class="bg-emerald-500 text-white px-2 py-0.5 rounded-lg text-xs font-black">دائن</span>
                                @else
                                    <span class="text-gray-400 text-xs">--</span>
                                @endif
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
        <div class="mt-6">
            <div class="hidden md:flex justify-center">{{ $stores->links() }}</div>
        </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
// بيانات المتاجر للبحث السريع
const storesData = [
    @foreach($allStoresForSearch as $store)
    {
        id: {{ $store->id }},
        name: @json($store->name),
        owner: @json($store->owner_name),
        location: @json($store->location ?? ''),
        url: "{{ request()->routeIs('marketer.*') ? route('marketer.stores.show', $store) : (request()->routeIs('warehouse.*') ? route('warehouse.stores.show', $store) : route('admin.stores.show', $store)) }}",
        debt: 0,
    },
    @endforeach
];

document.addEventListener('DOMContentLoaded', function () {
    const input    = document.getElementById('quick-search');
    const dropdown = document.getElementById('search-dropdown');
    const wrapper  = document.getElementById('search-wrapper');

    input.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        if (!q) { dropdown.classList.add('hidden'); return; }

        const results = storesData.filter(s =>
            s.name.toLowerCase().includes(q) ||
            s.owner.toLowerCase().includes(q) ||
            s.location.toLowerCase().includes(q)
        ).slice(0, 10);

        if (!results.length) {
            dropdown.innerHTML = '<div class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">لا توجد نتائج</div>';
            dropdown.classList.remove('hidden');
            return;
        }

        dropdown.innerHTML = results.map(s => {
            const debtColor = s.debt > 0 ? 'text-red-600 dark:text-red-400' : (s.debt < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400');
            const debtLabel = s.debt > 0 ? 'مدين' : (s.debt < 0 ? 'دائن' : '');
            const debtBadge = debtLabel
                ? `<span class="text-xs font-black px-2 py-0.5 rounded-lg ${ s.debt > 0 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' }">${debtLabel}</span>`
                : '';
            return `<a href="${s.url}" class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 dark:hover:bg-dark-bg border-b border-gray-100 dark:border-dark-border last:border-0 transition-colors">
                <div>
                    <div class="font-bold text-sm text-gray-900 dark:text-white">${s.name}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">${s.owner}</div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-sm font-black ${debtColor}">${s.debt.toFixed(2)}</span>
                    ${debtBadge}
                </div>
            </a>`;
        }).join('');

        dropdown.classList.remove('hidden');
    });

    document.addEventListener('click', function (e) {
        if (!wrapper.contains(e.target)) dropdown.classList.add('hidden');
    });
});

    function setView(view) {
        const tableView = document.getElementById('tableView');
        const gridView  = document.getElementById('gridView');
        const tableBtn  = document.getElementById('tableViewBtn');
        const gridBtn   = document.getElementById('gridViewBtn');
        const tableBtnM = document.getElementById('tableViewBtn-mobile');
        const gridBtnM  = document.getElementById('gridViewBtn-mobile');

        const activeCls   = ['bg-primary-600', 'text-white'];
        const inactiveCls = ['bg-gray-100', 'dark:bg-dark-bg', 'text-gray-600', 'dark:text-gray-400'];

        if (view === 'table') {
            tableView.classList.remove('hidden');
            gridView.classList.add('hidden');
            [tableBtn, tableBtnM].forEach(b => { if(b){ b.classList.add(...activeCls); b.classList.remove(...inactiveCls); } });
            [gridBtn,  gridBtnM ].forEach(b => { if(b){ b.classList.remove(...activeCls); b.classList.add(...inactiveCls); } });
            localStorage.setItem('storesView', 'table');
        } else {
            tableView.classList.add('hidden');
            gridView.classList.remove('hidden');
            [gridBtn,  gridBtnM ].forEach(b => { if(b){ b.classList.add(...activeCls); b.classList.remove(...inactiveCls); } });
            [tableBtn, tableBtnM].forEach(b => { if(b){ b.classList.remove(...activeCls); b.classList.add(...inactiveCls); } });
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
