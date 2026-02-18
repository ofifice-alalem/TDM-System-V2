@extends('layouts.app')

@section('title', 'تفاصيل العميل - ' . $customer->name)

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Back Button & Header --}}
        <div class="animate-fade-in-down">
            <a href="{{ route('sales.customers.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-4 transition-colors">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                <span class="font-bold">العودة للعملاء</span>
            </a>
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            {{-- Right Content - Transactions History --}}
            <div class="lg:col-span-8 order-2 lg:order-1">
                <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up" style="animation-delay: 0.2s">
                    
                    {{-- Customer Header --}}
                    <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-primary-500 dark:bg-primary-600 rounded-xl flex items-center justify-center text-white">
                                <i data-lucide="user" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $customer->name }}</h1>
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
                        @forelse($customer->debtLedger as $entry)
                            <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-4 hover:shadow-md transition-all border border-gray-100 dark:border-dark-border group">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                                    <div class="flex items-start gap-3 flex-1">
                                        <div class="w-12 h-12 shrink-0 rounded-xl flex items-center justify-center
                                            {{ $entry->entry_type === 'sale' ? 'bg-red-100 dark:bg-red-500/10 text-red-600 dark:text-red-400' : '' }}
                                            {{ $entry->entry_type === 'payment' ? 'bg-emerald-100 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400' : '' }}
                                            {{ $entry->entry_type === 'return' ? 'bg-orange-100 dark:bg-orange-500/10 text-orange-600 dark:text-orange-400' : '' }}
                                        ">
                                            @if($entry->entry_type === 'sale')
                                                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                                            @elseif($entry->entry_type === 'payment')
                                                <i data-lucide="banknote" class="w-5 h-5"></i>
                                            @else
                                                <i data-lucide="package-x" class="w-5 h-5"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                                <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">
                                                    @if($entry->entry_type === 'sale')
                                                        @if(isset($entry->is_cancellation) && $entry->is_cancellation)
                                                            إلغاء فاتورة مبيعات
                                                        @else
                                                            فاتورة مبيعات
                                                        @endif
                                                    @elseif($entry->entry_type === 'payment')
                                                        إيصال قبض
                                                    @else
                                                        مرتجعات
                                                    @endif
                                                </h3>
                                            </div>
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                                <span class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded flex items-center gap-1.5">
                                                    <i data-lucide="calendar" class="w-4 h-4"></i>
                                                    {{ $entry->created_at->format('d M Y H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between sm:justify-end gap-3 pt-3 sm:pt-0 border-t sm:border-t-0 border-gray-200 dark:border-gray-700">
                                        <div class="text-right">
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">المبلغ</div>
                                            <div class="text-base sm:text-lg font-black
                                                {{ $entry->amount > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}
                                            ">
                                                {{ $entry->amount > 0 ? '+' : '' }}{{ number_format($entry->amount, 0) }} دينار
                                            </div>
                                        </div>
                                        @if($entry->entry_type === 'sale' && $entry->invoice_id)
                                            <a href="{{ route('sales.invoices.show', $entry->invoice_id) }}" class="text-orange-500 hover:text-orange-600 transition-colors border-2 border-orange-500 hover:border-orange-600 rounded-lg p-1 mr-3">
                                                <i data-lucide="chevron-left" class="w-6 h-6" stroke-width="3"></i>
                                            </a>
                                        @elseif($entry->entry_type === 'payment' && $entry->payment_id)
                                            <a href="{{ route('sales.payments.show', $entry->payment_id) }}" class="text-orange-500 hover:text-orange-600 transition-colors border-2 border-orange-500 hover:border-orange-600 rounded-lg p-1 mr-3">
                                                <i data-lucide="chevron-left" class="w-6 h-6" stroke-width="3"></i>
                                            </a>
                                        @elseif($entry->entry_type === 'return' && $entry->return_id)
                                            <a href="{{ route('sales.returns.show', $entry->return_id) }}" class="text-orange-500 hover:text-orange-600 transition-colors border-2 border-orange-500 hover:border-orange-600 rounded-lg p-1 mr-3">
                                                <i data-lucide="chevron-left" class="w-6 h-6" stroke-width="3"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="inbox" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد حركات</h3>
                                <p class="text-gray-500 dark:text-dark-muted">لم يتم تسجيل أي حركات مالية لهذا العميل</p>
                            </div>
                        @endforelse
                    </div>

                </div>
            </div>

            {{-- Left Sidebar - Customer Info --}}
            <div class="lg:col-span-4 space-y-6 order-1 lg:order-2">
                
                {{-- Customer Details Card --}}
                <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center text-primary-600 dark:text-primary-400">
                            <i data-lucide="info" class="w-5 h-5"></i>
                        </div>
                        <h2 class="text-xl font-black text-gray-900 dark:text-white">بيانات العميل</h2>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-3 border-b border-gray-100 dark:border-dark-border">
                            <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                <i data-lucide="phone" class="w-4 h-4"></i>
                                رقم التواصل
                            </span>
                            <a href="tel:{{ $customer->phone }}" class="text-sm font-bold text-primary-600 dark:text-primary-400 hover:underline">{{ $customer->phone }}</a>
                        </div>

                        @if($customer->address)
                        <div class="py-3 border-b border-gray-100 dark:border-dark-border">
                            <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 mb-2">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                العنوان
                            </span>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $customer->address }}</p>
                        </div>
                        @endif

                        @if($customer->id_number)
                        <div class="py-3 border-b border-gray-100 dark:border-dark-border">
                            <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2 mb-2">
                                <i data-lucide="credit-card" class="w-4 h-4"></i>
                                رقم الهوية
                            </span>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $customer->id_number }}</p>
                        </div>
                        @endif

                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm text-gray-500 dark:text-gray-400">الحالة</span>
                            @if($customer->is_active)
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

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('sales.customers.edit', $customer) }}" class="w-full px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white text-center rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                            <i data-lucide="edit" class="w-4 h-4"></i>
                            تعديل البيانات
                        </a>
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
                            <div class="text-xs text-blue-600 dark:text-blue-400 mb-1">إجمالي الفواتير</div>
                            <div class="text-xl font-black text-blue-700 dark:text-blue-300">{{ number_format($totalInvoices, 0) }} دينار</div>
                        </div>

                        <div class="bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl p-4">
                            <div class="text-xs text-emerald-600 dark:text-emerald-400 mb-1">إجمالي المدفوعات</div>
                            <div class="text-xl font-black text-emerald-700 dark:text-emerald-300">{{ number_format($totalPayments, 0) }} دينار</div>
                        </div>

                        <div class="bg-orange-50 dark:bg-orange-500/10 rounded-2xl p-4">
                            <div class="text-xs text-orange-600 dark:text-orange-400 mb-1">إجمالي المرتجعات</div>
                            <div class="text-xl font-black text-orange-700 dark:text-orange-300">{{ number_format($totalReturns, 0) }} دينار</div>
                        </div>

                        <div class="bg-gradient-to-br from-{{ $totalDebt > 0 ? 'red' : ($totalDebt < 0 ? 'emerald' : 'gray') }}-100 to-{{ $totalDebt > 0 ? 'red' : ($totalDebt < 0 ? 'emerald' : 'gray') }}-200 dark:from-{{ $totalDebt > 0 ? 'red' : ($totalDebt < 0 ? 'emerald' : 'gray') }}-900/40 dark:to-{{ $totalDebt > 0 ? 'red' : ($totalDebt < 0 ? 'emerald' : 'gray') }}-800/40 rounded-2xl p-5 border-2 border-{{ $totalDebt > 0 ? 'red' : ($totalDebt < 0 ? 'emerald' : 'gray') }}-200 dark:border-{{ $totalDebt > 0 ? 'red' : ($totalDebt < 0 ? 'emerald' : 'gray') }}-700">
                            <div class="text-xs text-{{ $totalDebt > 0 ? 'red' : ($totalDebt < 0 ? 'emerald' : 'gray') }}-700 dark:text-{{ $totalDebt > 0 ? 'red' : ($totalDebt < 0 ? 'emerald' : 'gray') }}-300 mb-1 font-bold">الدين الحالي</div>
                            <div class="text-2xl font-black text-{{ $totalDebt > 0 ? 'red' : ($totalDebt < 0 ? 'emerald' : 'gray') }}-800 dark:text-{{ $totalDebt > 0 ? 'red' : ($totalDebt < 0 ? 'emerald' : 'gray') }}-200">
                                {{ number_format($totalDebt, 0) }} دينار
                            </div>
                        </div>
                    </div>

                    @if($totalDebt > 0)
                    <div class="mt-4">
                        <a href="{{ route('sales.payments.create', ['customer_id' => $customer->id]) }}" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white text-center rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                            <i data-lucide="banknote" class="w-4 h-4"></i>
                            تسديد دفعة
                        </a>
                    </div>
                    @endif
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
