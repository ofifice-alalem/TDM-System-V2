@extends('layouts.app')

@section('title', 'فواتير المبيعات')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header & Quick Actions --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
            <div class="lg:col-span-8">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة الفواتير
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    فواتير المبيعات
                </h1>
            </div>

            <div class="lg:col-span-4 lg:translate-y-[30px] flex items-center gap-3">
                <div class="flex items-center gap-2 bg-white dark:bg-dark-card rounded-xl p-1 border border-gray-200 dark:border-dark-border">
                    <button onclick="setInvoiceView('card')" id="cardBtn" class="px-4 py-2 rounded-lg font-bold transition-all flex items-center gap-2">
                        <i data-lucide="layout-grid" class="w-4 h-4"></i>
                    </button>
                    <button onclick="setInvoiceView('table')" id="tableBtn" class="px-4 py-2 rounded-lg font-bold transition-all flex items-center gap-2">
                        <i data-lucide="table" class="w-4 h-4"></i>
                    </button>
                </div>
                <a href="{{ route('sales.invoices.create') }}" class="flex-1 px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    فاتورة جديدة
                </a>
            </div>
        </div>

        {{-- Filters --}}
        <div class="animate-fade-in">
            <details class="bg-white dark:bg-dark-card rounded-2xl shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border overflow-hidden">
                <summary class="px-6 py-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i data-lucide="filter" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                        <span class="font-bold text-gray-900 dark:text-white">فلترة متقدمة</span>
                        @if(request()->hasAny(['invoice_number', 'customer', 'date_from', 'date_to', 'amount_from', 'amount_to', 'status']))
                            <span class="px-2 py-1 bg-primary-100 dark:bg-primary-500/20 text-primary-600 dark:text-primary-400 rounded-lg text-xs font-bold">نشط</span>
                        @endif
                    </div>
                    <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400"></i>
                </summary>
                <form method="GET" class="p-6 border-t border-gray-200 dark:border-dark-border">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">رقم الفاتورة</label>
                            <input type="text" name="invoice_number" value="{{ request('invoice_number') }}" placeholder="ابحث..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">العميل</label>
                            <input type="text" name="customer" value="{{ request('customer') }}" placeholder="ابحث..." class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">من تاريخ</label>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all dark:[color-scheme:dark]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">إلى تاريخ</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all dark:[color-scheme:dark]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">من مبلغ</label>
                            <input type="number" name="amount_from" value="{{ request('amount_from') }}" placeholder="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">إلى مبلغ</label>
                            <input type="number" name="amount_to" value="{{ request('amount_to') }}" placeholder="0" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-2">الحالة</label>
                            <select name="status" class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all">
                                <option value="">الكل</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>مكتمل</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex gap-2 mt-6">
                        <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all flex items-center gap-2 shadow-md">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            بحث
                        </button>
                        @if(request()->hasAny(['invoice_number', 'customer', 'date_from', 'date_to', 'amount_from', 'amount_to', 'status']))
                            <a href="{{ route('sales.invoices.index') }}" class="px-6 py-2.5 bg-gray-200 dark:bg-dark-bg hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all flex items-center gap-2">
                                <i data-lucide="x" class="w-4 h-4"></i>
                                إعادة تعيين
                            </a>
                        @endif
                    </div>
                </form>
            </details>
        </div>

        {{-- Invoices List --}}
        <div id="cardView" class="bg-white dark:bg-dark-card rounded-[2rem] p-6 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            @forelse($invoices as $invoice)
                <div class="bg-gradient-to-br from-white to-gray-50 dark:from-dark-bg dark:to-dark-card rounded-2xl p-6 mb-4 border-2 border-gray-200 dark:border-dark-border hover:shadow-2xl hover:border-primary-300 dark:hover:border-primary-600/50 transition-all duration-300 group">
                    <div class="flex items-start gap-4 mb-5">
                        <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-600 dark:from-primary-600 dark:to-primary-700 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-200 dark:shadow-primary-900/30 group-hover:scale-110 transition-transform">
                            <i data-lucide="file-text" class="w-7 h-7 text-white"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2">#{{ $invoice->invoice_number }}</h3>
                            <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                    <i data-lucide="user" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <span class="font-bold">{{ $invoice->customer->name }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-4 border border-blue-200 dark:border-blue-700/30 shadow-sm">
                            <div class="flex items-center gap-2 text-xs text-blue-600 dark:text-blue-400 mb-2 font-bold">
                                <i data-lucide="banknote" class="w-3.5 h-3.5"></i>
                                المبلغ
                            </div>
                            <p class="text-lg font-black text-blue-700 dark:text-blue-300">{{ number_format($invoice->total_amount, 0) }} د.ع</p>
                        </div>
                        <div class="bg-white dark:bg-dark-card rounded-xl p-4 border-2 border-gray-200 dark:border-dark-border shadow-sm">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 font-bold">نوع الدفع</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-black shadow-sm
                                {{ $invoice->payment_type === 'cash' ? 'bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 text-green-700 dark:text-green-400 border border-green-300 dark:border-green-700/30' : '' }}
                                {{ $invoice->payment_type === 'credit' ? 'bg-gradient-to-br from-red-100 to-red-200 dark:from-red-900/30 dark:to-red-800/30 text-red-700 dark:text-red-400 border border-red-300 dark:border-red-700/30' : '' }}
                                {{ $invoice->payment_type === 'partial' ? 'bg-gradient-to-br from-yellow-100 to-yellow-200 dark:from-yellow-900/30 dark:to-yellow-800/30 text-yellow-700 dark:text-yellow-400 border border-yellow-300 dark:border-yellow-700/30' : '' }}">
                                <i data-lucide="{{ $invoice->payment_type === 'cash' ? 'wallet' : ($invoice->payment_type === 'credit' ? 'clock' : 'coins') }}" class="w-3.5 h-3.5"></i>
                                {{ $invoice->payment_type === 'cash' ? 'نقدي' : ($invoice->payment_type === 'credit' ? 'آجل' : 'جزئي') }}
                            </span>
                        </div>
                        <div class="bg-white dark:bg-dark-card rounded-xl p-4 border-2 border-gray-200 dark:border-dark-border shadow-sm">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 font-bold">الحالة</p>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-black shadow-sm
                                {{ $invoice->status === 'completed' ? 'bg-gradient-to-br from-green-100 to-green-200 dark:from-green-900/30 dark:to-green-800/30 text-green-700 dark:text-green-400 border border-green-300 dark:border-green-700/30' : 'bg-gradient-to-br from-red-100 to-red-200 dark:from-red-900/30 dark:to-red-800/30 text-red-700 dark:text-red-400 border border-red-300 dark:border-red-700/30' }}">
                                <span class="w-2 h-2 rounded-full {{ $invoice->status === 'completed' ? 'bg-green-500 animate-pulse' : 'bg-red-500' }}"></span>
                                {{ $invoice->status === 'completed' ? 'مكتمل' : 'ملغي' }}
                            </span>
                        </div>
                        <div class="bg-white dark:bg-dark-card rounded-xl p-4 border-2 border-gray-200 dark:border-dark-border shadow-sm">
                            <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-2 font-bold">
                                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                التاريخ
                            </div>
                            <p class="text-sm font-black text-gray-900 dark:text-white">{{ $invoice->created_at->format('Y-m-d') }}</p>
                        </div>
                    </div>

                    <a href="{{ route('sales.invoices.show', $invoice) }}" class="block w-full px-5 py-3 bg-white dark:bg-dark-card hover:bg-primary-50 dark:hover:bg-primary-900/20 text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 rounded-xl font-black transition-all text-center flex items-center justify-center gap-2 border-2 border-gray-300 dark:border-gray-600 hover:border-primary-500 dark:hover:border-primary-500 shadow-sm hover:shadow-md">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                        عرض التفاصيل
                    </a>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="file-text" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد فواتير</h3>
                    <p class="text-gray-500 dark:text-dark-muted mb-6">لم تقم بإنشاء أي فواتير بعد</p>
                    <a href="{{ route('sales.invoices.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        إنشاء فاتورة جديدة
                    </a>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if($invoices->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
                    {{ $invoices->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        {{-- Table View --}}
        <div id="tableView" class="bg-white dark:bg-dark-card rounded-2xl shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up hidden overflow-hidden">
            @if($invoices->count() > 0)
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">رقم الفاتورة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">العميل</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">المبلغ</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">نوع الدفع</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">الحالة</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">التاريخ</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                    @foreach($invoices as $invoice)
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-600/20 rounded-lg flex items-center justify-center">
                                    <i data-lucide="file-text" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                                </div>
                                <span class="font-bold text-gray-900 dark:text-white">#{{ $invoice->invoice_number }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $invoice->customer->name }}</td>
                        <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-white">{{ number_format($invoice->total_amount, 0) }} دينار</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold 
                                {{ $invoice->payment_type === 'cash' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                {{ $invoice->payment_type === 'credit' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                {{ $invoice->payment_type === 'partial' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}">
                                {{ $invoice->payment_type === 'cash' ? 'نقدي' : ($invoice->payment_type === 'credit' ? 'آجل' : 'جزئي') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-bold {{ $invoice->status === 'completed' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $invoice->status === 'completed' ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                {{ $invoice->status === 'completed' ? 'مكتمل' : 'ملغي' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">{{ $invoice->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center">
                                <a href="{{ route('sales.invoices.show', $invoice) }}" class="w-9 h-9 bg-primary-600 hover:bg-primary-700 text-white rounded-lg flex items-center justify-center transition-all">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="file-text" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد فواتير</h3>
                <p class="text-gray-500 dark:text-dark-muted">لم تقم بإنشاء أي فواتير بعد</p>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        
        const view = localStorage.getItem('invoicesView') || 'card';
        setInvoiceView(view);
    });
    
    function setInvoiceView(view) {
        const cardView = document.getElementById('cardView');
        const tableView = document.getElementById('tableView');
        const cardBtn = document.getElementById('cardBtn');
        const tableBtn = document.getElementById('tableBtn');
        
        if (view === 'card') {
            cardView.classList.remove('hidden');
            tableView.classList.add('hidden');
            cardBtn.classList.add('bg-primary-600', 'text-white');
            cardBtn.classList.remove('text-gray-600', 'dark:text-gray-400');
            tableBtn.classList.remove('bg-primary-600', 'text-white');
            tableBtn.classList.add('text-gray-600', 'dark:text-gray-400');
        } else {
            cardView.classList.add('hidden');
            tableView.classList.remove('hidden');
            tableBtn.classList.add('bg-primary-600', 'text-white');
            tableBtn.classList.remove('text-gray-600', 'dark:text-gray-400');
            cardBtn.classList.remove('bg-primary-600', 'text-white');
            cardBtn.classList.add('text-gray-600', 'dark:text-gray-400');
        }
        
        localStorage.setItem('invoicesView', view);
        lucide.createIcons();
    }
</script>
@endpush
@endsection
