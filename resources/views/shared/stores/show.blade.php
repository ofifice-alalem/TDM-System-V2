@extends('layouts.app')

@section('title', 'تفاصيل المتجر - ' . $store->name)

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Back Button & Header --}}
        <div class="animate-fade-in-down">
            <a href="{{ request()->routeIs('marketer.*') ? route('marketer.stores.index') : (request()->routeIs('warehouse.*') ? route('warehouse.stores.index') : route('admin.stores.index')) }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-4 transition-colors">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                <span class="font-bold">العودة للمتاجر</span>
            </a>
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- Right Content - Transactions History --}}
            <div class="lg:col-span-8 order-2 lg:order-1">
                <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up" style="animation-delay: 0.2s">
                    
                    {{-- Store Header --}}
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-amber-500 dark:bg-amber-600 rounded-xl flex items-center justify-center text-white">
                                <i data-lucide="store" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $store->name }}</h1>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">عرض التفاصيل، السجل، الحركات المالية</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center text-primary-600 dark:text-primary-400">
                                <i data-lucide="history" class="w-5 h-5"></i>
                            </div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-white">سجل الحركات المالية</h2>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse($transactions as $transaction)
                            <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-4 hover:shadow-md transition-all border border-gray-100 dark:border-dark-border group">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="flex items-start gap-3 flex-1">
                                        <div class="w-12 h-12 shrink-0 rounded-xl flex items-center justify-center
                                            {{ $transaction['type'] === 'sale' ? 'bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400' : '' }}
                                            {{ $transaction['type'] === 'payment' ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : '' }}
                                            {{ $transaction['type'] === 'return' ? 'bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400' : '' }}
                                        ">
                                            @if($transaction['type'] === 'sale')
                                                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                                            @elseif($transaction['type'] === 'payment')
                                                <i data-lucide="banknote" class="w-5 h-5"></i>
                                            @else
                                                <i data-lucide="package-x" class="w-5 h-5"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">
                                                    @if($transaction['type'] === 'sale')
                                                        فاتورة مبيعات
                                                    @elseif($transaction['type'] === 'payment')
                                                        إيصال قبض
                                                    @else
                                                        مرتجعات
                                                    @endif
                                                </h3>
                                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold
                                                    {{ $transaction['type'] === 'sale' ? 'bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400' : '' }}
                                                    {{ $transaction['type'] === 'payment' ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : '' }}
                                                    {{ $transaction['type'] === 'return' ? 'bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400' : '' }}
                                                ">
                                                    {{ $transaction['number'] }}
                                                </span>
                                            </div>
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                                <span class="flex items-center gap-1.5">
                                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                                    {{ \Carbon\Carbon::parse($transaction['date'])->format('d M Y') }}
                                                </span>
                                                <span class="flex items-center gap-1.5">
                                                    <i data-lucide="user" class="w-4 h-4"></i>
                                                    {{ $transaction['marketer'] }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between sm:justify-end gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-gray-200 dark:border-gray-700">
                                        <div class="text-right">
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">المبلغ الإجمالي</div>
                                            <div class="text-base sm:text-lg font-black
                                                {{ $transaction['type'] === 'sale' ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}
                                            ">
                                                {{ $transaction['type'] === 'sale' ? '+' : '-' }}{{ number_format($transaction['amount'], 2) }} دينار
                                            </div>
                                        </div>
                                        @php
                                            $routePrefix = request()->routeIs('marketer.*') ? 'marketer' : (request()->routeIs('warehouse.*') ? 'warehouse' : 'admin');
                                            $url = '#';
                                            
                                            if ($transaction['type'] === 'sale') {
                                                if ($routePrefix === 'marketer' && Route::has('marketer.sales.show')) {
                                                    $url = route('marketer.sales.show', $transaction['id']);
                                                } elseif ($routePrefix === 'warehouse' && Route::has('warehouse.sales.show')) {
                                                    $url = route('warehouse.sales.show', $transaction['id']);
                                                }
                                            } elseif ($transaction['type'] === 'payment') {
                                                if ($routePrefix === 'marketer' && Route::has('marketer.payments.show')) {
                                                    $url = route('marketer.payments.show', $transaction['id']);
                                                } elseif ($routePrefix === 'warehouse' && Route::has('warehouse.payments.show')) {
                                                    $url = route('warehouse.payments.show', $transaction['id']);
                                                }
                                            } elseif ($transaction['type'] === 'return') {
                                                if ($routePrefix === 'marketer' && Route::has('marketer.sales-returns.show')) {
                                                    $url = route('marketer.sales-returns.show', $transaction['id']);
                                                } elseif ($routePrefix === 'warehouse' && Route::has('warehouse.sales-returns.show')) {
                                                    $url = route('warehouse.sales-returns.show', $transaction['id']);
                                                }
                                            }
                                        @endphp
                                        <a href="{{ $url }}" class="w-10 h-10 bg-gray-100 dark:bg-dark-bg rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-primary-500/10 hover:text-primary-600 dark:hover:text-primary-400 transition-all {{ $url === '#' ? 'opacity-50 cursor-not-allowed' : '' }}">
                                            <i data-lucide="arrow-left" class="w-5 h-5"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="inbox" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد حركات</h3>
                                <p class="text-gray-500 dark:text-dark-muted">لم يتم تسجيل أي حركات مالية لهذا المتجر</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

            {{-- Left Sidebar - Store Info --}}
            <div class="lg:col-span-4 space-y-6 order-1 lg:order-2">
                
                {{-- Store Details Card --}}
                <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center text-primary-600 dark:text-primary-400">
                            <i data-lucide="info" class="w-5 h-5"></i>
                        </div>
                        <h2 class="text-xl font-black text-gray-900 dark:text-white">بيانات المتجر</h2>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-dark-border">
                            <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <i data-lucide="user" class="w-4 h-4"></i>
                                المالك المسؤول
                            </span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $store->owner_name }}</span>
                        </div>

                        @if($store->phone)
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-dark-border">
                            <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                                رقم التواصل
                            </span>
                            <a href="tel:{{ $store->phone }}" class="text-sm font-bold text-primary-600 dark:text-primary-400 hover:underline">{{ $store->phone }}</a>
                        </div>
                        @endif

                        @if($store->location)
                        <div class="py-3 border-b border-gray-100 dark:border-dark-border">
                            <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 mb-2">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                الموقع
                            </span>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $store->location }}</p>
                        </div>
                        @endif

                        @if($store->address)
                        <div class="py-3 border-b border-gray-100 dark:border-dark-border">
                            <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 mb-2">
                                <i data-lucide="map" class="w-4 h-4"></i>
                                العنوان التفصيلي
                            </span>
                            <a href="{{ $store->address }}" target="_blank" class="text-sm font-bold text-primary-600 dark:text-primary-400 hover:underline break-all">فتح الموقع على الخريطة</a>
                        </div>
                        @endif

                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm text-gray-500 dark:text-gray-400">الحالة</span>
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
                    </div>
                </div>

                {{-- Financial Summary Card --}}
                <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up" style="animation-delay: 0.1s">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center text-primary-600 dark:text-primary-400">
                            <i data-lucide="wallet" class="w-5 h-5"></i>
                        </div>
                        <h2 class="text-xl font-black text-gray-900 dark:text-white">الملخص المالي</h2>
                    </div>

                    <div class="space-y-3">
                        <div class="bg-blue-50 dark:bg-blue-500/10 rounded-2xl p-4">
                            <div class="text-xs text-blue-600 dark:text-blue-400 mb-1">إجمالي المبيعات</div>
                            <div class="text-xl font-black text-blue-700 dark:text-blue-300">{{ number_format($stats['total_sales'], 2) }} دينار</div>
                        </div>

                        <div class="bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl p-4">
                            <div class="text-xs text-emerald-600 dark:text-emerald-400 mb-1">إجمالي المدفوعات</div>
                            <div class="text-xl font-black text-emerald-700 dark:text-emerald-300">{{ number_format($stats['total_payments'], 2) }} دينار</div>
                        </div>

                        <div class="bg-orange-50 dark:bg-orange-500/10 rounded-2xl p-4">
                            <div class="text-xs text-orange-600 dark:text-orange-400 mb-1">إجمالي المرتجعات</div>
                            <div class="text-xl font-black text-orange-700 dark:text-orange-300">{{ number_format($stats['total_returns'], 2) }} دينار</div>
                        </div>

                        <div class="bg-gradient-to-br from-{{ $debt > 0 ? 'red' : ($debt < 0 ? 'emerald' : 'gray') }}-100 to-{{ $debt > 0 ? 'red' : ($debt < 0 ? 'emerald' : 'gray') }}-200 dark:from-{{ $debt > 0 ? 'red' : ($debt < 0 ? 'emerald' : 'gray') }}-900/40 dark:to-{{ $debt > 0 ? 'red' : ($debt < 0 ? 'emerald' : 'gray') }}-800/40 rounded-2xl p-5 border-2 border-{{ $debt > 0 ? 'red' : ($debt < 0 ? 'emerald' : 'gray') }}-200 dark:border-{{ $debt > 0 ? 'red' : ($debt < 0 ? 'emerald' : 'gray') }}-700">
                            <div class="text-xs text-{{ $debt > 0 ? 'red' : ($debt < 0 ? 'emerald' : 'gray') }}-700 dark:text-{{ $debt > 0 ? 'red' : ($debt < 0 ? 'emerald' : 'gray') }}-300 mb-1 font-bold">الرصيد الحالي</div>
                            <div class="text-2xl font-black text-{{ $debt > 0 ? 'red' : ($debt < 0 ? 'emerald' : 'gray') }}-800 dark:text-{{ $debt > 0 ? 'red' : ($debt < 0 ? 'emerald' : 'gray') }}-200">
                                {{ $debt > 0 ? '-' : ($debt < 0 ? '+' : '') }}{{ number_format(abs($debt), 2) }} دينار
                            </div>
                        </div>
                    </div>
                </div>

            </div>

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
