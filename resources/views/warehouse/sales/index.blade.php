@extends('layouts.app')

@section('title', 'فواتير البيع')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        <div class="grid grid-cols-1 gap-6 animate-fade-in-down">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة المبيعات
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    فواتير البيع
                </h1>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <div class="lg:col-span-8">
                {{-- Filters --}}
                <div class="animate-fade-in mb-6">
                    <details class="bg-white dark:bg-dark-card rounded-2xl shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border overflow-hidden">
                        <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i data-lucide="filter" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                                <span class="font-bold text-gray-900 dark:text-white">فلترة متقدمة</span>
                                @if(request('invoice_number') || request('from_date') || request('to_date') || request('search'))
                                    <span class="px-2 py-1 bg-primary-100 dark:bg-primary-500/20 text-primary-600 dark:text-primary-400 rounded-lg text-xs font-bold">نشط</span>
                                @endif
                            </div>
                            <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400"></i>
                        </summary>
                        <form method="GET" action="{{ route('warehouse.sales.index') }}" class="p-6 border-t border-gray-200 dark:border-dark-border">
                            <input type="hidden" name="status" value="{{ request('status') }}">
                            <input type="hidden" name="all" value="{{ request('all') }}">
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">رقم الفاتورة</label>
                                    <input type="text" name="invoice_number" value="{{ request('invoice_number') }}" placeholder="ابحث..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">من تاريخ</label>
                                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all dark:[color-scheme:dark]">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">إلى تاريخ</label>
                                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all dark:[color-scheme:dark]">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">المسوق أو المتجر</label>
                                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all">
                                </div>
                            </div>
                            <div class="flex gap-2 mt-6">
                                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all flex items-center gap-2 shadow-md">
                                    <i data-lucide="search" class="w-4 h-4"></i>
                                    بحث
                                </button>
                                @if(request('invoice_number') || request('from_date') || request('to_date') || request('search'))
                                    <a href="{{ route('warehouse.sales.index', ['status' => request('status'), 'all' => request('all')]) }}" class="px-6 py-2.5 bg-gray-200 dark:bg-dark-bg hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all flex items-center gap-2">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                        إعادة تعيين
                                    </a>
                                @endif
                            </div>
                        </form>
                    </details>
                </div>

                @include('shared.sales._status-tabs', ['route' => fn($params) => route('warehouse.sales.index', $params)])

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-4 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            @forelse($invoices as $invoice)
                @include('shared.sales._invoice-card', [
                    'invoice' => $invoice,
                    'slot' => '<div class="flex items-center gap-2 mb-[7px]"><i data-lucide="calendar" class="w-4 h-4 text-gray-400"></i><span class="text-sm font-medium text-gray-600 dark:text-gray-400">' . $invoice->created_at->format('Y-m-d') . '</span></div>
                              <div class="flex items-center gap-2 mb-[7px]"><i data-lucide="user" class="w-4 h-4 text-gray-400"></i><span class="text-sm font-medium text-gray-600 dark:text-gray-400">' . $invoice->marketer->full_name . '</span></div>',
                    'storeSlot' => '<div class="flex items-center gap-2"><i data-lucide="store" class="w-4 h-4 text-gray-400"></i><span class="text-sm font-bold text-gray-700 dark:text-gray-300">' . $invoice->store->name . '</span></div>',
                    'actions' => '<a href="' . route('warehouse.sales.show', $invoice) . '" class="px-5 py-2.5 bg-white dark:bg-dark-card border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 dark:hover:border-gray-500 transition-all text-sm flex items-center gap-2 shadow-sm"><i data-lucide="eye" class="w-4 h-4"></i>التفاصيل</a>'
                ])
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="inbox" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد فواتير</h3>
                    <p class="text-gray-500 dark:text-dark-muted">لا توجد فواتير بيع في الوقت الحالي</p>
                </div>
            @endforelse

            @if($invoices->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
                    {{ $invoices->links() }}
                </div>
            @endif
                </div>
            </div>

            <div class="lg:col-span-4">
                @include('shared.sales._timeline-guide')
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
