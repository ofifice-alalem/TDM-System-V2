@extends('layouts.app')

@section('title', 'لوحة التحكم - أمين المخزن')

@section('content')

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4">
        
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 dark:text-white">لوحة التحكم</h1>
            <p class="text-gray-500 dark:text-dark-muted mt-1">نظرة سريعة على العمليات المعلقة</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            {{-- قسم: طلبات المسوقين --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="package" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <h2 class="text-lg font-black text-gray-900 dark:text-white">طلبات المسوقين</h2>
                </div>
                
                <a href="{{ route('warehouse.requests.index', ['status' => 'pending']) }}" class="block bg-white dark:bg-dark-card rounded-xl p-5 border border-gray-200 dark:border-dark-border hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/20 rounded-lg flex items-center justify-center">
                                <i data-lucide="clock" class="w-5 h-5 text-amber-600"></i>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white">طلبات معلقة</span>
                        </div>
                        @if($pendingRequests > 0)
                            <span class="bg-red-500 text-white text-sm font-bold px-3 py-1.5 rounded-full">{{ $pendingRequests }}</span>
                        @else
                            <span class="text-2xl font-black text-gray-400">{{ $pendingRequests }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">تحتاج موافقة</p>
                </a>

                <a href="{{ route('warehouse.requests.index', ['status' => 'approved']) }}" class="block bg-white dark:bg-dark-card rounded-xl p-5 border border-gray-200 dark:border-dark-border hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white">بانتظار التوثيق</span>
                        </div>
                        @if($approvedRequests > 0)
                            <span class="bg-blue-500 text-white text-sm font-bold px-3 py-1.5 rounded-full">{{ $approvedRequests }}</span>
                        @else
                            <span class="text-2xl font-black text-gray-400">{{ $approvedRequests }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">معتمدة ومحجوزة</p>
                </a>

                <a href="{{ route('warehouse.returns.index', ['status' => 'pending']) }}" class="block bg-white dark:bg-dark-card rounded-xl p-5 border border-gray-200 dark:border-dark-border hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                                <i data-lucide="package-x" class="w-5 h-5 text-purple-600"></i>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white">إرجاعات المسوقين</span>
                        </div>
                        @if($pendingReturns > 0)
                            <span class="bg-red-500 text-white text-sm font-bold px-3 py-1.5 rounded-full">{{ $pendingReturns }}</span>
                        @else
                            <span class="text-2xl font-black text-gray-400">{{ $pendingReturns }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">تحتاج معالجة</p>
                </a>
            </div>

            {{-- قسم: فواتير البيع --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                    </div>
                    <h2 class="text-lg font-black text-gray-900 dark:text-white">فواتير البيع</h2>
                </div>
                
                <a href="{{ route('warehouse.sales.index', ['status' => 'pending']) }}" class="block bg-white dark:bg-dark-card rounded-xl p-5 border border-gray-200 dark:border-dark-border hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg flex items-center justify-center">
                                <i data-lucide="file-text" class="w-5 h-5 text-emerald-600"></i>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white">فواتير معلقة</span>
                        </div>
                        @if($pendingSales > 0)
                            <span class="bg-red-500 text-white text-sm font-bold px-3 py-1.5 rounded-full">{{ $pendingSales }}</span>
                        @else
                            <span class="text-2xl font-black text-gray-400">{{ $pendingSales }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">تحتاج توثيق</p>
                </a>

                <a href="{{ route('warehouse.sales-returns.index', ['status' => 'pending']) }}" class="block bg-white dark:bg-dark-card rounded-xl p-5 border border-gray-200 dark:border-dark-border hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-50 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
                                <i data-lucide="undo-2" class="w-5 h-5 text-orange-600"></i>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white">إرجاعات من المتاجر</span>
                        </div>
                        @if($pendingSalesReturns > 0)
                            <span class="bg-red-500 text-white text-sm font-bold px-3 py-1.5 rounded-full">{{ $pendingSalesReturns }}</span>
                        @else
                            <span class="text-2xl font-black text-gray-400">{{ $pendingSalesReturns }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">من المتاجر للمسوقين</p>
                </a>
            </div>

            {{-- قسم: إيصالات القبض (الجانب الأيسر) --}}
            <div class="space-y-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-cyan-100 dark:bg-cyan-900/30 rounded-lg flex items-center justify-center">
                        <i data-lucide="banknote" class="w-5 h-5 text-cyan-600 dark:text-cyan-400"></i>
                    </div>
                    <h2 class="text-lg font-black text-gray-900 dark:text-white">إيصالات القبض</h2>
                </div>
                
                <a href="{{ route('warehouse.payments.index', ['status' => 'pending']) }}" class="block bg-white dark:bg-dark-card rounded-xl p-5 border border-gray-200 dark:border-dark-border hover:shadow-lg transition-all hover:-translate-y-1">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-cyan-50 dark:bg-cyan-900/20 rounded-lg flex items-center justify-center">
                                <i data-lucide="wallet" class="w-5 h-5 text-cyan-600"></i>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white">إيصالات معلقة</span>
                        </div>
                        @if($pendingPayments > 0)
                            <span class="bg-red-500 text-white text-sm font-bold px-3 py-1.5 rounded-full">{{ $pendingPayments }}</span>
                        @else
                            <span class="text-2xl font-black text-gray-400">{{ $pendingPayments }}</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">تحتاج موافقة</p>
                </a>

                {{-- قسم: الطلبات العاجلة --}}
                @if($oldestRequests->count() > 0)
                <div class="bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 rounded-xl p-5 border-2 border-red-200 dark:border-red-700/30 mt-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-500 rounded-lg flex items-center justify-center animate-pulse">
                            <i data-lucide="alert-triangle" class="w-5 h-5 text-white"></i>
                        </div>
                        <h2 class="text-lg font-black text-red-900 dark:text-red-400">طلبات عاجلة</h2>
                    </div>
                    
                    <div class="space-y-2">
                        @foreach($oldestRequests as $request)
                            @php
                                $daysOld = now()->diffInDays($request->created_at);
                            @endphp
                            
                            <div class="bg-white dark:bg-dark-card rounded-lg p-3">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-gray-900 dark:text-white text-sm">{{ $request->invoice_number }}</span>
                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2 py-0.5 rounded text-xs font-bold">
                                        {{ $daysOld }} يوم
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ $request->marketer->full_name }}</p>
                                <a href="{{ route('warehouse.requests.show', $request) }}" class="block w-full text-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold text-xs transition-all">
                                    معالجة فوراً
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
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
