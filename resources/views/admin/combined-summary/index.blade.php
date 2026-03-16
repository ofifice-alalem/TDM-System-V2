@extends('layouts.app')
@section('title', 'الملخص المالي الشامل')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-[1400px] mx-auto space-y-6 px-2">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">إدارة النظام</span>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white mt-2">الملخص المالي الشامل</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">متاجر وعملاء معاً</p>
            </div>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.combined-summary.index') }}" class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-5 shadow-sm">
            <div class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">من تاريخ</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}"
                        class="bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm [color-scheme:light] dark:[color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">إلى تاريخ</label>
                    <input type="date" name="to_date" value="{{ $toDate }}"
                        class="bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm [color-scheme:light] dark:[color-scheme:dark]">
                </div>
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                    <i data-lucide="search" class="w-4 h-4"></i> عرض
                </button>
                <a href="{{ route('admin.combined-summary.index', array_merge(request()->query(), ['export' => 1])) }}"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i> تصدير Excel
                </a>
            </div>
        </form>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-5 shadow-sm">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">إجمالي الفواتير</p>
                <p class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ number_format($grandInvoices, 2) }}</p>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-5 shadow-sm">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">إجمالي المدفوعات</p>
                <p class="text-2xl font-black text-green-600 dark:text-green-400">{{ number_format($grandPayments, 2) }}</p>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-5 shadow-sm">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">إجمالي المرتجعات</p>
                <p class="text-2xl font-black text-orange-600 dark:text-orange-400">{{ number_format($grandReturns, 2) }}</p>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-5 shadow-sm">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">إجمالي الدين</p>
                <p class="text-2xl font-black {{ $grandDebt > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">{{ number_format($grandDebt, 2) }}</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-sm overflow-hidden">
            @if($rows->isEmpty())
                <div class="text-center py-16">
                    <i data-lucide="file-x" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                    <p class="text-gray-500 dark:text-gray-400 font-bold">لا توجد بيانات في هذه الفترة</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                                <th class="px-4 py-3 text-right font-bold text-gray-700 dark:text-gray-300">#</th>
                                <th class="px-4 py-3 text-right font-bold text-gray-700 dark:text-gray-300">الاسم</th>
                                <th class="px-4 py-3 text-center font-bold text-gray-700 dark:text-gray-300">النوع</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-gray-300">إجمالي الفواتير</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-gray-300">إجمالي المدفوعات</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-gray-300">إجمالي المرتجعات</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-gray-300">الدين الحالي</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-dark-border">
                            @foreach($rows as $i => $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg/50 transition-colors">
                                    <td class="px-4 py-3 text-gray-400 dark:text-gray-500 text-xs">{{ $i + 1 }}</td>
                                    <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ $row->name }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2.5 py-1 rounded-lg text-xs font-bold {{ $row->type === 'متجر' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' }}">
                                            {{ $row->type }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-left font-mono text-gray-700 dark:text-gray-300">{{ number_format($row->total_invoices, 2) }}</td>
                                    <td class="px-4 py-3 text-left font-mono text-green-600 dark:text-green-400">{{ number_format($row->total_payments, 2) }}</td>
                                    <td class="px-4 py-3 text-left font-mono text-orange-600 dark:text-orange-400">{{ number_format($row->total_returns, 2) }}</td>
                                    <td class="px-4 py-3 text-left font-mono font-bold {{ $row->total_debt > 0 ? 'text-red-600 dark:text-red-400' : ($row->total_debt < 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400') }}">
                                        {{ number_format($row->total_debt, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 dark:bg-dark-bg border-t-2 border-gray-300 dark:border-dark-border">
                                <td colspan="3" class="px-4 py-3 font-black text-gray-900 dark:text-white">الإجمالي</td>
                                <td class="px-4 py-3 text-left font-black font-mono text-blue-600 dark:text-blue-400">{{ number_format($grandInvoices, 2) }}</td>
                                <td class="px-4 py-3 text-left font-black font-mono text-green-600 dark:text-green-400">{{ number_format($grandPayments, 2) }}</td>
                                <td class="px-4 py-3 text-left font-black font-mono text-orange-600 dark:text-orange-400">{{ number_format($grandReturns, 2) }}</td>
                                <td class="px-4 py-3 text-left font-black font-mono {{ $grandDebt > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">{{ number_format($grandDebt, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

    </div>
</div>

@push('scripts')
<script>document.addEventListener('DOMContentLoaded', () => lucide.createIcons());</script>
@endpush
@endsection
