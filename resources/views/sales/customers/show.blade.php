@extends('layouts.app')

@section('title', 'تفاصيل العميل')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto px-4">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('sales.customers.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </a>
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        تفاصيل العميل
                    </span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white">{{ $customer->name }}</h1>
            </div>
            <a href="{{ route('sales.customers.edit', $customer) }}" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                <i data-lucide="edit" class="w-4 h-4"></i>
                تعديل
            </a>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-600/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="file-text" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-dark-muted">إجمالي الفواتير</p>
                        <p class="text-lg font-bold text-blue-600">{{ number_format($totalInvoices, 0) }} دينار</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-600/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="banknote" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-dark-muted">إجمالي الدفع</p>
                        <p class="text-lg font-bold text-green-600">{{ number_format($totalPayments, 0) }} دينار</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-600/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="undo-2" class="w-6 h-6 text-orange-600 dark:text-orange-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-dark-muted">إجمالي المرتجعات</p>
                        <p class="text-lg font-bold text-orange-600">{{ number_format($totalReturns, 0) }} دينار</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-12 h-12 bg-red-100 dark:bg-red-600/20 rounded-xl flex items-center justify-center">
                        <i data-lucide="wallet" class="w-6 h-6 text-red-600 dark:text-red-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-dark-muted">الدين الحالي</p>
                        <p class="text-lg font-bold {{ $totalDebt > 0 ? 'text-red-600' : 'text-green-600' }}">{{ number_format($totalDebt, 0) }} دينار</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Invoices --}}
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border mb-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">آخر الفواتير</h3>
                    @forelse($customer->invoices as $invoice)
                        <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-4 mb-3 border border-gray-200 dark:border-dark-border">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white">#{{ $invoice->invoice_number }}</p>
                                    <p class="text-sm text-gray-500 dark:text-dark-muted">{{ $invoice->created_at->format('Y-m-d') }}</p>
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-gray-900 dark:text-white">{{ number_format($invoice->total_amount, 0) }} د.ع</p>
                                    <span class="text-xs px-2 py-1 rounded-lg {{ $invoice->payment_type === 'cash' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $invoice->payment_type === 'cash' ? 'نقدي' : 'آجل' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-dark-muted py-8">لا توجد فواتير</p>
                    @endforelse
                </div>

                <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">حركات الحساب</h3>
                    @forelse($customer->debtLedger as $entry)
                        <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-dark-border last:border-0">
                            <div>
                                <p class="font-bold text-gray-900 dark:text-white">
                                    @if($entry->entry_type === 'sale') بيع
                                    @elseif($entry->entry_type === 'payment') دفعة
                                    @else إرجاع
                                    @endif
                                </p>
                                <p class="text-sm text-gray-500 dark:text-dark-muted">{{ $entry->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                            <p class="font-bold {{ $entry->amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ $entry->amount > 0 ? '+' : '' }}{{ number_format($entry->amount, 0) }} د.ع
                            </p>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-dark-muted py-8">لا توجد حركات</p>
                    @endforelse
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border mb-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">معلومات إضافية</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-500 dark:text-dark-muted">العنوان</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $customer->address ?? 'غير محدد' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-dark-muted">رقم الهوية</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $customer->id_number ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">إجراءات سريعة</h3>
                    <div class="space-y-3">
                        <a href="{{ route('sales.invoices.create', ['customer_id' => $customer->id]) }}" class="block w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white text-center rounded-xl font-bold transition-all">
                            فاتورة جديدة
                        </a>
                        @if($totalDebt > 0)
                            <a href="{{ route('sales.payments.create', ['customer_id' => $customer->id]) }}" class="block w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white text-center rounded-xl font-bold transition-all">
                                تسديد دفعة
                            </a>
                        @endif
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
