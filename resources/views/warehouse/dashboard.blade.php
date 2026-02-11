@extends('layouts.app')

@section('title', 'لوحة التحكم - أمين المخزن')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
            <div class="lg:col-span-12">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        أمين المخزن
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    لوحة التحكم
                </h1>
                <p class="text-gray-500 dark:text-dark-muted mt-2">نظرة شاملة على جميع العمليات المعلقة</p>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            {{-- طلبات البضاعة المعلقة --}}
            <a href="{{ route('warehouse.requests.index', ['status' => 'pending']) }}" class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-2xl p-6 border border-amber-200 dark:border-amber-700/30 hover:shadow-xl transition-all transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-amber-500 dark:bg-amber-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="package" class="w-6 h-6 text-white"></i>
                    </div>
                    @if($pendingRequests > 0)
                        <span class="bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">{{ $pendingRequests }}</span>
                    @endif
                </div>
                <h3 class="text-2xl font-black text-amber-900 dark:text-amber-400">{{ $pendingRequests }}</h3>
                <p class="text-sm text-amber-700 dark:text-amber-500 font-bold mt-1">طلبات معلقة</p>
            </a>

            {{-- طلبات معتمدة بانتظار التوثيق --}}
            <a href="{{ route('warehouse.requests.index', ['status' => 'approved']) }}" class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-2xl p-6 border border-blue-200 dark:border-blue-700/30 hover:shadow-xl transition-all transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-500 dark:bg-blue-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                    </div>
                    @if($approvedRequests > 0)
                        <span class="bg-blue-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">{{ $approvedRequests }}</span>
                    @endif
                </div>
                <h3 class="text-2xl font-black text-blue-900 dark:text-blue-400">{{ $approvedRequests }}</h3>
                <p class="text-sm text-blue-700 dark:text-blue-500 font-bold mt-1">بانتظار التوثيق</p>
            </a>

            {{-- إرجاعات المسوقين --}}
            <a href="{{ route('warehouse.returns.index', ['status' => 'pending']) }}" class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-2xl p-6 border border-purple-200 dark:border-purple-700/30 hover:shadow-xl transition-all transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-500 dark:bg-purple-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="package-x" class="w-6 h-6 text-white"></i>
                    </div>
                    @if($pendingReturns > 0)
                        <span class="bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">{{ $pendingReturns }}</span>
                    @endif
                </div>
                <h3 class="text-2xl font-black text-purple-900 dark:text-purple-400">{{ $pendingReturns }}</h3>
                <p class="text-sm text-purple-700 dark:text-purple-500 font-bold mt-1">إرجاعات معلقة</p>
            </a>

            {{-- فواتير البيع --}}
            <a href="{{ route('warehouse.sales.index', ['status' => 'pending']) }}" class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 rounded-2xl p-6 border border-emerald-200 dark:border-emerald-700/30 hover:shadow-xl transition-all transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-emerald-500 dark:bg-emerald-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="shopping-cart" class="w-6 h-6 text-white"></i>
                    </div>
                    @if($pendingSales > 0)
                        <span class="bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">{{ $pendingSales }}</span>
                    @endif
                </div>
                <h3 class="text-2xl font-black text-emerald-900 dark:text-emerald-400">{{ $pendingSales }}</h3>
                <p class="text-sm text-emerald-700 dark:text-emerald-500 font-bold mt-1">فواتير بيع معلقة</p>
            </a>

            {{-- إيصالات القبض --}}
            <a href="{{ route('warehouse.payments.index', ['status' => 'pending']) }}" class="bg-gradient-to-br from-cyan-50 to-cyan-100 dark:from-cyan-900/20 dark:to-cyan-800/20 rounded-2xl p-6 border border-cyan-200 dark:border-cyan-700/30 hover:shadow-xl transition-all transform hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-cyan-500 dark:bg-cyan-600 rounded-xl flex items-center justify-center">
                        <i data-lucide="banknote" class="w-6 h-6 text-white"></i>
                    </div>
                    @if($pendingPayments > 0)
                        <span class="bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full">{{ $pendingPayments }}</span>
                    @endif
                </div>
                <h3 class="text-2xl font-black text-cyan-900 dark:text-cyan-400">{{ $pendingPayments }}</h3>
                <p class="text-sm text-cyan-700 dark:text-cyan-500 font-bold mt-1">إيصالات معلقة</p>
            </a>
        </div>

        {{-- أقدم الطلبات (أولوية) --}}
        @if($oldestRequests->count() > 0)
        <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white">الطلبات الأقدم (أولوية)</h2>
                    <p class="text-sm text-gray-500 dark:text-dark-muted mt-1">يجب معالجة هذه الطلبات أولاً</p>
                </div>
                <div class="bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 px-4 py-2 rounded-xl font-bold text-sm flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i>
                    عاجل
                </div>
            </div>

            <div class="space-y-4">
                @foreach($oldestRequests as $request)
                    @php
                        $statusConfig = [
                            'pending' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-700 dark:text-amber-400', 'label' => 'معلق'],
                            'approved' => ['bg' => 'bg-blue-50 dark:bg-blue-900/20', 'text' => 'text-blue-700 dark:text-blue-400', 'label' => 'معتمد'],
                        ][$request->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'label' => 'غير معروف'];
                        
                        $daysOld = now()->diffInDays($request->created_at);
                    @endphp
                    
                    <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-4 border border-gray-200 dark:border-dark-border hover:shadow-md transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="font-black text-gray-900 dark:text-white">{{ $request->invoice_number }}</span>
                                    <span class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-3 py-1 rounded-lg text-xs font-bold">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                    @if($daysOld > 2)
                                        <span class="bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 px-3 py-1 rounded-lg text-xs font-bold">
                                            {{ $daysOld }} يوم
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="user" class="w-4 h-4"></i>
                                        {{ $request->marketer->full_name }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-4 h-4"></i>
                                        {{ $request->created_at->format('Y-m-d') }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="package" class="w-4 h-4"></i>
                                        {{ $request->items->count() }} منتج
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('warehouse.requests.show', $request) }}" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                                معالجة
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
                <a href="{{ route('warehouse.requests.index') }}" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 font-bold text-sm flex items-center gap-2 justify-center">
                    عرض جميع الطلبات
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
        @endif

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
