@extends('layouts.app')

@section('title', 'تفاصيل المسوق - ' . $marketer->full_name)

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto space-y-8 px-4 lg:px-8">
        
        <div class="animate-fade-in-down">
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.users.index') }}" class="w-10 h-10 bg-gray-100 dark:bg-dark-bg rounded-xl flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all">
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white">{{ $marketer->full_name }}</h1>
            </div>
            <p class="text-gray-600 dark:text-gray-400 mr-13">عرض الأرباح والعمولات وعمليات السحب</p>
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
                    <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-5 md:p-4 border border-gray-200 dark:border-dark-border hover:shadow-md transition-all">
                        <div class="flex flex-col gap-4">
                            <div class="flex items-start gap-3">
                                <div class="w-14 h-14 md:w-12 md:h-12 rounded-xl flex items-center justify-center shrink-0
                                    {{ $transaction['type'] === 'commission' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400' }}">
                                    <i data-lucide="{{ $transaction['type'] === 'commission' ? 'trending-up' : 'arrow-down-circle' }}" class="w-6 h-6 md:w-5 md:h-5"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    @if($transaction['type'] === 'commission')
                                        <h3 class="font-bold text-gray-900 dark:text-white mb-3 text-base md:text-base">عمولة من إيصال قبض</h3>
                                        <div class="flex flex-wrap items-center gap-2 text-xs">
                                            <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-3 py-1.5 rounded-lg font-bold text-xs">
                                                <i data-lucide="store" class="w-3.5 h-3.5 inline-block"></i>
                                                {{ $transaction['store_name'] }}
                                            </span>
                                            <span class="bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-3 py-1.5 rounded-lg font-bold text-xs">
                                                {{ $transaction['rate'] }}%
                                            </span>
                                            <span class="bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded-lg font-bold text-xs">
                                                من {{ number_format($transaction['payment_amount'], 2) }} دينار
                                            </span>
                                        </div>
                                    @else
                                        <h3 class="font-bold text-gray-900 dark:text-white mb-3 text-base md:text-base">عملية سحب</h3>
                                        <div class="flex items-center gap-2">
                                            <span class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 px-3 py-1.5 rounded-lg text-xs font-bold">موثق</span>
                                        </div>
                                    @endif
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">{{ \Carbon\Carbon::parse($transaction['date'])->format('Y-m-d h:i A') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-dark-border">
                                <div>
                                    <p class="text-2xl md:text-xl font-black {{ $transaction['type'] === 'commission' ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $transaction['type'] === 'commission' ? '+' : '-' }}{{ number_format($transaction['amount'], 2) }}
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">دينار</p>
                                </div>
                            </div>
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
