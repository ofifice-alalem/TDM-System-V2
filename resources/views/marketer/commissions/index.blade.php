@extends('layouts.app')

@section('title', 'أرباحي')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto space-y-8 px-4 lg:px-8">
        
        <div class="animate-fade-in-down">
            <h1 class="text-4xl font-black text-gray-900 dark:text-white mb-2">أرباحي</h1>
            <p class="text-gray-600 dark:text-gray-400">عرض الأرباح والعمولات وعمليات السحب</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-slide-up">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/30 dark:to-blue-800/30 rounded-3xl p-6 border-2 border-blue-200 dark:border-blue-700">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center text-white">
                        <i data-lucide="trending-up" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-sm font-bold text-blue-700 dark:text-blue-300">إجمالي الأرباح</h3>
                </div>
                <p class="text-3xl font-black text-blue-900 dark:text-blue-100">{{ number_format($totalCommissions, 2) }}</p>
                <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">دينار</p>
            </div>

            <div class="bg-gradient-to-br from-red-50 to-red-100 dark:from-red-900/30 dark:to-red-800/30 rounded-3xl p-6 border-2 border-red-200 dark:border-red-700">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center text-white">
                        <i data-lucide="arrow-down-circle" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-sm font-bold text-red-700 dark:text-red-300">إجمالي المسحوب</h3>
                </div>
                <p class="text-3xl font-black text-red-900 dark:text-red-100">{{ number_format($totalWithdrawals, 2) }}</p>
                <p class="text-xs text-red-600 dark:text-red-400 mt-1">دينار</p>
            </div>

            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/30 dark:to-emerald-800/30 rounded-3xl p-6 border-2 border-emerald-200 dark:border-emerald-700">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center text-white">
                        <i data-lucide="wallet" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-sm font-bold text-emerald-700 dark:text-emerald-300">الرصيد المستحق</h3>
                </div>
                <p class="text-3xl font-black text-emerald-900 dark:text-emerald-100">{{ number_format($availableBalance, 2) }}</p>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1">دينار</p>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-card rounded-3xl p-6 md:p-8 shadow-xl border border-gray-200 dark:border-dark-border animate-slide-up" style="animation-delay: 0.1s">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-primary-50 dark:bg-primary-900/30 rounded-xl flex items-center justify-center text-primary-600 dark:text-primary-400">
                    <i data-lucide="list" class="w-5 h-5"></i>
                </div>
                <h2 class="text-2xl font-black text-gray-900 dark:text-white">آخر المعاملات</h2>
            </div>

            <div class="space-y-3">
                @forelse($recentTransactions as $transaction)
                    <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-4 border border-gray-200 dark:border-dark-border hover:shadow-md transition-all">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center shrink-0
                                    {{ $transaction['type'] === 'commission' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' }}">
                                    <i data-lucide="{{ $transaction['type'] === 'commission' ? 'trending-up' : 'arrow-down-circle' }}" class="w-5 h-5"></i>
                                </div>
                                <div class="flex-1">
                                    @if($transaction['type'] === 'commission')
                                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">عمولة من إيصال قبض</h3>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $transaction['store_name'] }} • {{ $transaction['rate'] }}% من {{ number_format($transaction['payment_amount'], 2) }} دينار</p>
                                    @else
                                        <h3 class="font-bold text-gray-900 dark:text-white mb-1">عملية سحب</h3>
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            الحالة: 
                                            @if($transaction['status'] === 'approved') <span class="text-emerald-600">موثق</span>
                                            @elseif($transaction['status'] === 'pending') <span class="text-amber-600">قيد الانتظار</span>
                                            @else <span class="text-red-600">مرفوض</span>
                                            @endif
                                        </p>
                                    @endif
                                    <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">{{ $transaction['date']->format('Y-m-d h:i A') }}</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="text-xl font-black {{ $transaction['type'] === 'commission' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $transaction['type'] === 'commission' ? '+' : '-' }}{{ number_format($transaction['amount'], 2) }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">دينار</p>
                            </div>
                            @if($transaction['type'] === 'commission' && $transaction['payment_id'])
                                <a href="{{ route('marketer.payments.show', $transaction['payment_id']) }}" class="w-10 h-10 bg-gray-100 dark:bg-dark-bg rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all">
                                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                            <i data-lucide="inbox" class="w-10 h-10 text-gray-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد معاملات</h3>
                        <p class="text-gray-500 dark:text-gray-400">لم يتم تسجيل أي معاملات حتى الآن</p>
                    </div>
                @endforelse
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
