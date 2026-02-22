@extends('layouts.app')

@section('title', 'إيصالات القبض')

@section('content')

<div class="min-h-screen py-4 md:py-8">
    <div class="max-w-[1600px] mx-auto space-y-6 md:space-y-8 px-4 md:px-2">
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
            <div class="lg:col-span-8">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة التسديدات
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    إيصالات القبض
                </h1>
            </div>

            <div class="lg:col-span-4">
                <a href="{{ route('marketer.payments.create') }}" class="px-6 md:px-8 py-3 md:py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2 w-full">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    إيصال جديد
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">
            <div class="lg:col-span-8">
                {{-- Filters --}}
                <details class="bg-white dark:bg-dark-card rounded-2xl shadow-lg shadow-gray-200/50 dark:shadow-none border border-gray-200 dark:border-dark-border mb-6">
                    <summary class="px-4 py-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-dark-bg/50 transition-colors rounded-2xl flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i data-lucide="filter" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                            <span class="font-bold text-gray-900 dark:text-white">فلترة متقدمة</span>
                            @if(request('payment_number') || request('from_date') || request('to_date') || request('store'))
                                <span class="px-2 py-0.5 bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 text-xs font-bold rounded-full">نشط</span>
                            @endif
                        </div>
                        <i data-lucide="chevron-down" class="w-5 h-5 text-gray-400 transition-transform"></i>
                    </summary>
                    <form method="GET" action="{{ route('marketer.payments.index') }}" class="p-4 pt-2">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <input type="hidden" name="all" value="{{ request('all') }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">رقم الإيصال</label>
                                <input type="text" name="payment_number" value="{{ request('payment_number') }}" placeholder="ابحث برقم الإيصال..." class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">من تاريخ</label>
                                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:[color-scheme:dark]">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">إلى تاريخ</label>
                                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:[color-scheme:dark]">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">المتجر</label>
                                <input type="text" name="store" value="{{ request('store') }}" placeholder="ابحث عن متجر..." class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2 justify-center">
                                <i data-lucide="search" class="w-4 h-4"></i>
                                بحث
                            </button>
                            @if(request('payment_number') || request('from_date') || request('to_date') || request('store'))
                                <a href="{{ route('marketer.payments.index', ['status' => request('status'), 'all' => request('all')]) }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2 justify-center">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                    إعادة تعيين
                                </a>
                            @endif
                        </div>
                    </form>
                </details>

                @include('shared.payments._status-tabs', ['route' => fn($params) => route('marketer.payments.index', $params)])

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-4 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            @forelse($payments as $payment)
                @include('shared.payments._payment-card', [
                    'payment' => $payment,
                    'slot' => '<div class="flex items-center gap-2 bg-blue-50 dark:bg-blue-900/20 px-3 py-2 rounded-lg border border-blue-100 dark:border-blue-800 w-fit"><i data-lucide="store" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i><span class="text-sm font-bold text-blue-700 dark:text-blue-300">' . $payment->store->name . '</span></div>',
                    'actions' => '<a href="' . route('marketer.payments.show', $payment) . '" class="px-5 py-2.5 bg-white dark:bg-dark-card border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-gray-700 hover:border-gray-400 dark:hover:border-gray-500 transition-all text-sm flex items-center gap-2 shadow-sm"><i data-lucide="eye" class="w-4 h-4"></i>التفاصيل</a>'
                ])
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="inbox" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد إيصالات</h3>
                    <p class="text-gray-500 dark:text-dark-muted mb-6">لم تقم بإنشاء أي إيصالات قبض بعد</p>
                    <a href="{{ route('marketer.payments.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all">
                        <i data-lucide="plus-circle" class="w-5 h-5"></i>
                        إنشاء إيصال جديد
                    </a>
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
                <div class="bg-white dark:bg-dark-card rounded-2xl md:rounded-[2rem] p-4 md:p-6 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border mb-4 md:mb-6 animate-slide-up">
                    <div class="flex items-center gap-3 mb-3 md:mb-4">
                        <span class="bg-emerald-50 dark:bg-emerald-900/20 p-2 md:p-2.5 rounded-xl text-emerald-600 dark:text-emerald-400 shadow-sm border border-emerald-100 dark:border-emerald-600/30">
                            <i data-lucide="percent" class="w-4 h-4 md:w-5 md:h-5"></i>
                        </span>
                        <h3 class="font-bold text-base md:text-lg text-gray-900 dark:text-white">نسبة العمولة</h3>
                    </div>
                    <div class="text-center py-4 md:py-6">
                        <div class="text-4xl md:text-5xl font-black text-emerald-600 dark:text-emerald-400 mb-2">
                            {{ number_format(auth()->user()->commission_rate ?? 0, 1) }}%
                        </div>
                        <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400">من كل تسديد موثق</p>
                    </div>
                </div>

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
