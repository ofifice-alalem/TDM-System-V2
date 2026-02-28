@extends('layouts.app')

@section('title', 'الإحصائيات')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4">
        
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 dark:text-white">الإحصائيات</h1>
            <p class="text-gray-500 dark:text-dark-muted mt-1">تقارير وإحصائيات مفصلة</p>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl p-6 border border-gray-200 dark:border-dark-border shadow-lg mb-6">
            <form method="GET" action="{{ route('admin.statistics.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">نوع الإحصاء</label>
                        <select name="stat_type" id="stat_type" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">اختر النوع...</option>
                            <option value="stores" {{ request('stat_type') == 'stores' ? 'selected' : '' }}>المتاجر</option>
                            <option value="marketers" {{ request('stat_type') == 'marketers' ? 'selected' : '' }}>المسوقين</option>
                        </select>
                    </div>
                </div>

                <div id="filters_container" style="display: {{ request('stat_type') ? 'block' : 'none' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div id="store_field" style="display: {{ request('stat_type') == 'stores' ? 'block' : 'none' }}">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">اسم المتجر</label>
                            <select name="store_id" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">اختر المتجر...</option>
                                <option value="all" {{ request('store_id') == 'all' ? 'selected' : '' }}>الكل</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="marketer_field" style="display: {{ request('stat_type') == 'marketers' ? 'block' : 'none' }}">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">اسم المسوق</label>
                            <select name="marketer_id" id="marketer_id" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">اختر المسوق...</option>
                                @foreach($marketers as $marketer)
                                    <option value="{{ $marketer->id }}" {{ request('marketer_id') == $marketer->id ? 'selected' : '' }}>{{ $marketer->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="marketer_store_field" style="display: {{ request('stat_type') == 'marketers' && in_array(request('operation'), ['sales', 'payments', 'sales_returns']) ? 'block' : 'none' }}">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">المتجر (اختياري)</label>
                            <select name="marketer_store_id" id="marketer_store_id" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">الكل</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">العملية</label>
                            <select name="operation" id="operation" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">اختر العملية...</option>
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

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">الحالة</label>
                        <select name="status" id="status" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">الكل</option>
                        </select>
                    </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                        عرض الإحصائيات
                    </button>
                    @if(request()->hasAny(['stat_type', 'store_id', 'operation', 'from_date', 'to_date', 'status']))
                        <button type="submit" name="export" value="1" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            تصدير Excel
                        </button>
                        <a href="{{ route('admin.statistics.index') }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            إعادة تعيين
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Results --}}
        @if($results)
            @if(isset($results['is_summary']) && $results['is_summary'])
                {{-- Financial Summary --}}
                <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-dark-border">
                        <h2 class="text-xl font-black text-gray-900 dark:text-white mb-6">الملخص المالي</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                                <p class="text-sm text-blue-600 dark:text-blue-400 font-bold mb-2">إجمالي المبيعات</p>
                                <p class="text-2xl font-black text-blue-700 dark:text-blue-300">{{ number_format($results['total_sales'], 2) }} دينار</p>
                            </div>
                            <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4 border border-emerald-200 dark:border-emerald-800">
                                <p class="text-sm text-emerald-600 dark:text-emerald-400 font-bold mb-2">إجمالي المدفوعات</p>
                                <p class="text-2xl font-black text-emerald-700 dark:text-emerald-300">{{ number_format($results['total_payments'], 2) }} دينار</p>
                            </div>
                            <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800">
                                <p class="text-sm text-amber-600 dark:text-amber-400 font-bold mb-2">إجمالي المرتجعات</p>
                                <p class="text-2xl font-black text-amber-700 dark:text-amber-300">{{ number_format($results['total_returns'], 2) }} دينار</p>
                            </div>
                            <div class="bg-{{ $results['current_balance'] >= 0 ? 'red' : 'purple' }}-50 dark:bg-{{ $results['current_balance'] >= 0 ? 'red' : 'purple' }}-900/20 rounded-xl p-4 border border-{{ $results['current_balance'] >= 0 ? 'red' : 'purple' }}-200 dark:border-{{ $results['current_balance'] >= 0 ? 'red' : 'purple' }}-800">
                                <p class="text-sm text-{{ $results['current_balance'] >= 0 ? 'red' : 'purple' }}-600 dark:text-{{ $results['current_balance'] >= 0 ? 'red' : 'purple' }}-400 font-bold mb-2">الدين</p>
                                <p class="text-2xl font-black text-{{ $results['current_balance'] >= 0 ? 'red' : 'purple' }}-700 dark:text-{{ $results['current_balance'] >= 0 ? 'red' : 'purple' }}-300">{{ number_format($results['current_balance'], 2) }} دينار</p>
                            </div>
                        </div>
                    </div>
                    
                    @if(isset($results['stores_data']) && count($results['stores_data']) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المتجر</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">إجمالي المبيعات</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">إجمالي المدفوع</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">إجمالي المرتجعات</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">الدين</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                                @foreach($results['stores_data'] as $storeData)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">{{ $storeData['store_name'] }}</td>
                                        <td class="px-6 py-4 text-sm text-blue-600 dark:text-blue-400 font-bold">{{ number_format($storeData['sales'], 2) }}</td>
                                        <td class="px-6 py-4 text-sm text-emerald-600 dark:text-emerald-400 font-bold">{{ number_format($storeData['payments'], 2) }}</td>
                                        <td class="px-6 py-4 text-sm text-amber-600 dark:text-amber-400 font-bold">{{ number_format($storeData['returns'], 2) }}</td>
                                        <td class="px-6 py-4 text-sm font-bold {{ $storeData['balance'] >= 0 ? 'text-red-600 dark:text-red-400' : 'text-purple-600 dark:text-purple-400' }}">{{ number_format($storeData['balance'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            @else
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-dark-border">
                    <h2 class="text-xl font-black text-gray-900 dark:text-white mb-4">النتائج</h2>
                    @if((request('stat_type') == 'stores' || request('stat_type') == 'marketers') && !request('status') && isset($results['status_totals']) && !in_array($results['operation'], ['requests', 'returns']))
                        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
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
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 {{ (request('stat_type') == 'stores' || request('stat_type') == 'marketers') && !request('status') ? 'mt-3' : '' }}">
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
                    @if((request('stat_type') == 'stores' || request('stat_type') == 'marketers') && !request('status'))
                        @if($results['operation'] == 'payments' && isset($results['payment_method_totals']))
                        @endif
                    @elseif(!in_array($results['operation'], ['requests', 'returns']))
                        <div class="flex items-center justify-between">
                            <div></div>
                            <div class="text-left">
                                <p class="text-xs text-gray-500 dark:text-gray-400">الإجمالي</p>
                                <p class="text-2xl font-black text-primary-600 dark:text-primary-400">{{ number_format($results['total'], 2) }} دينار</p>
                                @if($results['operation'] == 'payments' && $results['total_commission'] > 0 && request('stat_type') == 'marketers')
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">إجمالي المستحق</p>
                                    <p class="text-lg font-black text-emerald-600 dark:text-emerald-400">{{ number_format($results['total_commission'], 2) }} دينار</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">
                                    @if($results['operation'] == 'sales')
                                        رقم الفاتورة
                                    @elseif($results['operation'] == 'payments')
                                        رقم الإيصال
                                    @elseif($results['operation'] == 'returns')
                                        رقم الإرجاع
                                    @elseif($results['operation'] == 'sales_returns')
                                        رقم الإرجاع
                                    @elseif($results['operation'] == 'requests')
                                        رقم الطلب
                                    @elseif($results['operation'] == 'withdrawals')
                                        رقم السحب
                                    @endif
                                </th>
                                @if(request('stat_type') == 'stores')
                                    @if(request('store_id') == 'all')
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المتجر</th>
                                    @endif
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المسوق</th>
                                @elseif(request('stat_type') == 'marketers' && in_array($results['operation'], ['sales', 'payments', 'sales_returns']))
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المتجر</th>
                                @endif
                                @if($results['operation'] == 'payments' && request('stat_type') == 'marketers')
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">نسبة العمولة</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">القيمة المستحقة</th>
                                @endif
                                @if($results['operation'] == 'payments')
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
                                        'pending' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'label' => 'معلق'],
                                        'approved' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'موثق'],
                                        'documented' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'موثق'],
                                        'cancelled' => ['bg' => 'bg-gray-100 dark:bg-gray-800/50', 'text' => 'text-gray-700 dark:text-gray-400', 'label' => 'ملغي'],
                                        'rejected' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'مرفوض'],
                                    ][$item->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => $item->status];
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                        @if($results['operation'] == 'sales')
                                            {{ $item->invoice_number }}
                                        @elseif($results['operation'] == 'payments')
                                            {{ $item->payment_number }}
                                        @elseif($results['operation'] == 'returns')
                                            {{ $item->return_number }}
                                        @elseif($results['operation'] == 'sales_returns')
                                            {{ $item->return_number }}
                                        @elseif($results['operation'] == 'requests')
                                            {{ $item->invoice_number }}
                                        @elseif($results['operation'] == 'withdrawals')
                                            WD-{{ $item->id }}
                                        @endif
                                    </td>
                                    @if(request('stat_type') == 'stores')
                                        @if(request('store_id') == 'all')
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->store->name ?? '-' }}</td>
                                        @endif
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->marketer->full_name ?? '-' }}</td>
                                    @elseif(request('stat_type') == 'marketers' && in_array($results['operation'], ['sales', 'payments', 'sales_returns']))
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->store->name ?? '-' }}</td>
                                    @endif
                                    @if($results['operation'] == 'payments' && request('stat_type') == 'marketers')
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->commission->commission_rate ?? '-' }}%</td>
                                        <td class="px-6 py-4 text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($item->commission->commission_amount ?? 0, 2) }}</td>
                                    @endif
                                    @if($results['operation'] == 'payments')
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                            @if($item->payment_method == 'cash')
                                                كاش
                                            @elseif($item->payment_method == 'transfer')
                                                حوالة
                                            @elseif($item->payment_method == 'certified_check')
                                                شيك مصدق
                                            @else
                                                -
                                            @endif
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
                                            @if($results['operation'] == 'sales')
                                                {{ number_format($item->total_amount, 2) }}
                                            @elseif($results['operation'] == 'payments')
                                                {{ number_format($item->amount, 2) }}
                                            @elseif($results['operation'] == 'sales_returns')
                                                {{ number_format($item->total_amount, 2) }}
                                            @elseif($results['operation'] == 'withdrawals')
                                                {{ number_format($item->requested_amount, 2) }}
                                            @endif
                                            دينار
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $results['operation'] == 'payments' && request('stat_type') == 'marketers' ? ((request('stat_type') == 'stores') || (request('stat_type') == 'marketers' && in_array($results['operation'], ['sales', 'payments'])) ? '7' : '6') : ((request('stat_type') == 'stores' && request('store_id') == 'all') ? '6' : ((request('stat_type') == 'stores') || (request('stat_type') == 'marketers' && in_array($results['operation'], ['sales', 'payments'])) ? '5' : '4')) }}" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i data-lucide="inbox" class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-3"></i>
                                            <p class="text-gray-500 dark:text-gray-400">لا توجد نتائج</p>
                                        </div>
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
            @endif
        @endif

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        
        const statType = document.getElementById('stat_type');
        const filtersContainer = document.getElementById('filters_container');
        const storeField = document.getElementById('store_field');
        const marketerField = document.getElementById('marketer_field');
        const marketerStoreField = document.getElementById('marketer_store_field');
        const marketerSelect = document.getElementById('marketer_id');
        const marketerStoreSelect = document.getElementById('marketer_store_id');
        const operation = document.getElementById('operation');
        const statusSelect = document.getElementById('status');
        
        const storeOperations = [
            {value: 'summary', text: 'الملخص المالي'},
            {value: 'sales', text: 'فواتير البيع'},
            {value: 'payments', text: 'إيصالات القبض'},
            {value: 'returns', text: 'إرجاعات البضاعة'}
        ];
        
        const marketerOperations = [
            {value: 'requests', text: 'طلبات البضاعة'},
            {value: 'returns', text: 'إرجاعات البضاعة'},
            {value: 'sales_returns', text: 'إرجاعات المتاجر'},
            {value: 'sales', text: 'فواتير البيع'},
            {value: 'payments', text: 'إيصالات القبض'},
            {value: 'withdrawals', text: 'طلبات سحب الأرباح'}
        ];
        
        // Status options based on operation type
        const statusOptions = {
            // For requests and returns from marketer
            'requests': [
                {value: 'pending', text: 'معلق'},
                {value: 'approved', text: 'موافق عليه'},
                {value: 'documented', text: 'موثق'},
                {value: 'cancelled', text: 'ملغي'},
                {value: 'rejected', text: 'مرفوض'}
            ],
            'returns': [
                {value: 'pending', text: 'معلق'},
                {value: 'approved', text: 'موافق عليه'},
                {value: 'documented', text: 'موثق'},
                {value: 'cancelled', text: 'ملغي'},
                {value: 'rejected', text: 'مرفوض'}
            ],
            'sales_returns': [
                {value: 'pending', text: 'معلق'},
                {value: 'approved', text: 'موثق'},
                {value: 'cancelled', text: 'ملغي'},
                {value: 'rejected', text: 'مرفوض'}
            ],
            // For sales and payments
            'sales': [
                {value: 'pending', text: 'معلق'},
                {value: 'approved', text: 'موثق'},
                {value: 'cancelled', text: 'ملغي'},
                {value: 'rejected', text: 'مرفوض'}
            ],
            'payments': [
                {value: 'pending', text: 'معلق'},
                {value: 'approved', text: 'موثق'},
                {value: 'cancelled', text: 'ملغي'},
                {value: 'rejected', text: 'مرفوض'}
            ],
            'summary': [],
            'withdrawals': [
                {value: 'pending', text: 'معلق'},
                {value: 'approved', text: 'موثق'},
                {value: 'cancelled', text: 'ملغي'},
                {value: 'rejected', text: 'مرفوض'}
            ]
        };
        
        const selectedOperation = '{{ request('operation') }}';
        
        // Load marketer stores when marketer is selected
        marketerSelect.addEventListener('change', function() {
            const marketerId = this.value;
            // Only load if operation is sales or payments
            if (marketerId && ['sales', 'payments', 'sales_returns'].includes(operation.value)) {
                fetch(`{{ url('/admin/statistics/marketer-stores') }}/${marketerId}`)
                    .then(response => response.json())
                    .then(stores => {
                        marketerStoreSelect.innerHTML = '<option value="">الكل</option>';
                        stores.forEach(store => {
                            const option = document.createElement('option');
                            option.value = store.id;
                            option.text = store.name;
                            if ('{{ request('marketer_store_id') }}' == store.id) {
                                option.selected = true;
                            }
                            marketerStoreSelect.appendChild(option);
                        });
                    });
            } else {
                marketerStoreSelect.innerHTML = '<option value="">الكل</option>';
            }
        });
        
        statType.addEventListener('change', function() {
            if (this.value === 'stores') {
                filtersContainer.style.display = 'block';
                storeField.style.display = 'block';
                marketerField.style.display = 'none';
                marketerStoreField.style.display = 'none';
                updateOperations(storeOperations, selectedOperation);
            } else if (this.value === 'marketers') {
                filtersContainer.style.display = 'block';
                storeField.style.display = 'none';
                marketerField.style.display = 'block';
                updateOperations(marketerOperations, selectedOperation);
            } else {
                filtersContainer.style.display = 'none';
                storeField.style.display = 'none';
                marketerField.style.display = 'none';
                marketerStoreField.style.display = 'none';
                operation.innerHTML = '<option value="">اختر العملية...</option>';
            }
        });
        
        function updateOperations(ops, selected) {
            operation.innerHTML = '<option value="">اختر العملية...</option>';
            ops.forEach(op => {
                const option = document.createElement('option');
                option.value = op.value;
                option.text = op.text;
                if (selected && op.value === selected) {
                    option.selected = true;
                }
                operation.appendChild(option);
            });
            
            // Update status options when operation changes
            if (selected) {
                updateStatusOptions(selected);
            }
            
            // Show/hide marketer store field based on operation
            if (statType.value === 'marketers' && ['sales', 'payments', 'sales_returns'].includes(operation.value)) {
                marketerStoreField.style.display = 'block';
            } else {
                marketerStoreField.style.display = 'none';
            }
        }
        
        function updateStatusOptions(operationType) {
            const selectedStatus = '{{ request('status') }}';
            const options = statusOptions[operationType] || [];
            
            statusSelect.innerHTML = '<option value="">الكل</option>';
            options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.text = opt.text;
                if (selectedStatus && opt.value === selectedStatus) {
                    option.selected = true;
                }
                statusSelect.appendChild(option);
            });
        }
        
        // Listen to operation changes
        operation.addEventListener('change', function() {
            updateStatusOptions(this.value);
            
            // Show/hide status field for summary operation
            const statusField = statusSelect.closest('div');
            if (this.value === 'summary') {
                statusField.style.display = 'none';
            } else {
                statusField.style.display = 'block';
            }
            
            if (statType.value === 'marketers' && ['sales', 'payments', 'sales_returns'].includes(this.value)) {
                marketerStoreField.style.display = 'block';
                // Load stores when operation changes to sales or payments
                if (marketerSelect.value) {
                    fetch(`{{ url('/admin/statistics/marketer-stores') }}/${marketerSelect.value}`)
                        .then(response => response.json())
                        .then(stores => {
                            marketerStoreSelect.innerHTML = '<option value="">الكل</option>';
                            stores.forEach(store => {
                                const option = document.createElement('option');
                                option.value = store.id;
                                option.text = store.name;
                                if ('{{ request('marketer_store_id') }}' == store.id) {
                                    option.selected = true;
                                }
                                marketerStoreSelect.appendChild(option);
                            });
                        });
                }
            } else {
                marketerStoreField.style.display = 'none';
            }
        });
        
        // Initialize on load
        if (statType.value) {
            statType.dispatchEvent(new Event('change'));
        }
        
        // Initialize status options on load if operation is selected
        if (operation.value) {
            updateStatusOptions(operation.value);
        }
        
        // Load marketer stores on page load ONLY if operation is sales or payments
        if (marketerSelect.value && statType.value === 'marketers' && ['sales', 'payments', 'sales_returns'].includes(operation.value)) {
            fetch(`{{ url('/admin/statistics/marketer-stores') }}/${marketerSelect.value}`)
                .then(response => response.json())
                .then(stores => {
                    marketerStoreSelect.innerHTML = '<option value="">الكل</option>';
                    stores.forEach(store => {
                        const option = document.createElement('option');
                        option.value = store.id;
                        option.text = store.name;
                        if ('{{ request('marketer_store_id') }}' == store.id) {
                            option.selected = true;
                        }
                        marketerStoreSelect.appendChild(option);
                    });
                });
        }
    });
</script>
@endpush
@endsection
