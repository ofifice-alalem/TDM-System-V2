@extends('layouts.app')

@section('title', 'طلبات السحب')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
            <div class="lg:col-span-12">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-amber-100 dark:bg-amber-600/20 text-amber-600 dark:text-amber-400 px-3 py-1 rounded-lg text-xs font-bold border border-amber-100 dark:border-amber-600/30">
                        إدارة السحب
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    طلبات سحب الأرباح
                </h1>
            </div>
        </div>

        {{-- Withdrawals List --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Main List --}}
            <div class="lg:col-span-8">
                {{-- Filters --}}
                <div class="bg-white dark:bg-dark-card rounded-2xl p-4 md:p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border mb-6 animate-slide-up">
                    <form method="GET" action="{{ route('admin.withdrawals.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">رقم السحب</label>
                                <input type="number" name="withdrawal_id" value="{{ request('withdrawal_id') }}" placeholder="1" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">من تاريخ</label>
                                <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">إلى تاريخ</label>
                                <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">اسم المسوق</label>
                                <input type="text" name="marketer" value="{{ request('marketer') }}" placeholder="اسم المسوق" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm">
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                                <i data-lucide="filter" class="w-4 h-4"></i>
                                فلترة
                            </button>
                            <a href="{{ route('admin.withdrawals.index') }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                                <i data-lucide="x" class="w-4 h-4"></i>
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>

                @include('shared.withdrawals._status-tabs', ['route' => fn($params) => route('admin.withdrawals.index', $params)])

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-4 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            @forelse($withdrawals as $withdrawal)
                @include('shared.withdrawals._withdrawal-card', [
                    'withdrawal' => $withdrawal,
                    'viewRoute' => route('admin.withdrawals.show', $withdrawal)
                ])
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="wallet" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد طلبات سحب</h3>
                    <p class="text-gray-500 dark:text-dark-muted">لا توجد طلبات سحب في هذه الحالة</p>
                </div>
            @endforelse

            {{-- Pagination --}}
            @if($withdrawals->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
                    {{ $withdrawals->links() }}
                </div>
            @endif
                </div>
            </div>

            {{-- Timeline Guide --}}
            <div class="lg:col-span-4">
                @include('shared.withdrawals._timeline-guide')
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
