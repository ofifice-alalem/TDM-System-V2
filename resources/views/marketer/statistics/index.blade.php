@extends('layouts.app')

@section('title', 'إحصائياتي')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 space-y-6">

        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white">إحصائياتي</h1>
            <p class="text-gray-500 dark:text-dark-muted mt-1">تقارير وإحصائيات مفصلة لنشاطك</p>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl p-6 border border-gray-200 dark:border-dark-border shadow-lg">
            <form method="GET" action="{{ route('marketer.statistics.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">العملية</label>
                        <select name="operation" id="operation" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">اختر العملية...</option>
                            <option value="summary"        {{ request('operation') == 'summary'        ? 'selected' : '' }}>الملخص المالي</option>
                            <option value="sales"          {{ request('operation') == 'sales'          ? 'selected' : '' }}>فواتير البيع</option>
                            <option value="payments"       {{ request('operation') == 'payments'       ? 'selected' : '' }}>إيصالات القبض</option>
                            <option value="sales_returns"  {{ request('operation') == 'sales_returns'  ? 'selected' : '' }}>إرجاعات المتاجر</option>
                            <option value="requests"       {{ request('operation') == 'requests'       ? 'selected' : '' }}>طلبات البضاعة</option>
                            <option value="returns"        {{ request('operation') == 'returns'        ? 'selected' : '' }}>إرجاعات البضاعة</option>
                            <option value="withdrawals"    {{ request('operation') == 'withdrawals'    ? 'selected' : '' }}>طلبات سحب الأرباح</option>
                        </select>
                    </div>

                    <div id="store_field" style="display: {{ in_array(request('operation'), ['sales', 'payments', 'sales_returns']) ? 'block' : 'none' }}">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">المتجر (اختياري)</label>
                        <select name="marketer_store_id" id="marketer_store_id" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">الكل</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ request('marketer_store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">من تاريخ</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:[color-scheme:dark]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">إلى تاريخ</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:[color-scheme:dark]">
                    </div>

                    <div id="status_field" style="display: {{ request('operation') == 'summary' ? 'none' : 'block' }}">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">الحالة</label>
                        <select name="status" id="status" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">الكل</option>
                        </select>
                    </div>

                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                        عرض الإحصائيات
                    </button>
                    @if(request()->hasAny(['operation', 'from_date', 'to_date', 'status']))
                        <button type="submit" name="export" value="1" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            تصدير Excel
                        </button>
                        <a href="{{ route('marketer.statistics.index') }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            إعادة تعيين
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Results --}}
        @if($results)
            @if(isset($results['is_store_summary']))
                {{-- ملخص متاجر المسوق --}}
                <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-dark-border">
                        <h2 class="text-xl font-black text-gray-900 dark:text-white mb-6">الملخص المالي للمتاجر</h2>

                        {{-- Stats Grid --}}
                        <div class="flex flex-col lg:flex-row gap-4">

                        {{-- يمين: الإحصائيات والـ pending --}}
                        <div class="flex-1 space-y-3">
                        <div class="grid grid-cols-3 divide-x divide-x-reverse divide-gray-100 dark:divide-dark-border border border-gray-100 dark:border-dark-border rounded-2xl overflow-hidden min-w-0">
                            <div class="p-4 text-center">
                                <div class="w-8 h-8 bg-blue-50 dark:bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-500 dark:text-blue-400 mx-auto mb-2">
                                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">المبيعات</div>
                                <div class="text-sm font-black text-blue-600 dark:text-blue-400">{{ number_format($results['total_sales'], 2) }}</div>
                            </div>
                            <div class="p-4 text-center">
                                <div class="w-8 h-8 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-500 dark:text-emerald-400 mx-auto mb-2">
                                    <i data-lucide="banknote" class="w-4 h-4"></i>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">المدفوعات</div>
                                <div class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ number_format($results['total_payments'], 2) }}</div>
                            </div>
                            <div class="p-4 text-center">
                                <div class="w-8 h-8 bg-orange-50 dark:bg-orange-500/10 rounded-lg flex items-center justify-center text-orange-500 dark:text-orange-400 mx-auto mb-2">
                                    <i data-lucide="package-x" class="w-4 h-4"></i>
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">المرتجعات</div>
                                <div class="text-sm font-black text-orange-600 dark:text-orange-400">{{ number_format($results['total_returns'], 2) }}</div>
                            </div>
                        </div>

                        {{-- Pending Badges --}}
                        @if($results['pending_sales'] > 0 || $results['pending_payments'] > 0 || $results['pending_returns'] > 0)
                        <div class="space-y-2 mb-4">
                            @if($results['pending_sales'] > 0)
                            <div class="flex items-center justify-between bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 rounded-xl px-4 py-2.5">
                                <div class="flex items-center gap-2 text-blue-700 dark:text-blue-400">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                    <span class="text-xs font-bold">فواتير معلقة</span>
                                </div>
                                <span class="text-xs font-black text-blue-700 dark:text-blue-300">+ {{ number_format($results['pending_sales'], 2) }} دينار</span>
                            </div>
                            @endif
                            @if($results['pending_payments'] > 0)
                            <div class="flex items-center justify-between bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl px-4 py-2.5">
                                <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-400">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                    <span class="text-xs font-bold">إيصالات معلقة</span>
                                </div>
                                <span class="text-xs font-black text-emerald-700 dark:text-emerald-300">- {{ number_format($results['pending_payments'], 2) }} دينار</span>
                            </div>
                            @endif
                            @if($results['pending_returns'] > 0)
                            <div class="flex items-center justify-between bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 rounded-xl px-4 py-2.5">
                                <div class="flex items-center gap-2 text-orange-700 dark:text-orange-400">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                    <span class="text-xs font-bold">مرتجعات معلقة</span>
                                </div>
                                <span class="text-xs font-black text-orange-700 dark:text-orange-300">- {{ number_format($results['pending_returns'], 2) }} دينار</span>
                            </div>
                            @endif
                        </div>
                        @endif
                        </div>{{-- end flex-1 --}}

                        {{-- يسار: بطاقة إجمالي الدين --}}
                        {{-- Total Debt --}}
                        <div class="w-full lg:w-96 shrink-0">
                        <div class="relative rounded-2xl overflow-hidden h-full">
                            <div class="absolute inset-0 bg-gradient-to-br from-orange-500 via-orange-600 to-amber-700"></div>
                            <div class="absolute inset-0 opacity-20">
                                <div class="absolute -top-4 -left-4 w-32 h-32 bg-white rounded-full"></div>
                                <div class="absolute -bottom-6 -right-6 w-40 h-40 bg-white rounded-full"></div>
                            </div>
                            <div class="relative p-6">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-white text-sm font-black tracking-wide">إجمالي الدين</span>
                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                        <i data-lucide="trending-up" class="w-4 h-4 text-white"></i>
                                    </div>
                                </div>
                                <div class="text-3xl font-black text-white tracking-tight">
                                    {{ number_format(abs($results['total_debt']), 2) }}
                                    <span class="text-base font-bold text-white/70">دينار</span>
                                </div>
                                @if($results['pending_sales'] > 0 || $results['pending_payments'] > 0 || $results['pending_returns'] > 0)
                                @php $pendingNet = $results['pending_sales'] - $results['pending_payments'] - $results['pending_returns']; @endphp
                                <div class="mt-3 pt-3 border-t border-white/20 grid grid-cols-2 gap-3 text-sm">
                                    <div class="bg-white/20 rounded-xl px-4 py-3">
                                        <div class="text-white/80 text-xs font-bold mb-1">معتمد</div>
                                        <div class="text-white font-black text-base">{{ number_format($results['total_debt'] - $pendingNet, 2) }} دينار</div>
                                    </div>
                                    <div class="bg-white/20 rounded-xl px-4 py-3">
                                        <div class="text-white/80 text-xs font-bold mb-1">معلق</div>
                                        <div class="text-white font-black text-base">{{ number_format($pendingNet, 2) }} دينار</div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>{{-- end lg:w-56 --}}

                        </div>{{-- end flex --}}

                    </div>
                    @if(count($results['stores_data']) > 0)
                    {{-- Mobile Cards --}}
                    <div class="md:hidden p-2 space-y-3">
                        @foreach($results['stores_data'] as $row)
                        <div class="border border-gray-200 dark:border-dark-border rounded-2xl overflow-hidden">
                            {{-- Store name header --}}
                            <div class="bg-gray-50 dark:bg-dark-bg px-3 py-2 border-b border-gray-200 dark:border-dark-border">
                                <p class="text-sm font-black text-gray-900 dark:text-white">{{ $row['store_name'] }}</p>
                            </div>
                            <div class="p-3 space-y-2">
                                {{-- 3 ops with icons --}}
                                <div class="grid grid-cols-3 gap-1.5">
                                    <div class="flex flex-col items-center gap-1 bg-blue-50 dark:bg-blue-500/10 rounded-xl px-2 py-2">
                                        <i data-lucide="shopping-cart" class="w-4 h-4 text-blue-500 dark:text-blue-400"></i>
                                        <p class="text-xs text-blue-500 dark:text-blue-400 font-bold">المبيعات</p>
                                        <p class="text-xs font-black text-blue-700 dark:text-blue-300 text-center">{{ number_format($row['sales'], 2) }}</p>
                                    </div>
                                    <div class="flex flex-col items-center gap-1 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl px-2 py-2">
                                        <i data-lucide="banknote" class="w-4 h-4 text-emerald-500 dark:text-emerald-400"></i>
                                        <p class="text-xs text-emerald-500 dark:text-emerald-400 font-bold">المدفوعات</p>
                                        <p class="text-xs font-black text-emerald-700 dark:text-emerald-300 text-center">{{ number_format($row['payments'], 2) }}</p>
                                    </div>
                                    <div class="flex flex-col items-center gap-1 bg-orange-50 dark:bg-orange-500/10 rounded-xl px-2 py-2">
                                        <i data-lucide="package-x" class="w-4 h-4 text-orange-500 dark:text-orange-400"></i>
                                        <p class="text-xs text-orange-500 dark:text-orange-400 font-bold">المرتجعات</p>
                                        <p class="text-xs font-black text-orange-700 dark:text-orange-300 text-center">{{ number_format($row['returns'], 2) }}</p>
                                    </div>
                                </div>
                                {{-- Debt footer --}}
                                <div class="flex items-center justify-between {{ $row['balance'] > 0 ? 'bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30' : 'bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30' }} rounded-xl px-3 py-2">
                                    <div class="flex items-center gap-1.5 {{ $row['balance'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                        <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                                        <span class="text-xs font-bold">الدين</span>
                                    </div>
                                    <span class="text-sm font-black {{ $row['balance'] > 0 ? 'text-red-700 dark:text-red-300' : 'text-emerald-700 dark:text-emerald-300' }}">{{ number_format($row['balance'], 2) }} دينار</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto mt-8">
                        <table class="w-full min-w-[600px]">
                            <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المتجر</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-blue-600 dark:text-blue-400">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>المبيعات
                                        </span>
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="banknote" class="w-3.5 h-3.5"></i>المدفوعات
                                        </span>
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-orange-600 dark:text-orange-400">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="package-x" class="w-3.5 h-3.5"></i>المرتجعات
                                        </span>
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">
                                        <span class="flex items-center gap-1.5">
                                            <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>الدين
                                        </span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                                @foreach($results['stores_data'] as $row)
                                <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">{{ $row['store_name'] }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-blue-600 dark:text-blue-400">{{ number_format($row['sales'], 2) }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($row['payments'], 2) }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-orange-600 dark:text-orange-400">{{ number_format($row['returns'], 2) }}</td>
                                    <td class="px-6 py-4 text-sm font-black {{ $row['balance'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ number_format($row['balance'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            @elseif(isset($results['is_summary']) && $results['is_summary'] && isset($results['is_marketer_summary']))
                {{-- Marketer Financial Summary --}}
                <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-dark-border">
                        <h2 class="text-xl font-black text-gray-900 dark:text-white mb-6">الملخص المالي</h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4 border border-emerald-200 dark:border-emerald-800">
                                <p class="text-sm text-emerald-600 dark:text-emerald-400 font-bold mb-2">إجمالي الأرباح</p>
                                <p class="text-2xl font-black text-emerald-700 dark:text-emerald-300">{{ number_format($results['total_commissions'], 2) }} دينار</p>
                            </div>
                            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800">
                                <p class="text-sm text-amber-600 dark:text-amber-400 font-bold mb-2">إجمالي المسحوب</p>
                                <p class="text-2xl font-black text-amber-700 dark:text-amber-300">{{ number_format($results['total_withdrawals'], 2) }} دينار</p>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                                <p class="text-sm text-blue-600 dark:text-blue-400 font-bold mb-2">المتبقي</p>
                                <p class="text-2xl font-black text-blue-700 dark:text-blue-300">{{ number_format($results['remaining'], 2) }} دينار</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-dark-border">
                        <h2 class="text-xl font-black text-gray-900 dark:text-white mb-4">النتائج</h2>

                        @if(!request('status') && isset($results['status_totals']) && !in_array($results['operation'], ['requests', 'returns']))
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 mb-4">
                            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-3 border border-amber-200 dark:border-amber-800">
                                <p class="text-xs text-amber-600 dark:text-amber-400 font-bold mb-1">معلق</p>
                                <p class="text-lg font-black text-amber-700 dark:text-amber-300">{{ number_format($results['status_totals']['pending'], 2) }}</p>
                            </div>
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-3 border border-red-200 dark:border-red-800">
                                <p class="text-xs text-red-600 dark:text-red-400 font-bold mb-1">ملغي</p>
                                <p class="text-lg font-black text-red-700 dark:text-red-300">{{ number_format($results['status_totals']['cancelled'], 2) }}</p>
                            </div>
                            <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-3 border border-red-200 dark:border-red-800">
                                <p class="text-xs text-red-600 dark:text-red-400 font-bold mb-1">مرفوض</p>
                                <p class="text-lg font-black text-red-700 dark:text-red-300">{{ number_format($results['status_totals']['rejected'], 2) }}</p>
                            </div>
                            <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-3 border border-emerald-200 dark:border-emerald-800">
                                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-bold mb-1">موثق</p>
                                <p class="text-lg font-black text-emerald-700 dark:text-emerald-300">{{ number_format($results['status_totals']['approved'], 2) }}</p>
                            </div>
                            <div class="bg-blue-50 dark:bg-blue-900/30 rounded-xl p-3 border border-blue-200 dark:border-blue-800">
                                <p class="text-xs text-blue-600 dark:text-blue-400 font-bold mb-1">الكلي</p>
                                <p class="text-lg font-black text-blue-700 dark:text-blue-300">{{ number_format($results['status_totals']['total'], 2) }}</p>
                            </div>
                        </div>
                        @endif

                        @if($results['operation'] == 'payments' && isset($results['payment_method_totals']))
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-xl p-3 border border-purple-200 dark:border-purple-800">
                                <p class="text-xs text-purple-600 dark:text-purple-400 font-bold mb-1">كاش</p>
                                <p class="text-lg font-black text-purple-700 dark:text-purple-300">{{ number_format($results['payment_method_totals']['cash'], 2) }}</p>
                            </div>
                            <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-3 border border-indigo-200 dark:border-indigo-800">
                                <p class="text-xs text-indigo-600 dark:text-indigo-400 font-bold mb-1">حوالة</p>
                                <p class="text-lg font-black text-indigo-700 dark:text-indigo-300">{{ number_format($results['payment_method_totals']['transfer'], 2) }}</p>
                            </div>
                            <div class="bg-cyan-50 dark:bg-cyan-900/20 rounded-xl p-3 border border-cyan-200 dark:border-cyan-800">
                                <p class="text-xs text-cyan-600 dark:text-cyan-400 font-bold mb-1">شيك مصدق</p>
                                <p class="text-lg font-black text-cyan-700 dark:text-cyan-300">{{ number_format($results['payment_method_totals']['certified_check'], 2) }}</p>
                            </div>
                            <div class="bg-gray-50 dark:bg-gray-900/20 rounded-xl p-3 border border-gray-200 dark:border-gray-800">
                                <p class="text-xs text-gray-600 dark:text-gray-400 font-bold mb-1">إجمالي</p>
                                <p class="text-lg font-black text-gray-700 dark:text-gray-300">{{ number_format($results['payment_method_totals']['total'], 2) }}</p>
                            </div>
                        </div>
                        @endif

                        @if(!in_array($results['operation'], ['requests', 'returns']) && request('status'))
                        <div class="text-left">
                            <p class="text-xs text-gray-500 dark:text-gray-400">الإجمالي</p>
                            <p class="text-2xl font-black text-primary-600 dark:text-primary-400">{{ number_format($results['total'], 2) }} دينار</p>
                            @if($results['operation'] == 'payments' && $results['total_commission'] > 0)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">إجمالي المستحق</p>
                                <p class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ number_format($results['total_commission'], 2) }} دينار</p>
                            @endif
                        </div>
                        @endif
                    </div>

                    {{-- Mobile Cards --}}
                    <div class="md:hidden divide-y divide-gray-200 dark:divide-dark-border">
                        @forelse($results['data'] as $item)
                            @php
                                $statusConfig = [
                                    'pending'    => ['bg' => 'bg-amber-100 dark:bg-amber-900/30',     'text' => 'text-amber-700 dark:text-amber-400',   'label' => 'معلق'],
                                    'approved'   => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400','label' => 'موثق'],
                                    'documented' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400','label' => 'موثق'],
                                    'cancelled'  => ['bg' => 'bg-gray-100 dark:bg-gray-800/50',       'text' => 'text-gray-700 dark:text-gray-400',     'label' => 'ملغي'],
                                    'rejected'   => ['bg' => 'bg-red-100 dark:bg-red-900/30',         'text' => 'text-red-700 dark:text-red-400',       'label' => 'مرفوض'],
                                ][$item->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => $item->status];
                            @endphp
                            <div class="p-4 space-y-3">
                                {{-- Header row: number + status --}}
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-black text-gray-900 dark:text-white">
                                        @if($results['operation'] == 'sales')             {{ $item->invoice_number }}
                                        @elseif($results['operation'] == 'payments')      {{ $item->payment_number }}
                                        @elseif($results['operation'] == 'sales_returns') {{ $item->return_number }}
                                        @elseif($results['operation'] == 'returns')       {{ $item->return_number }}
                                        @elseif($results['operation'] == 'requests')      {{ $item->invoice_number }}
                                        @elseif($results['operation'] == 'withdrawals')   WD-{{ $item->id }}
                                        @endif
                                    </span>
                                    <span class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-2 py-1 rounded text-xs font-bold">
                                        {{ $statusConfig['label'] }}
                                    </span>
                                </div>
                                {{-- Details grid --}}
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">التاريخ</span>
                                        <p class="font-bold text-gray-700 dark:text-gray-300 mt-0.5">{{ $item->created_at->format('Y-m-d') }}</p>
                                    </div>
                                    @if(in_array($results['operation'], ['sales', 'payments', 'sales_returns']))
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">المتجر</span>
                                        <p class="font-bold text-gray-700 dark:text-gray-300 mt-0.5">{{ $item->store->name ?? '-' }}</p>
                                    </div>
                                    @endif
                                    @if($results['operation'] == 'payments')
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">طريقة الدفع</span>
                                        <p class="font-bold text-gray-700 dark:text-gray-300 mt-0.5">
                                            @if($item->payment_method == 'cash') كاش
                                            @elseif($item->payment_method == 'transfer') حوالة
                                            @elseif($item->payment_method == 'certified_check') شيك مصدق
                                            @else - @endif
                                        </p>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">نسبة العمولة</span>
                                        <p class="font-bold text-gray-700 dark:text-gray-300 mt-0.5">{{ $item->commission->commission_rate ?? '-' }}%</p>
                                    </div>
                                    @endif
                                </div>
                                {{-- Amount / commission footer --}}
                                @if(!in_array($results['operation'], ['requests', 'returns']))
                                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-dark-border">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">المبلغ</span>
                                    <span class="text-sm font-black text-gray-900 dark:text-white">
                                        @if($results['operation'] == 'sales')             {{ number_format($item->total_amount, 2) }}
                                        @elseif($results['operation'] == 'payments')      {{ number_format($item->amount, 2) }}
                                        @elseif($results['operation'] == 'sales_returns') {{ number_format($item->total_amount, 2) }}
                                        @elseif($results['operation'] == 'withdrawals')   {{ number_format($item->requested_amount, 2) }}
                                        @endif
                                        دينار
                                    </span>
                                </div>
                                @if($results['operation'] == 'payments' && ($item->commission->commission_amount ?? 0) > 0)
                                <div class="flex items-center justify-between">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">المستحق</span>
                                    <span class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ number_format($item->commission->commission_amount, 2) }} دينار</span>
                                </div>
                                @endif
                                @endif
                            </div>
                        @empty
                            <div class="px-6 py-12 text-center">
                                <i data-lucide="inbox" class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400">لا توجد نتائج</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Desktop Table --}}
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full min-w-[640px]">
                            <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">
                                        @if($results['operation'] == 'sales') رقم الفاتورة
                                        @elseif($results['operation'] == 'payments') رقم الإيصال
                                        @elseif($results['operation'] == 'sales_returns') رقم الإرجاع
                                        @elseif($results['operation'] == 'returns') رقم الإرجاع
                                        @elseif($results['operation'] == 'requests') رقم الطلب
                                        @elseif($results['operation'] == 'withdrawals') رقم السحب
                                        @endif
                                    </th>
                                    @if(in_array($results['operation'], ['sales', 'payments', 'sales_returns']))
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المتجر</th>
                                    @endif
                                    @if($results['operation'] == 'payments')
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">نسبة العمولة</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">القيمة المستحقة</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">طريقة الدفع</th>
                                    @endif
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">التاريخ</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">الحالة</th>
                                    @if(!in_array($results['operation'], ['requests', 'returns']))
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المبلغ</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                                @forelse($results['data'] as $item)
                                    @php
                                        $statusConfig = [
                                            'pending'    => ['bg' => 'bg-amber-100 dark:bg-amber-900/30',  'text' => 'text-amber-700 dark:text-amber-400',   'label' => 'معلق'],
                                            'approved'   => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'موثق'],
                                            'documented' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'موثق'],
                                            'cancelled'  => ['bg' => 'bg-gray-100 dark:bg-gray-800/50',    'text' => 'text-gray-700 dark:text-gray-400',     'label' => 'ملغي'],
                                            'rejected'   => ['bg' => 'bg-red-100 dark:bg-red-900/30',      'text' => 'text-red-700 dark:text-red-400',       'label' => 'مرفوض'],
                                        ][$item->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => $item->status];
                                    @endphp
                                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                            @if($results['operation'] == 'sales')           {{ $item->invoice_number }}
                                            @elseif($results['operation'] == 'payments')    {{ $item->payment_number }}
                                            @elseif($results['operation'] == 'sales_returns') {{ $item->return_number }}
                                            @elseif($results['operation'] == 'returns')     {{ $item->return_number }}
                                            @elseif($results['operation'] == 'requests')    {{ $item->invoice_number }}
                                            @elseif($results['operation'] == 'withdrawals') WD-{{ $item->id }}
                                            @endif
                                        </td>
                                        @if(in_array($results['operation'], ['sales', 'payments', 'sales_returns']))
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->store->name ?? '-' }}</td>
                                        @endif
                                        @if($results['operation'] == 'payments')
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->commission->commission_rate ?? '-' }}%</td>
                                            <td class="px-6 py-4 text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($item->commission->commission_amount ?? 0, 2) }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                @if($item->payment_method == 'cash') كاش
                                                @elseif($item->payment_method == 'transfer') حوالة
                                                @elseif($item->payment_method == 'certified_check') شيك مصدق
                                                @else - @endif
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->created_at->format('Y-m-d') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-2 py-1 rounded text-xs font-bold">
                                                {{ $statusConfig['label'] }}
                                            </span>
                                        </td>
                                        @if(!in_array($results['operation'], ['requests', 'returns']))
                                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                                @if($results['operation'] == 'sales')           {{ number_format($item->total_amount, 2) }}
                                                @elseif($results['operation'] == 'payments')    {{ number_format($item->amount, 2) }}
                                                @elseif($results['operation'] == 'sales_returns') {{ number_format($item->total_amount, 2) }}
                                                @elseif($results['operation'] == 'withdrawals') {{ number_format($item->requested_amount, 2) }}
                                                @endif
                                                دينار
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <i data-lucide="inbox" class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-3"></i>
                                            <p class="text-gray-500 dark:text-gray-400">لا توجد نتائج</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($results['data']->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-dark-border">
                            {{ $results['data']->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            @endif
        @endif

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        const operation  = document.getElementById('operation');
        const storeField = document.getElementById('store_field');
        const statusField = document.getElementById('status_field');
        const statusSelect = document.getElementById('status');

        const statusOptions = {
            sales:         [{value:'pending',text:'معلق'},{value:'approved',text:'موثق'},{value:'cancelled',text:'ملغي'},{value:'rejected',text:'مرفوض'}],
            payments:      [{value:'pending',text:'معلق'},{value:'approved',text:'موثق'},{value:'cancelled',text:'ملغي'},{value:'rejected',text:'مرفوض'}],
            sales_returns: [{value:'pending',text:'معلق'},{value:'approved',text:'موثق'},{value:'cancelled',text:'ملغي'},{value:'rejected',text:'مرفوض'}],
            requests:      [{value:'pending',text:'معلق'},{value:'approved',text:'موافق عليه'},{value:'documented',text:'موثق'},{value:'cancelled',text:'ملغي'},{value:'rejected',text:'مرفوض'}],
            returns:       [{value:'pending',text:'معلق'},{value:'approved',text:'موافق عليه'},{value:'documented',text:'موثق'},{value:'cancelled',text:'ملغي'},{value:'rejected',text:'مرفوض'}],
            withdrawals:   [{value:'pending',text:'معلق'},{value:'approved',text:'موثق'},{value:'cancelled',text:'ملغي'},{value:'rejected',text:'مرفوض'}],
            summary:       []
        };

        function updateStatus(op) {
            const selected = '{{ request('status') }}';
            const opts = statusOptions[op] || [];
            statusSelect.innerHTML = '<option value="">الكل</option>';
            opts.forEach(o => {
                const el = document.createElement('option');
                el.value = o.value; el.text = o.text;
                if (o.value === selected) el.selected = true;
                statusSelect.appendChild(el);
            });
            statusField.style.display = op === 'summary' ? 'none' : 'block';
            storeField.style.display  = ['sales','payments','sales_returns'].includes(op) ? 'block' : 'none';
        }

        operation.addEventListener('change', function() { updateStatus(this.value); });

        // init on load
        if (operation.value) updateStatus(operation.value);
    });
</script>
@endpush

@endsection
