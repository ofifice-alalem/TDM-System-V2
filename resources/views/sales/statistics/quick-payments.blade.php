@extends('layouts.app')

@section('title', 'المدفوعات - خيارات سريعة')

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
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white">المدفوعات المكتملة</h1>
                    <p class="text-gray-500 dark:text-dark-muted mt-1">من <span class="inline-block px-3 py-1.5 font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm">{{ \Carbon\Carbon::parse($fromDate)->format('d/m/Y') }}</span> إلى <span class="inline-block px-3 py-1.5 font-bold text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm">{{ \Carbon\Carbon::parse($toDate)->format('d/m/Y') }}</span></p>
                </div>
                <a href="{{ route('sales.statistics.quick-payments', array_merge(request()->query(), ['export' => 1])) }}" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all flex items-center gap-2 shadow-md">
                    <i data-lucide="download" class="w-5 h-5"></i>
                    تصدير Excel
                </a>
            </div>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white dark:bg-dark-card rounded-xl p-4 border border-gray-200 dark:border-dark-border shadow-sm hover:shadow-md transition-shadow">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">نقدي</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($cashTotal, 0) }} <span class="text-sm text-gray-500">د</span></p>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-xl p-4 border border-gray-200 dark:border-dark-border shadow-sm hover:shadow-md transition-shadow">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">تحويل</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($transferTotal, 0) }} <span class="text-sm text-gray-500">د</span></p>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-xl p-4 border border-gray-200 dark:border-dark-border shadow-sm hover:shadow-md transition-shadow">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">شيك</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($checkTotal, 0) }} <span class="text-sm text-gray-500">د</span></p>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-xl p-4 border border-gray-200 dark:border-dark-border shadow-sm hover:shadow-md transition-shadow">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">الإجمالي</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($totalAmount, 0) }} <span class="text-sm text-gray-500">د</span></p>
            </div>
        </div>

        {{-- Customers Table --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200 dark:border-dark-border">
                <h2 class="text-xl font-black text-gray-900 dark:text-white">المدفوعات حسب العميل</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">العميل</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">نقدي</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">تحويل</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">شيك</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                        @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                        <i data-lucide="user" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white">{{ $customer->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $customer->phone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg text-sm font-black">
                                    {{ number_format($customer->cash_total, 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg text-sm font-black">
                                    {{ number_format($customer->transfer_total, 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-3 py-1.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg text-sm font-black">
                                    {{ number_format($customer->check_total, 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-black text-lg text-purple-600 dark:text-purple-400">
                                {{ number_format($customer->total_payments, 0) }} دينار
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="inbox" class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-3"></i>
                                    <p class="text-gray-500 dark:text-gray-400">لا توجد مدفوعات مكتملة</p>
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
