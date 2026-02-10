@extends('layouts.app')

@section('title', 'إيصالات القبض')

@section('content')

<div class="min-h-screen py-4 md:py-8">
    <div class="max-w-[1600px] mx-auto space-y-6 md:space-y-8 px-4 md:px-2">
        
        <div class="animate-fade-in-down">
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                    مراجعة التسديدات
                </span>
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                إيصالات القبض
            </h1>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">
            <div class="lg:col-span-8">
                {{-- Filters --}}
                <div class="bg-white dark:bg-dark-card rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-none border border-gray-200 dark:border-dark-border mb-6">
                    <form method="GET" action="{{ route('warehouse.payments.index') }}" class="flex flex-col md:flex-row gap-3">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <input type="hidden" name="all" value="{{ request('all') }}">
                        
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">رقم الإيصال</label>
                            <input type="text" name="payment_number" value="{{ request('payment_number') }}" placeholder="ابحث برقم الإيصال..." class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">من تاريخ</label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:[color-scheme:dark]">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">إلى تاريخ</label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:[color-scheme:dark]">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">مسوق أو متجر</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن مسوق أو متجر..." class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div class="flex gap-2 items-end">
                            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2 justify-center">
                                <i data-lucide="filter" class="w-4 h-4"></i>
                                فلترة
                            </button>
                            @if(request('payment_number') || request('from_date') || request('to_date') || request('search'))
                                <a href="{{ route('warehouse.payments.index', ['status' => request('status'), 'all' => request('all')]) }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2 justify-center">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                    إلغاء
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                @include('shared.payments._status-tabs', ['route' => fn($params) => route('warehouse.payments.index', $params)])

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-4 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            @forelse($payments as $payment)
                @include('shared.payments._payment-card', [
                    'payment' => $payment,
                    'slot' => '<div class="flex flex-wrap items-center gap-2"><div class="flex items-center gap-2 bg-blue-50 dark:bg-blue-900/20 px-3 py-1.5 rounded-lg border border-blue-100 dark:border-blue-800"><i data-lucide="store" class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400"></i><span class="text-xs font-bold text-blue-700 dark:text-blue-300">' . $payment->store->name . '</span></div><div class="flex items-center gap-2 bg-purple-50 dark:bg-purple-900/20 px-3 py-1.5 rounded-lg border border-purple-100 dark:border-purple-800"><i data-lucide="user" class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400"></i><span class="text-xs font-bold text-purple-700 dark:text-purple-300">' . $payment->marketer->full_name . '</span></div></div>',
                    'actions' => '<a href="' . route('warehouse.payments.show', $payment) . '" class="px-5 py-2.5 bg-white dark:bg-dark-card border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 dark:hover:border-gray-500 transition-all text-sm flex items-center gap-2 shadow-sm"><i data-lucide="eye" class="w-4 h-4"></i>التفاصيل</a>'
                ])
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="inbox" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد إيصالات</h3>
                    <p class="text-gray-500 dark:text-dark-muted">لا توجد إيصالات قبض للمراجعة</p>
                </div>
            @endforelse

            @if($payments->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
                    {{ $payments->links() }}
                </div>
            @endif
                </div>
            </div>

            <div class="lg:col-span-4">
                @include('shared.payments._timeline-guide')
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
