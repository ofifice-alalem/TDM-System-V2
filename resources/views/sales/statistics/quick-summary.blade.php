@extends('layouts.app')

@section('title', 'الملخص المالي - خيارات سريعة')

@section('content')

<div class="min-h-screen py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Header --}}
        <div class="mb-6 sm:mb-8">
            <a href="{{ route('sales.statistics.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-4 transition-colors">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                <span class="font-bold">العودة للإحصائيات</span>
            </a>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">الملخص المالي للعملاء</h1>
                    <p class="text-gray-500 dark:text-dark-muted mt-1 text-sm">
                        من <span class="inline-block px-2.5 py-1 font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-xs">{{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }}</span>
                        إلى <span class="inline-block px-2.5 py-1 font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-xs">{{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}</span>
                    </p>
                </div>
                <a href="{{ route('sales.statistics.quick-summary', array_merge(request()->query(), ['export' => 1])) }}" class="w-full sm:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-md text-sm">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    تصدير Excel
                </a>
            </div>
        </div>

        {{-- Summary Cards --}}
        @php
            $sumInvoices = $customers->sum('total_invoices');
            $sumPayments = $customers->sum('total_payments');
            $sumReturns  = $customers->sum('total_returns');
            $sumDebt     = $customers->sum('total_debt');
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
            <div class="bg-white dark:bg-dark-card rounded-2xl p-4 border border-gray-200 dark:border-dark-border shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-blue-100 dark:bg-blue-500/10 rounded-lg flex items-center justify-center shrink-0">
                        <i data-lucide="file-text" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold">إجمالي الفواتير</p>
                </div>
                <p class="text-xl sm:text-2xl font-black text-blue-600 dark:text-blue-400">{{ number_format($sumInvoices, 0) }} <span class="text-xs text-gray-500 font-normal">د</span></p>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-2xl p-4 border border-gray-200 dark:border-dark-border shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-green-100 dark:bg-green-500/10 rounded-lg flex items-center justify-center shrink-0">
                        <i data-lucide="banknote" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold">إجمالي المدفوعات</p>
                </div>
                <p class="text-xl sm:text-2xl font-black text-green-600 dark:text-green-400">{{ number_format($sumPayments, 0) }} <span class="text-xs text-gray-500 font-normal">د</span></p>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-2xl p-4 border border-gray-200 dark:border-dark-border shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 bg-orange-100 dark:bg-orange-500/10 rounded-lg flex items-center justify-center shrink-0">
                        <i data-lucide="package-x" class="w-4 h-4 text-orange-600 dark:text-orange-400"></i>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold">إجمالي المرتجعات</p>
                </div>
                <p class="text-xl sm:text-2xl font-black text-orange-600 dark:text-orange-400">{{ number_format($sumReturns, 0) }} <span class="text-xs text-gray-500 font-normal">د</span></p>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-2xl p-4 border border-gray-200 dark:border-dark-border shadow-sm">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $sumDebt > 0 ? 'bg-red-100 dark:bg-red-500/10' : ($sumDebt < 0 ? 'bg-emerald-100 dark:bg-emerald-500/10' : 'bg-gray-100 dark:bg-gray-500/10') }}">
                        <i data-lucide="wallet" class="w-4 h-4 {{ $sumDebt > 0 ? 'text-red-600 dark:text-red-400' : ($sumDebt < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-400') }}"></i>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold">إجمالي الدين</p>
                </div>
                <p class="text-xl sm:text-2xl font-black {{ $sumDebt > 0 ? 'text-red-600 dark:text-red-400' : ($sumDebt < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-900 dark:text-white') }}">{{ number_format($sumDebt, 0) }} <span class="text-xs text-gray-500 font-normal">د</span></p>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl sm:rounded-3xl border border-gray-200 dark:border-dark-border shadow-lg overflow-hidden">
            <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-dark-border">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-purple-50 dark:bg-purple-500/10 rounded-xl flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
                    </div>
                    <h2 class="text-xl font-black text-gray-900 dark:text-white">الملخص المالي حسب العميل</h2>
                </div>
            </div>

            {{-- Desktop --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-l from-gray-50 to-gray-100 dark:from-dark-bg dark:to-gray-800/50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">العميل</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">إجمالي الفواتير</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">إجمالي المدفوعات</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">إجمالي المرتجعات</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الدين الحالي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-dark-border">
                        @forelse($customers as $index => $customer)
                        <tr class="hover:bg-purple-50/30 dark:hover:bg-purple-900/10 transition-all duration-200 {{ $index % 2 === 0 ? 'bg-white dark:bg-dark-card' : 'bg-gray-50/50 dark:bg-dark-bg/50' }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center shrink-0">
                                        <i data-lucide="user" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white">{{ $customer->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center min-w-[70px] px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg text-sm font-black">
                                    {{ number_format($customer->total_invoices, 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center min-w-[70px] px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg text-sm font-black">
                                    {{ number_format($customer->total_payments, 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center min-w-[70px] px-3 py-1.5 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded-lg text-sm font-black">
                                    {{ number_format($customer->total_returns, 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center min-w-[70px] px-3 py-1.5 rounded-lg text-sm font-black
                                    {{ $customer->total_debt > 0 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : ($customer->total_debt < 0 ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-400') }}">
                                    {{ number_format($customer->total_debt, 0) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="inbox" class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-3"></i>
                                    <p class="text-gray-500 dark:text-gray-400">لا توجد بيانات</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-200 dark:divide-dark-border">
                @forelse($customers as $customer)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center shrink-0">
                            <i data-lucide="user" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $customer->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->phone }}</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div class="bg-blue-50 dark:bg-blue-500/10 rounded-xl p-3 text-center">
                            <p class="text-xs text-blue-600 dark:text-blue-400 font-bold mb-1">الفواتير</p>
                            <p class="text-base font-black text-blue-700 dark:text-blue-300">{{ number_format($customer->total_invoices, 0) }}</p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-500/10 rounded-xl p-3 text-center">
                            <p class="text-xs text-green-600 dark:text-green-400 font-bold mb-1">المدفوعات</p>
                            <p class="text-base font-black text-green-700 dark:text-green-300">{{ number_format($customer->total_payments, 0) }}</p>
                        </div>
                        <div class="bg-orange-50 dark:bg-orange-500/10 rounded-xl p-3 text-center">
                            <p class="text-xs text-orange-600 dark:text-orange-400 font-bold mb-1">المرتجعات</p>
                            <p class="text-base font-black text-orange-700 dark:text-orange-300">{{ number_format($customer->total_returns, 0) }}</p>
                        </div>
                        <div class="rounded-xl p-3 text-center
                            {{ $customer->total_debt > 0 ? 'bg-red-50 dark:bg-red-500/10' : ($customer->total_debt < 0 ? 'bg-emerald-50 dark:bg-emerald-500/10' : 'bg-gray-50 dark:bg-gray-500/10') }}">
                            <p class="text-xs font-bold mb-1 {{ $customer->total_debt > 0 ? 'text-red-600 dark:text-red-400' : ($customer->total_debt < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-400') }}">الدين</p>
                            <p class="text-base font-black {{ $customer->total_debt > 0 ? 'text-red-700 dark:text-red-300' : ($customer->total_debt < 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-gray-700 dark:text-gray-300') }}">{{ number_format($customer->total_debt, 0) }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-12 text-center">
                    <div class="flex flex-col items-center">
                        <i data-lucide="inbox" class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-3"></i>
                        <p class="text-gray-500 dark:text-gray-400">لا توجد بيانات</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() { lucide.createIcons(); });
</script>
@endpush
@endsection
