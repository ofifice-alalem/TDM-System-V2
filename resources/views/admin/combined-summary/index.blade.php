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
                <a href="{{ route('admin.combined-summary.index', array_merge(request()->query(), ['pdf' => 1])) }}"
                    target="_blank"
                    class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4"></i> تصدير PDF
                </a>
            </div>
        </form>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-4 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-50 dark:bg-amber-500/10 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="history" class="w-5 h-5 text-amber-500 dark:text-amber-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">ديون سابقة</p>
                    <p class="text-lg font-black text-amber-600 dark:text-amber-400 truncate">{{ number_format($grandOldDebt, 2) }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-4 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-500/10 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="shopping-cart" class="w-5 h-5 text-blue-500 dark:text-blue-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">إجمالي الفواتير</p>
                    <p class="text-lg font-black text-blue-600 dark:text-blue-400 truncate">{{ number_format($grandInvoices, 2) }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-4 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="banknote" class="w-5 h-5 text-emerald-500 dark:text-emerald-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">إجمالي المدفوعات</p>
                    <p class="text-lg font-black text-emerald-600 dark:text-emerald-400 truncate">{{ number_format($grandPayments, 2) }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-4 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 bg-orange-50 dark:bg-orange-500/10 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="package-x" class="w-5 h-5 text-orange-500 dark:text-orange-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">إجمالي المرتجعات</p>
                    <p class="text-lg font-black text-orange-600 dark:text-orange-400 truncate">{{ number_format($grandReturns, 2) }}</p>
                </div>
            </div>
            <div class="{{ $grandDebt > 0 ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800' }} rounded-2xl border p-4 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 {{ $grandDebt > 0 ? 'bg-red-100 dark:bg-red-500/20' : 'bg-emerald-100 dark:bg-emerald-500/20' }} rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="trending-up" class="w-5 h-5 {{ $grandDebt > 0 ? 'text-red-500 dark:text-red-400' : 'text-emerald-500 dark:text-emerald-400' }}"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">إجمالي الدين</p>
                    <p class="text-lg font-black {{ $grandDebt > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }} truncate">{{ number_format($grandDebt, 2) }}</p>
                </div>
            </div>
        </div>

        {{-- Summaries: Stores + Customers --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

            {{-- ملخص المتاجر --}}
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-dark-border flex items-center gap-2">
                    <i data-lucide="store" class="w-4 h-4 text-blue-500"></i>
                    <h3 class="font-black text-gray-900 dark:text-white text-sm">ملخص المتاجر</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="grid grid-cols-4 divide-x divide-x-reverse divide-gray-100 dark:divide-dark-border border border-gray-100 dark:border-dark-border rounded-2xl overflow-hidden">
                        <div class="p-3 text-center">
                            <div class="w-7 h-7 bg-amber-50 dark:bg-amber-500/10 rounded-lg flex items-center justify-center text-amber-500 mx-auto mb-1"><i data-lucide="history" class="w-3.5 h-3.5"></i></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">ديون سابقة</div>
                            <div class="text-sm font-black text-amber-600 dark:text-amber-400">{{ number_format($storeSummary['old_debt'], 2) }}</div>
                        </div>
                        <div class="p-3 text-center">
                            <div class="w-7 h-7 bg-blue-50 dark:bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-500 mx-auto mb-1"><i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">المبيعات</div>
                            <div class="text-sm font-black text-blue-600 dark:text-blue-400">{{ number_format($storeSummary['invoices'], 2) }}</div>
                        </div>
                        <div class="p-3 text-center">
                            <div class="w-7 h-7 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-500 mx-auto mb-1"><i data-lucide="banknote" class="w-3.5 h-3.5"></i></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">المدفوعات</div>
                            <div class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ number_format($storeSummary['payments'], 2) }}</div>
                        </div>
                        <div class="p-3 text-center">
                            <div class="w-7 h-7 bg-orange-50 dark:bg-orange-500/10 rounded-lg flex items-center justify-center text-orange-500 mx-auto mb-1"><i data-lucide="package-x" class="w-3.5 h-3.5"></i></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">المرتجعات</div>
                            <div class="text-sm font-black text-orange-600 dark:text-orange-400">{{ number_format($storeSummary['returns'], 2) }}</div>
                        </div>
                    </div>
                    @if($storeSummary['pending_invoices'] > 0 || $storeSummary['pending_payments'] > 0 || $storeSummary['pending_returns'] > 0)
                    <div class="flex gap-3">
                        {{-- Pending badges --}}
                        <div class="flex-1 space-y-1.5">
                        @if($storeSummary['pending_invoices'] > 0)
                        <div class="flex items-center justify-between bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 rounded-xl px-3 py-2">
                            <div class="flex items-center gap-2 text-blue-700 dark:text-blue-400"><i data-lucide="clock" class="w-3.5 h-3.5"></i><span class="text-xs font-bold">فواتير معلقة</span></div>
                            <span class="text-xs font-black text-blue-700 dark:text-blue-300">+ {{ number_format($storeSummary['pending_invoices'], 2) }}</span>
                        </div>
                        @endif
                        @if($storeSummary['pending_payments'] > 0)
                        <div class="flex items-center justify-between bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl px-3 py-2">
                            <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-400"><i data-lucide="clock" class="w-3.5 h-3.5"></i><span class="text-xs font-bold">إيصالات معلقة</span></div>
                            <span class="text-xs font-black text-emerald-700 dark:text-emerald-300">- {{ number_format($storeSummary['pending_payments'], 2) }}</span>
                        </div>
                        @endif
                        @if($storeSummary['pending_returns'] > 0)
                        <div class="flex items-center justify-between bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 rounded-xl px-3 py-2">
                            <div class="flex items-center gap-2 text-orange-700 dark:text-orange-400"><i data-lucide="clock" class="w-3.5 h-3.5"></i><span class="text-xs font-bold">مرتجعات معلقة</span></div>
                            <span class="text-xs font-black text-orange-700 dark:text-orange-300">- {{ number_format($storeSummary['pending_returns'], 2) }}</span>
                        </div>
                        @endif
                        </div>
                        {{-- إجمالي الدين --}}
                        <div class="relative rounded-2xl overflow-hidden shrink-0 w-1/2">
                            <div class="absolute inset-0 bg-gradient-to-br from-orange-700 to-orange-900"></div>
                            <div class="relative p-3 h-full flex flex-col justify-between">
                                <div class="text-white/80 text-xs font-bold">إجمالي الدين</div>
                                <div class="text-xl font-black text-white">{{ number_format($storeSummary['debt'], 2) }}<span class="text-xs font-bold text-white/70 mr-1">دينار</span></div>
                                @php $pendingNet = $storeSummary['pending_invoices'] - $storeSummary['pending_payments'] - $storeSummary['pending_returns']; @endphp
                                <div class="grid grid-cols-2 gap-1 mt-1">
                                    <div class="bg-white/20 rounded-lg px-2 py-1">
                                        <div class="text-white/70 text-xs">معتمد</div>
                                        <div class="text-white text-xs font-black">{{ number_format($storeSummary['approved_debt'], 2) }}</div>
                                    </div>
                                    <div class="bg-white/20 rounded-lg px-2 py-1">
                                        <div class="text-white/70 text-xs">معلق</div>
                                        <div class="text-white text-xs font-black">{{ number_format($pendingNet, 2) }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @else
                    {{-- إجمالي الدين بدون pending --}}
                    <div class="relative rounded-2xl overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-orange-700 to-orange-900"></div>
                        <div class="relative p-4 flex items-center justify-between">
                            <div>
                                <div class="text-white/80 text-xs font-bold mb-1">إجمالي الدين</div>
                                <div class="text-2xl font-black text-white">{{ number_format($storeSummary['debt'], 2) }} <span class="text-sm font-bold text-white/70">دينار</span></div>
                            </div>
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <i data-lucide="trending-up" class="w-5 h-5 text-white"></i>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ملخص العملاء --}}
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 dark:border-dark-border flex items-center gap-2">
                    <i data-lucide="users" class="w-4 h-4 text-purple-500"></i>
                    <h3 class="font-black text-gray-900 dark:text-white text-sm">ملخص العملاء</h3>
                </div>
                <div class="p-5 space-y-3">
                    <div class="grid grid-cols-4 divide-x divide-x-reverse divide-gray-100 dark:divide-dark-border border border-gray-100 dark:border-dark-border rounded-2xl overflow-hidden">
                        <div class="p-3 text-center">
                            <div class="w-7 h-7 bg-amber-50 dark:bg-amber-500/10 rounded-lg flex items-center justify-center text-amber-500 mx-auto mb-1"><i data-lucide="history" class="w-3.5 h-3.5"></i></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">ديون سابقة</div>
                            <div class="text-sm font-black text-amber-600 dark:text-amber-400">{{ number_format($customerSummary['old_debt'], 2) }}</div>
                        </div>
                        <div class="p-3 text-center">
                            <div class="w-7 h-7 bg-blue-50 dark:bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-500 mx-auto mb-1"><i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">المبيعات</div>
                            <div class="text-sm font-black text-blue-600 dark:text-blue-400">{{ number_format($customerSummary['invoices'], 2) }}</div>
                        </div>
                        <div class="p-3 text-center">
                            <div class="w-7 h-7 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-500 mx-auto mb-1"><i data-lucide="banknote" class="w-3.5 h-3.5"></i></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">المدفوعات</div>
                            <div class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ number_format($customerSummary['payments'], 2) }}</div>
                        </div>
                        <div class="p-3 text-center">
                            <div class="w-7 h-7 bg-orange-50 dark:bg-orange-500/10 rounded-lg flex items-center justify-center text-orange-500 mx-auto mb-1"><i data-lucide="package-x" class="w-3.5 h-3.5"></i></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">المرتجعات</div>
                            <div class="text-sm font-black text-orange-600 dark:text-orange-400">{{ number_format($customerSummary['returns'], 2) }}</div>
                        </div>
                    </div>
                    {{-- إجمالي الدين --}}
                    <div class="relative rounded-2xl overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-700 to-purple-900"></div>
                        <div class="relative p-4 flex items-center justify-between">
                            <div>
                                <div class="text-white/80 text-xs font-bold mb-1">إجمالي الدين</div>
                                <div class="text-2xl font-black text-white">{{ number_format($customerSummary['debt'], 2) }} <span class="text-sm font-bold text-white/70">دينار</span></div>
                            </div>
                            <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                                <i data-lucide="trending-up" class="w-5 h-5 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
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
                                <th class="px-4 py-3 text-left font-bold text-amber-600 dark:text-amber-400">ديون سابقة</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-gray-300">إجمالي الفواتير</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-gray-300">إجمالي المدفوعات</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-gray-300">إجمالي المرتجعات</th>
                                <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-gray-300">الدين الحالي</th>
                                <th class="px-4 py-3 text-center font-bold text-gray-700 dark:text-gray-300">دائن / مدين</th>
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
                                    <td class="px-4 py-3 text-left font-mono {{ $row->old_debt > 0 ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-gray-400 dark:text-gray-600' }}">
                                        {{ $row->old_debt > 0 ? number_format($row->old_debt, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-left font-mono text-gray-700 dark:text-gray-300">{{ number_format($row->total_invoices, 2) }}</td>
                                    <td class="px-4 py-3 text-left font-mono text-green-600 dark:text-green-400">{{ number_format($row->total_payments, 2) }}</td>
                                    <td class="px-4 py-3 text-left font-mono text-orange-600 dark:text-orange-400">{{ number_format($row->total_returns, 2) }}</td>
                                    <td class="px-4 py-3 text-left font-mono font-bold {{ $row->total_debt > 0 ? 'text-red-600 dark:text-red-400' : ($row->total_debt < 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400') }}">
                                        {{ number_format($row->total_debt, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($row->total_debt > 0)
                                            <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-2 py-0.5 rounded-lg text-xs font-black">مدين</span>
                                        @elseif($row->total_debt < 0)
                                            <span class="bg-emerald-500 text-white px-2 py-0.5 rounded-lg text-xs font-black">دائن</span>
                                        @else
                                            <span class="text-gray-400 text-xs">--</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 dark:bg-dark-bg border-t-2 border-gray-300 dark:border-dark-border">
                                <td colspan="3" class="px-4 py-3 font-black text-gray-900 dark:text-white">الإجمالي</td>
                                <td class="px-4 py-3 text-left font-black font-mono {{ $grandOldDebt > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400' }}">{{ $grandOldDebt > 0 ? number_format($grandOldDebt, 2) : '—' }}</td>
                                <td class="px-4 py-3 text-left font-black font-mono text-blue-600 dark:text-blue-400">{{ number_format($grandInvoices, 2) }}</td>
                                <td class="px-4 py-3 text-left font-black font-mono text-green-600 dark:text-green-400">{{ number_format($grandPayments, 2) }}</td>
                                <td class="px-4 py-3 text-left font-black font-mono text-orange-600 dark:text-orange-400">{{ number_format($grandReturns, 2) }}</td>
                                <td class="px-4 py-3 text-left font-black font-mono {{ $grandDebt > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">{{ number_format($grandDebt, 2) }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($grandDebt > 0)
                                        <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-2 py-0.5 rounded-lg text-xs font-black">مدين</span>
                                    @elseif($grandDebt < 0)
                                        <span class="bg-emerald-500 text-white px-2 py-0.5 rounded-lg text-xs font-black">دائن</span>
                                    @else
                                        <span class="text-gray-400 text-xs">--</span>
                                    @endif
                                </td>
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
