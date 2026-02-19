@extends('layouts.app')

@section('title', 'الفواتير - خيارات سريعة')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4">
        
        <div class="mb-8">
            <a href="{{ route('sales.statistics.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-4 transition-colors">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                <span class="font-bold">العودة للإحصائيات</span>
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white">الفواتير المكتملة</h1>
                    <p class="text-gray-500 dark:text-dark-muted mt-1">من <span class="inline-block px-3 py-1.5 font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm">{{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }}</span> إلى <span class="inline-block px-3 py-1.5 font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm">{{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}</span></p>
                </div>
                <a href="{{ route('sales.statistics.quick-invoices', array_merge(request()->query(), ['export' => 1])) }}" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all flex items-center gap-2 shadow-md">
                    <i data-lucide="download" class="w-5 h-5"></i>
                    تصدير Excel
                </a>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
            <div class="bg-white dark:bg-dark-card rounded-xl p-5 border border-gray-200 dark:border-dark-border shadow-sm hover:shadow-md transition-shadow">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">إجمالي عدد الفواتير</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalInvoices) }}</p>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-xl p-5 border border-gray-200 dark:border-dark-border shadow-sm hover:shadow-md transition-shadow">
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">إجمالي المبلغ</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($totalAmount, 0) }} <span class="text-lg text-gray-500">دينار</span></p>
            </div>
        </div>

        {{-- Customers Table --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-dark-border">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">الفواتير حسب العميل</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">العميل</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">عدد الفواتير</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">إجمالي المبلغ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                        @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                        <i data-lucide="user" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white">{{ $customer->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg text-sm font-black">
                                    {{ number_format($customer->invoices_count) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-lg text-green-600 dark:text-green-400">
                                {{ number_format($customer->invoices_total ?? 0, 0) }} دينار
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="inbox" class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-3"></i>
                                    <p class="text-gray-500 dark:text-gray-400">لا توجد فواتير مكتملة</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
