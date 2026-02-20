@extends('layouts.app')

@section('title', 'فواتير المصنع')

@section('content')
@php
    $routePrefix = request()->routeIs('admin.*') ? 'admin' : 'warehouse';
@endphp

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
            <div class="lg:col-span-8">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        تعبئة المخزن
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    فواتير المصنع
                </h1>
            </div>

            <div class="lg:col-span-4 lg:translate-y-[30px]">
                <a href="{{ route($routePrefix . '.factory-invoices.create') }}" class="px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2 w-full">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    فاتورة جديدة
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-none border border-gray-200 dark:border-dark-border mb-6">
            <form method="GET" class="flex flex-col md:flex-row gap-3">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <input type="hidden" name="all" value="{{ request('all') }}">
                
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">رقم الفاتورة</label>
                    <input type="text" name="invoice_number" value="{{ request('invoice_number') }}" placeholder="ابحث برقم الفاتورة..." class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex gap-2 items-end">
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2 justify-center">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        فلترة
                    </button>
                    @if(request('invoice_number'))
                        <a href="{{ route($routePrefix . '.factory-invoices.index', ['status' => request('status'), 'all' => request('all')]) }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2 justify-center">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            إلغاء
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-dark-card rounded-2xl p-2 shadow-lg shadow-gray-200/50 dark:shadow-none border border-gray-200 dark:border-dark-border mb-6">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route($routePrefix . '.factory-invoices.index', ['status' => 'pending']) }}" class="{{ !request('all') && (!request('status') || request('status') === 'pending') ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border-2 border-amber-200 dark:border-amber-800' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(50%-0.5rem)] md:basis-auto justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    قيد الانتظار
                </a>
                <a href="{{ route($routePrefix . '.factory-invoices.index', ['status' => 'documented']) }}" class="{{ request('status') === 'documented' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-2 border-emerald-200 dark:border-emerald-800' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(50%-0.5rem)] md:basis-auto justify-center">
                    <i data-lucide="file-check" class="w-4 h-4"></i>
                    موثق
                </a>
                <a href="{{ route($routePrefix . '.factory-invoices.index', ['status' => 'cancelled']) }}" class="{{ request('status') === 'cancelled' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 border-2 border-red-200 dark:border-red-800' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(50%-0.5rem)] md:basis-auto justify-center">
                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                    ملغى
                </a>
                <a href="{{ route($routePrefix . '.factory-invoices.index', ['all' => '1']) }}" class="{{ request('all') ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-3 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-1.5 flex-1 basis-[calc(50%-0.5rem)] md:basis-auto justify-center">
                    <i data-lucide="list" class="w-4 h-4"></i>
                    الكل
                </a>
            </div>
        </div>

        {{-- Invoices List --}}
        <div class="bg-white dark:bg-dark-card rounded-[2rem] p-4 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
            @forelse($invoices as $invoice)
                @php
                    $statusConfig = [
                        'pending' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20', 'text' => 'text-amber-700 dark:text-amber-400', 'iconBg' => 'bg-amber-100 dark:bg-amber-900/40', 'icon' => 'clock', 'label' => 'قيد الانتظار'],
                        'documented' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-700 dark:text-emerald-400', 'iconBg' => 'bg-emerald-100 dark:bg-emerald-900/40', 'icon' => 'file-check', 'label' => 'موثق'],
                        'cancelled' => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'text' => 'text-red-700 dark:text-red-400', 'iconBg' => 'bg-red-100 dark:bg-red-900/40', 'icon' => 'x-circle', 'label' => 'ملغى'],
                    ][$invoice->status];
                @endphp

                <div class="bg-gray-50 dark:bg-dark-bg/60 rounded-2xl border border-gray-200 dark:border-dark-border mb-3 first:mt-4 md:first:mt-0 last:mb-4 md:last:mb-0 hover:shadow-md transition-all overflow-hidden">
                    <div class="flex flex-row-reverse">
                        <div class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-3 py-4 text-sm font-bold flex flex-col items-center justify-center gap-3 border-r {{ $statusConfig['iconBg'] }}/30">
                            <span class="{{ $statusConfig['iconBg'] }} {{ $statusConfig['text'] }} w-8 h-8 rounded-full flex items-center justify-center shrink-0">
                                <i data-lucide="{{ $statusConfig['icon'] }}" class="w-4 h-4"></i>
                            </span>
                            <span style="writing-mode: vertical-rl; text-orientation: mixed;">{{ $statusConfig['label'] }}</span>
                        </div>

                        <div class="flex-1 p-4 md:p-6 flex items-center">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 w-full">
                                <div class="flex gap-2 md:order-2">
                                    <a href="{{ route($routePrefix . '.factory-invoices.show', $invoice) }}" class="px-5 py-2.5 bg-white dark:bg-dark-card border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 dark:hover:border-gray-500 transition-all text-sm flex items-center gap-2 shadow-sm">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                        التفاصيل
                                    </a>
                                </div>
                                
                                <div class="flex-1 md:order-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-xl md:text-lg font-black text-gray-900 dark:text-white">#{{ $invoice->invoice_number }}</h3>
                                    </div>
                                    
                                    <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400 font-medium">
                                        <div class="flex items-center gap-1.5">
                                            <i data-lucide="calendar" class="w-4 h-4"></i>
                                            <span>{{ $invoice->created_at->format('Y-m-d') }}</span>
                                        </div>
                                        <div class="flex items-center gap-1.5">
                                            <i data-lucide="package" class="w-4 h-4"></i>
                                            <span>{{ $invoice->items->count() }} منتج</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="inbox" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد فواتير</h3>
                    <p class="text-gray-500 dark:text-dark-muted mb-6">لم تقم بإنشاء أي فواتير مصنع بعد</p>
                    <a href="{{ route($routePrefix . '.factory-invoices.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        إنشاء فاتورة جديدة
                    </a>
                </div>
            @endforelse

            @if($invoices->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
                    {{ $invoices->links() }}
                </div>
            @endif
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
