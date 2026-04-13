@extends('layouts.app')

@section('title', 'الإحصائيات')

@section('content')
@php
    $isSuperAdmin          = auth()->user()?->role_id === 5;
    $featureBulkPdf        = $isSuperAdmin || (\App\Models\Feature::where('key','admin.customer-statistics.bulk-pdf')->first()?->isCurrentlyEnabled() ?? true);
    $featureExportPdf      = $isSuperAdmin || (\App\Models\Feature::where('key','admin.customer-statistics.export-pdf')->first()?->isCurrentlyEnabled() ?? true);
    $featureInvoicePreview = $isSuperAdmin || (\App\Models\Feature::where('key','admin.customer-statistics.invoice-preview')->first()?->isCurrentlyEnabled() ?? true);
@endphp
<div class="min-h-screen py-6 sm:py-8">
    <div class="max-w-7xl mx-auto px-4">

        {{-- Header --}}
        <div class="mb-6 sm:mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">الإحصائيات</h1>
                    <p class="text-gray-500 dark:text-dark-muted mt-1 text-sm">تقارير وإحصائيات العملاء</p>
                </div>
                {{-- Quick Buttons --}}
                <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-2">
                    <button onclick="openQuickModal('invoices')" class="px-3 py-2 sm:px-5 sm:py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-md text-sm">
                        <i data-lucide="file-text" class="w-4 h-4 shrink-0"></i>
                        الفواتير
                    </button>
                    <button onclick="openQuickModal('payments')" class="px-3 py-2 sm:px-5 sm:py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-md text-sm">
                        <i data-lucide="banknote" class="w-4 h-4 shrink-0"></i>
                        المدفوعات
                    </button>
                    <button onclick="openQuickModal('returns')" class="px-3 py-2 sm:px-5 sm:py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-md text-sm">
                        <i data-lucide="package-x" class="w-4 h-4 shrink-0"></i>
                        المرتجعات
                    </button>
                    <button onclick="openQuickModal('summary')" class="px-3 py-2 sm:px-5 sm:py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-md text-sm">
                        <i data-lucide="wallet" class="w-4 h-4 shrink-0"></i>
                        الملخص المالي
                    </button>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl p-4 sm:p-6 border border-gray-200 dark:border-dark-border shadow-lg mb-6">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">اسم العميل</label>
                        <div class="flex gap-2"
                            x-data="{
                                search: '{{ request('customer_id') == 'all' ? 'الكل' : ($customers->firstWhere('id', request('customer_id'))?->name ?? request('customer_name', '')) }}',
                                selectedId: '{{ request('customer_id') ?? '' }}',
                                open: false,
                                customers: {{ Js::from($customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name])) }},
                                filtered: [],
                                init() { this.filtered = this.customers; },
                                filter() {
                                    this.selectedId = '';
                                    const q = this.search.toLowerCase();
                                    this.filtered = q ? this.customers.filter(c => c.name.toLowerCase().includes(q)) : this.customers;
                                    this.open = true;
                                },
                                select(id, name) {
                                    this.selectedId = id;
                                    this.search = name;
                                    this.open = false;
                                },
                                selectAll() {
                                    this.selectedId = 'all';
                                    this.search = 'الكل';
                                    this.open = false;
                                }
                            }"
                            @click.outside="open = false"
                        >
                            <div class="relative flex-1">
                                <input
                                    type="text"
                                    x-model="search"
                                    @focus="open = true"
                                    @input="filter()"
                                    placeholder="ابحث عن عميل..."
                                    autocomplete="off"
                                    class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 pr-10"
                                >
                                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute top-1/2 -translate-y-1/2 right-3 pointer-events-none"></i>
                                <input type="hidden" name="customer_id" x-model="selectedId">
                                <input type="hidden" name="customer_name" :value="selectedId ? '' : search">
                                <div
                                    x-show="open"
                                    x-transition
                                    class="absolute z-50 w-full mt-1 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-xl overflow-hidden"
                                    style="max-height: 220px; overflow-y: auto;"
                                >
                                    <div
                                        @click="selectAll()"
                                        class="px-4 py-2.5 text-sm font-bold text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-500/10 cursor-pointer border-b border-gray-100 dark:border-dark-border"
                                    >الكل</div>
                                    <template x-for="c in filtered" :key="c.id">
                                        <div
                                            @click="select(c.id, c.name)"
                                            class="px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-dark-bg cursor-pointer"
                                            x-text="c.name"
                                        ></div>
                                    </template>
                                    <div x-show="filtered.length === 0" class="px-4 py-3 text-sm text-gray-400 dark:text-gray-500 text-center">لا توجد نتائج</div>
                                </div>
                            </div>
                            <button
                                type="button"
                                @click="selectAll()"
                                :class="selectedId === 'all' ? 'bg-primary-600 text-white border-primary-600' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 border-gray-200 dark:border-dark-border hover:bg-gray-200 dark:hover:bg-gray-700'"
                                class="px-4 py-2.5 rounded-xl font-bold text-sm transition-all border shrink-0"
                            >الكل</button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">العملية</label>
                        <select name="operation" id="operation" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">اختر العملية...</option>
                            <option value="invoices" {{ request('operation') == 'invoices' ? 'selected' : '' }}>الفواتير</option>
                            <option value="payments" {{ request('operation') == 'payments' ? 'selected' : '' }}>المدفوعات</option>
                            <option value="returns" {{ request('operation') == 'returns' ? 'selected' : '' }}>المرتجعات</option>
                            <option value="summary" {{ request('operation') == 'summary' ? 'selected' : '' }}>الملخص المالي</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">الحالة</label>
                        <select name="status" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">الكل</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">الموظف</label>
                        <select name="sales_user_id" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">الكل</option>
                            @foreach($salesUsers as $user)
                                <option value="{{ $user->id }}" {{ request('sales_user_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name }}</option>
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
                </div>

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="flex-1 sm:flex-none px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center justify-center gap-2">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                        عرض الإحصائيات
                    </button>
                    @if(request()->hasAny(['customer_id', 'operation', 'from_date', 'to_date', 'sales_user_id']))
                        <button type="submit" name="export" value="1" class="flex-1 sm:flex-none px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all text-sm flex items-center justify-center gap-2">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            تصدير Excel
                        </button>
                        @if($featureExportPdf && $results && !isset($results['is_summary']))
                        <button type="submit" name="pdf" value="1" formtarget="_blank" class="flex-1 sm:flex-none px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all text-sm flex items-center justify-center gap-2">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            تصدير PDF
                        </button>
                        @endif
                        @if($featureBulkPdf && $results && !isset($results['is_summary']) && request('operation') !== 'summary')
                        <button type="button" onclick="openBulkModal()" class="flex-1 sm:flex-none px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-bold transition-all text-sm flex items-center justify-center gap-2">
                            <i data-lucide="files" class="w-4 h-4"></i>
                            تحميل كل الفواتير PDF
                        </button>
                        @endif
                        <a href="{{ route('admin.customer-statistics.index') }}" class="flex-1 sm:flex-none px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all text-sm flex items-center justify-center gap-2">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            إعادة تعيين
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Results --}}
        @if($results)
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-lg overflow-hidden">
                <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-dark-border">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-white">النتائج</h2>
                            @if(!request('status') && $results['operation'] != 'summary')
                            <div class="flex items-center gap-2 mt-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-500/30 rounded-lg">
                                <i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400 shrink-0"></i>
                                <p class="text-xs font-bold text-blue-700 dark:text-blue-400">يتم احتساب العمليات المكتملة فقط</p>
                            </div>
                            @endif
                        </div>
                        @if($results['operation'] == 'summary')
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 w-full mt-2 sm:mt-0">
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-500/30 rounded-xl p-3">
                                <p class="text-xs text-blue-700 dark:text-blue-400 font-bold mb-1">إجمالي الفواتير</p>
                                <p class="text-base font-black text-blue-600 dark:text-blue-400">{{ number_format($results['total'], 2) }} <span class="text-xs font-normal">د</span></p>
                            </div>
                            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-500/30 rounded-xl p-3">
                                <p class="text-xs text-green-700 dark:text-green-400 font-bold mb-1">إجمالي المدفوعات</p>
                                <p class="text-base font-black text-green-600 dark:text-green-400">{{ number_format($results['grand_payments'], 2) }} <span class="text-xs font-normal">د</span></p>
                            </div>
                            <div class="bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-500/30 rounded-xl p-3">
                                <p class="text-xs text-orange-700 dark:text-orange-400 font-bold mb-1">إجمالي المرتجعات</p>
                                <p class="text-base font-black text-orange-600 dark:text-orange-400">{{ number_format($results['grand_returns'], 2) }} <span class="text-xs font-normal">د</span></p>
                            </div>
                            <div class="{{ $results['grand_debt'] > 0 ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-500/30' : ($results['grand_debt'] < 0 ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-500/30' : 'bg-gray-50 dark:bg-dark-bg border-gray-200 dark:border-dark-border') }} border rounded-xl p-3">
                                <p class="text-xs font-bold mb-1 {{ $results['grand_debt'] > 0 ? 'text-red-700 dark:text-red-400' : ($results['grand_debt'] < 0 ? 'text-emerald-700 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-400') }}">الدين</p>
                                <p class="text-base font-black {{ $results['grand_debt'] > 0 ? 'text-red-600 dark:text-red-400' : ($results['grand_debt'] < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-700 dark:text-gray-300') }}">{{ number_format($results['grand_debt'], 2) }} <span class="text-xs font-normal">د</span></p>
                            </div>
                        </div>
                        @else
                        <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-3 sm:text-left">
                            <p class="text-xs text-gray-500 dark:text-gray-400">الإجمالي</p>
                            <p class="text-xl sm:text-2xl font-black text-primary-600 dark:text-primary-400">{{ number_format($results['total'], 2) }} دينار</p>
                        </div>
                        @endif
                    </div>
                    @if($results['operation'] == 'payments' && $results['paymentMethodTotals'])
                    <div class="mt-4 grid grid-cols-3 gap-2 sm:gap-3">
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-500/30 rounded-xl p-3">
                            <p class="text-xs text-green-700 dark:text-green-400 font-bold mb-1">نقدي</p>
                            <p class="text-base sm:text-lg font-black text-green-600 dark:text-green-400">{{ number_format($results['paymentMethodTotals']['cash'], 0) }} <span class="text-xs font-normal">د</span></p>
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-500/30 rounded-xl p-3">
                            <p class="text-xs text-blue-700 dark:text-blue-400 font-bold mb-1">تحويل</p>
                            <p class="text-base sm:text-lg font-black text-blue-600 dark:text-blue-400">{{ number_format($results['paymentMethodTotals']['transfer'], 0) }} <span class="text-xs font-normal">د</span></p>
                        </div>
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-500/30 rounded-xl p-3">
                            <p class="text-xs text-amber-700 dark:text-amber-400 font-bold mb-1">شيك</p>
                            <p class="text-base sm:text-lg font-black text-amber-600 dark:text-amber-400">{{ number_format($results['paymentMethodTotals']['check'], 0) }} <span class="text-xs font-normal">د</span></p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Desktop Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gradient-to-l from-gray-50 to-gray-100 dark:from-dark-bg dark:to-gray-800/50">
                            <tr>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    @if($results['operation'] == 'invoices') رقم الفاتورة
                                    @elseif($results['operation'] == 'payments') رقم الإيصال
                                    @elseif($results['operation'] == 'returns') رقم الإرجاع
                                    @elseif($results['operation'] == 'summary') العميل
                                    @endif
                                </th>
                                @if($results['operation'] != 'summary')
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">العميل</th>
                                <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الموظف</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">التاريخ</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الحالة</th>
                                @if($results['operation'] == 'payments')
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">طريقة الدفع</th>
                                @endif
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">المبلغ</th>
                                @if($results['operation'] == 'invoices')
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">المرتجعات</th>
                                @endif
                                @if($featureInvoicePreview)
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">عرض</th>
                                @endif
                                @else
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">إجمالي الفواتير</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">إجمالي المدفوعات</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">إجمالي المرتجعات</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">الدين</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-dark-border">
                            @forelse($results['data'] as $index => $item)
                                @if($results['operation'] == 'summary')
                                <tr class="hover:bg-purple-50/30 dark:hover:bg-purple-900/10 transition-all duration-200 {{ $index % 2 === 0 ? 'bg-white dark:bg-dark-card' : 'bg-gray-50/50 dark:bg-dark-bg/50' }}">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center shrink-0">
                                                <i data-lucide="user" class="w-4 h-4 text-purple-600 dark:text-purple-400"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900 dark:text-white">{{ $item->customer->name ?? '-' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->customer->phone ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center"><span class="inline-flex items-center justify-center min-w-[70px] px-3 py-1.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg text-sm font-black">{{ number_format($item->total_invoices, 0) }}</span></td>
                                    <td class="px-6 py-4 text-center"><span class="inline-flex items-center justify-center min-w-[70px] px-3 py-1.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-lg text-sm font-black">{{ number_format($item->total_payments, 0) }}</span></td>
                                    <td class="px-6 py-4 text-center"><span class="inline-flex items-center justify-center min-w-[70px] px-3 py-1.5 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded-lg text-sm font-black">{{ number_format($item->total_returns, 0) }}</span></td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center justify-center min-w-[70px] px-3 py-1.5 rounded-lg text-sm font-black
                                            {{ $item->total_debt > 0 ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : ($item->total_debt < 0 ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-gray-100 dark:bg-gray-900/30 text-gray-700 dark:text-gray-400') }}">
                                            {{ number_format($item->total_debt, 0) }}
                                        </span>
                                    </td>
                                </tr>
                                @else
                                @php
                                    $statusConfig = match($item->status) {
                                        'completed' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'مكتمل'],
                                        'cancelled' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'ملغي'],
                                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => $item->status]
                                    };
                                @endphp
                                <tr class="hover:bg-primary-50/30 dark:hover:bg-primary-900/10 transition-all duration-200 {{ $index % 2 === 0 ? 'bg-white dark:bg-dark-card' : 'bg-gray-50/50 dark:bg-dark-bg/50' }}">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                        @php
                                            $showUrl = match($results['operation']) {
                                                'invoices' => route('sales.invoices.show', $item->id),
                                                'payments' => route('sales.payments.show', $item->id),
                                                'returns'  => route('sales.returns.show', $item->id),
                                                default    => null,
                                            };
                                            $refNum = match($results['operation']) {
                                                'invoices' => $item->invoice_number,
                                                'payments' => $item->payment_number,
                                                'returns'  => $item->return_number,
                                                default    => '-',
                                            };
                                        @endphp
                                        <a href="{{ $showUrl }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">
                                            {{ $refNum }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->customer->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->salesUser->full_name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-600 dark:text-gray-400">{{ $item->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-2.5 py-1 rounded-lg text-xs font-bold">
                                            {{ $statusConfig['label'] }}
                                        </span>
                                    </td>
                                    @if($results['operation'] == 'payments')
                                    <td class="px-6 py-4 text-center text-sm text-gray-600 dark:text-gray-400">
                                        {{ $item->payment_method === 'cash' ? 'نقدي' : ($item->payment_method === 'transfer' ? 'تحويل' : 'شيك') }}
                                    </td>
                                    @endif
                                    <td class="px-6 py-4 text-center">
                                        <span class="font-black text-gray-900 dark:text-white">
                                            @if($results['operation'] == 'invoices') {{ number_format($item->total_amount, 0) }}
                                            @elseif($results['operation'] == 'payments') {{ number_format($item->amount, 0) }}
                                            @elseif($results['operation'] == 'returns') {{ number_format($item->total_amount, 0) }}
                                            @endif
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 mr-1">دينار</span>
                                    </td>
                                    @if($results['operation'] == 'invoices')
                                    <td class="px-6 py-4 text-center">
                                        @if($item->returns->count() > 0)
                                            @foreach($item->returns as $return)
                                                <span class="inline-block px-2 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded text-xs font-bold mr-1 mb-1">{{ $return->return_number }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-gray-400 dark:text-gray-600">-</span>
                                        @endif
                                    </td>
                                    @endif
                                    @if($featureInvoicePreview)
                                    @php
                                        $previewUrl = match($results['operation']) {
                                            'invoices' => route('admin.customer-statistics.invoice-data', $item->id),
                                            'payments' => route('admin.customer-statistics.payment-data', $item->id),
                                            'returns'  => route('admin.customer-statistics.return-data', $item->id),
                                            default    => null,
                                        };
                                        $pdfUrl = match($results['operation']) {
                                            'invoices' => route('admin.customer-statistics.invoice-pdf', $item->id),
                                            'payments' => route('admin.customer-statistics.payment-pdf', $item->id),
                                            'returns'  => route('admin.customer-statistics.return-pdf', $item->id),
                                            default    => null,
                                        };
                                        $previewLabel = match($results['operation']) {
                                            'invoices' => $item->invoice_number,
                                            'payments' => $item->payment_number,
                                            'returns'  => $item->return_number,
                                            default    => $item->id,
                                        };
                                    @endphp
                                    <td class="px-6 py-4 text-center">
                                        @if($previewUrl)
                                        <button type="button"
                                            onclick="openInvoiceModal('{{ $previewUrl }}', '{{ $previewLabel }}', '{{ $results['operation'] }}', '{{ $pdfUrl }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-50 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400 border border-primary-200 dark:border-primary-500/30 rounded-lg text-xs font-bold hover:bg-primary-100 dark:hover:bg-primary-500/20 transition-colors">
                                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                            عرض
                                        </button>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="{{ $results['operation'] == 'invoices' ? '7' : ($results['operation'] == 'payments' ? '7' : '6') }}" class="px-6 py-12 text-center">
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

                {{-- Mobile Cards --}}
                <div class="md:hidden divide-y divide-gray-100 dark:divide-dark-border">
                    @forelse($results['data'] as $item)
                        @if($results['operation'] == 'summary')
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center shrink-0">
                                    <i data-lucide="user" class="w-5 h-5 text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $item->customer->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->customer->phone ?? '' }}</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="bg-blue-50 dark:bg-blue-500/10 rounded-xl p-3 text-center">
                                    <p class="text-xs text-blue-600 dark:text-blue-400 font-bold mb-1">الفواتير</p>
                                    <p class="text-base font-black text-blue-700 dark:text-blue-300">{{ number_format($item->total_invoices, 0) }}</p>
                                </div>
                                <div class="bg-green-50 dark:bg-green-500/10 rounded-xl p-3 text-center">
                                    <p class="text-xs text-green-600 dark:text-green-400 font-bold mb-1">المدفوعات</p>
                                    <p class="text-base font-black text-green-700 dark:text-green-300">{{ number_format($item->total_payments, 0) }}</p>
                                </div>
                                <div class="bg-orange-50 dark:bg-orange-500/10 rounded-xl p-3 text-center">
                                    <p class="text-xs text-orange-600 dark:text-orange-400 font-bold mb-1">المرتجعات</p>
                                    <p class="text-base font-black text-orange-700 dark:text-orange-300">{{ number_format($item->total_returns, 0) }}</p>
                                </div>
                                <div class="rounded-xl p-3 text-center {{ $item->total_debt > 0 ? 'bg-red-50 dark:bg-red-500/10' : ($item->total_debt < 0 ? 'bg-emerald-50 dark:bg-emerald-500/10' : 'bg-gray-50 dark:bg-gray-500/10') }}">
                                    <p class="text-xs font-bold mb-1 {{ $item->total_debt > 0 ? 'text-red-600 dark:text-red-400' : ($item->total_debt < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-600 dark:text-gray-400') }}">الدين</p>
                                    <p class="text-base font-black {{ $item->total_debt > 0 ? 'text-red-700 dark:text-red-300' : ($item->total_debt < 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-gray-700 dark:text-gray-300') }}">{{ number_format($item->total_debt, 0) }}</p>
                                </div>
                            </div>
                        </div>
                        @else
                        @php
                            $statusConfig = match($item->status) {
                                'completed' => [
                                    'bg'        => 'bg-emerald-100 dark:bg-emerald-900/30',
                                    'text'      => 'text-emerald-700 dark:text-emerald-400',
                                    'icon'      => 'check-circle',
                                    'iconColor' => 'text-emerald-500 dark:text-emerald-400',
                                    'label'     => 'مكتمل',
                                ],
                                'cancelled' => [
                                    'bg'        => 'bg-red-100 dark:bg-red-900/30',
                                    'text'      => 'text-red-700 dark:text-red-400',
                                    'icon'      => 'x-circle',
                                    'iconColor' => 'text-red-500 dark:text-red-400',
                                    'label'     => 'ملغي',
                                ],
                                default => [
                                    'bg'        => 'bg-gray-100 dark:bg-gray-700/40',
                                    'text'      => 'text-gray-700 dark:text-gray-300',
                                    'icon'      => 'circle',
                                    'iconColor' => 'text-gray-400',
                                    'label'     => $item->status,
                                ],
                            };
                            $opIcon = match($results['operation']) {
                                'invoices' => 'file-text',
                                'payments' => 'banknote',
                                'returns'  => 'package-x',
                                default    => 'file',
                            };
                            $opColor = match($results['operation']) {
                                'invoices' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400',
                                'payments' => 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400',
                                'returns'  => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400',
                                default    => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400',
                            };
                            $amount = match($results['operation']) {
                                'payments' => $item->amount,
                                default    => $item->total_amount,
                            };
                            $refNumber = match($results['operation']) {
                                'invoices' => $item->invoice_number,
                                'payments' => $item->payment_number,
                                'returns'  => $item->return_number,
                                default    => '-',
                            };
                        @endphp
                        <div class="p-4 hover:bg-gray-50 dark:hover:bg-dark-bg/60 transition-colors">
                            {{-- Row 1: icon + ref number + status badge --}}
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 {{ $opColor }} rounded-xl flex items-center justify-center shrink-0">
                                    <i data-lucide="{{ $opIcon }}" class="w-5 h-5"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-black text-gray-900 dark:text-white text-sm leading-tight">{{ $refNumber }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 truncate">{{ $item->customer->name ?? '-' }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-1 shrink-0">
                                    <span class="inline-flex items-center gap-1 {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-2.5 py-1 rounded-lg text-xs font-bold">
                                        <i data-lucide="{{ $statusConfig['icon'] }}" class="w-3 h-3"></i>
                                        {{ $statusConfig['label'] }}
                                    </span>
                                    @if($results['operation'] == 'payments')
                                    @php
                                        $methodConfig = match($item->payment_method) {
                                            'cash'     => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-700 dark:text-green-400', 'icon' => 'banknote',     'label' => 'نقدي'],
                                            'transfer' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30',  'text' => 'text-blue-700 dark:text-blue-400',  'icon' => 'arrow-right-left', 'label' => 'تحويل'],
                                            'check'    => ['bg' => 'bg-amber-100 dark:bg-amber-900/30','text' => 'text-amber-700 dark:text-amber-400','icon' => 'file-check',      'label' => 'شيك'],
                                            default    => ['bg' => 'bg-gray-100 dark:bg-gray-700/40',  'text' => 'text-gray-600 dark:text-gray-400',  'icon' => 'circle',          'label' => $item->payment_method],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 {{ $methodConfig['bg'] }} {{ $methodConfig['text'] }} px-2.5 py-1 rounded-lg text-xs font-bold">
                                        <i data-lucide="{{ $methodConfig['icon'] }}" class="w-3 h-3"></i>
                                        {{ $methodConfig['label'] }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                            {{-- Row 2: meta info + amount --}}
                            <div class="flex items-center justify-between gap-2 bg-gray-50 dark:bg-dark-bg/60 rounded-xl px-3 py-2.5">
                                <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                        {{ $item->created_at->format('Y-m-d') }}
                                    </span>
                                    @if($results['operation'] == 'payments')
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                                        {{ $item->payment_method === 'cash' ? 'نقدي' : ($item->payment_method === 'transfer' ? 'تحويل' : 'شيك') }}
                                    </span>
                                    @endif
                                    <span class="flex items-center gap-1">
                                        <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                        {{ $item->salesUser->full_name ?? '-' }}
                                    </span>
                                </div>
                                <p class="font-black text-gray-900 dark:text-white text-sm shrink-0">
                                    {{ number_format($amount, 0) }}
                                    <span class="text-xs text-gray-400 dark:text-gray-500 font-normal">د</span>
                                </p>
                            </div>
                            {{-- Row 3: returns (invoices only) --}}
                            @if($results['operation'] == 'invoices' && $item->returns->count() > 0)
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <span class="text-xs text-gray-400 dark:text-gray-500 flex items-center gap-1">
                                    <i data-lucide="corner-down-left" class="w-3 h-3"></i>
                                    مرتجعات:
                                </span>
                                @foreach($item->returns as $return)
                                    <span class="px-2 py-0.5 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded-lg text-xs font-bold">{{ $return->return_number }}</span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                        @endif
                    @empty
                        <div class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i data-lucide="inbox" class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-3"></i>
                                <p class="text-gray-500 dark:text-gray-400">لا توجد نتائج</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if(method_exists($results['data'], 'hasPages') && $results['data']->hasPages())
                    <div class="px-4 sm:px-6 py-4 border-t border-gray-200 dark:border-dark-border">
                        {{ $results['data']->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        @endif

    </div>
</div>

{{-- Bulk PDF Modal --}}
<div id="bulkModal" class="fixed inset-0 z-[9999] items-center justify-center p-4" style="display:none">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeBulkModal()"></div>
    <div class="relative bg-white dark:bg-dark-card rounded-2xl shadow-2xl w-full max-w-md">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-dark-border">
            <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                <i data-lucide="files" class="w-5 h-5 text-violet-500"></i>
                تحميل الفواتير
            </h3>
            <button onclick="closeBulkModal()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-dark-bg text-gray-500 dark:text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div id="bulkModalLoading" class="flex items-center justify-center py-12">
            <div class="w-8 h-8 border-4 border-violet-200 border-t-violet-600 rounded-full animate-spin"></div>
        </div>
        <div id="bulkModalContent" class="px-6 py-5 space-y-4" style="display:none">
            <p class="text-sm text-gray-600 dark:text-gray-400">عدد النتائج: <span id="bulkCount" class="font-black text-gray-900 dark:text-white"></span></p>
            <a id="bulkSingleBtn" href="#" target="_blank"
               class="flex items-center gap-3 w-full px-4 py-3 bg-violet-50 dark:bg-violet-500/10 border border-violet-200 dark:border-violet-500/30 rounded-xl hover:bg-violet-100 dark:hover:bg-violet-500/20 transition-colors">
                <i data-lucide="file-down" class="w-5 h-5 text-violet-600 dark:text-violet-400 shrink-0"></i>
                <div>
                    <div class="font-bold text-sm text-violet-700 dark:text-violet-300">تحميل في ملف واحد</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">جميع الفواتير في PDF واحد</div>
                </div>
            </a>
            <div id="bulkChunksSection">
                <button type="button" onclick="toggleChunks()"
                        class="flex items-center gap-3 w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors text-right">
                    <i data-lucide="layers" class="w-5 h-5 text-gray-500 dark:text-gray-400 shrink-0"></i>
                    <div class="flex-1">
                        <div class="font-bold text-sm text-gray-700 dark:text-gray-300">تحميل على أجزاء</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">تقسيم الفواتير إلى ملفات أصغر</div>
                    </div>
                    <i data-lucide="chevron-down" id="chunksChevron" class="w-4 h-4 text-gray-400 transition-transform"></i>
                </button>
                <div id="chunksPanel" style="display:none" class="mt-3 space-y-3">
                    <div class="flex items-center gap-3">
                        <label class="text-xs font-bold text-gray-600 dark:text-gray-400 shrink-0">فواتير لكل ملف:</label>
                        <input type="number" id="chunkSize" min="50" max="70" value="70"
                               oninput="clampChunkSize(this); renderChunks()"
                               class="w-24 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border text-gray-900 dark:text-white rounded-lg px-3 py-1.5 text-sm text-center font-bold focus:outline-none focus:ring-2 focus:ring-violet-500">
                        <span class="text-xs text-gray-400 dark:text-gray-500">(50 - 70)</span>
                    </div>
                    <div id="chunksList" class="space-y-2"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Invoice Preview Modal --}}
<div id="invoiceModal" class="fixed inset-0 z-[9999] items-center justify-center p-4" style="display:none">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeInvoiceModal()"></div>
    <div class="relative bg-white dark:bg-dark-card rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-dark-border shrink-0">
            <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                <i data-lucide="file-text" class="w-5 h-5 text-primary-500"></i>
                <span id="modalInvoiceTitle">فاتورة</span>
            </h3>
            <div class="flex items-center gap-2">
                <a id="downloadPdfBtn" href="#" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-dark-bg text-white rounded-xl text-sm font-bold hover:bg-gray-700 transition-colors">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    تحميل PDF
                </a>
                <button onclick="closeInvoiceModal()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 dark:bg-dark-bg text-gray-500 dark:text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>
        <div id="invoiceModalBody" class="overflow-y-auto flex-1 p-6 bg-white dark:bg-dark-card">
            <div id="invoiceModalLoading" class="flex items-center justify-center py-20">
                <div class="w-10 h-10 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin"></div>
            </div>
            <div id="invoiceModalContent" style="display:none"></div>
        </div>
    </div>
</div>

{{-- Quick Date Modal --}}
<div id="quickDateModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-dark-card rounded-2xl shadow-2xl max-w-md w-full p-5 sm:p-6 border border-gray-200 dark:border-dark-border">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-black text-gray-900 dark:text-white">تحديد الفترة الزمنية</h3>
            <button onclick="closeQuickModal()" class="w-8 h-8 bg-gray-100 dark:bg-dark-bg rounded-lg flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-gray-600 dark:text-gray-400"></i>
            </button>
        </div>
        <form id="quickDateForm" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">من تاريخ</label>
                <input type="date" id="quickFromDate" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all dark:[color-scheme:dark]">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">إلى تاريخ</label>
                <input type="date" id="quickToDate" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all dark:[color-scheme:dark]">
            </div>
            <button type="submit" class="w-full px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                عرض النتائج
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentType = '';

    function openQuickModal(type) {
        currentType = type;
        const today = new Date().toISOString().split('T')[0];
        const firstDay = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
        document.getElementById('quickFromDate').value = firstDay;
        document.getElementById('quickToDate').value = today;
        document.getElementById('quickDateModal').classList.remove('hidden');
        lucide.createIcons();
    }

    function closeQuickModal() {
        document.getElementById('quickDateModal').classList.add('hidden');
    }

    document.getElementById('quickDateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const fromDate = document.getElementById('quickFromDate').value;
        const toDate = document.getElementById('quickToDate').value;
        const routes = {
            'invoices': '{{ route('admin.customer-statistics.quick-invoices') }}',
            'payments': '{{ route('admin.customer-statistics.quick-payments') }}',
            'returns': '{{ route('admin.customer-statistics.quick-returns') }}',
            'summary': '{{ route('admin.customer-statistics.quick-summary') }}'
        };
        window.location.href = routes[currentType] + '?from_date=' + fromDate + '&to_date=' + toDate;
    });

    document.getElementById('quickDateModal').addEventListener('click', function(e) {
        if (e.target === this) closeQuickModal();
    });

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>

<script>
let _currentInvoiceOperation = null;
let _bulkBaseUrl, _bulkCount, _chunksOpen = false;

function openInvoiceModal(url, number, operation, pdfUrl) {
    _currentInvoiceOperation = operation;
    const modal   = document.getElementById('invoiceModal');
    const loading = document.getElementById('invoiceModalLoading');
    const content = document.getElementById('invoiceModalContent');
    modal.style.display = 'flex';
    loading.style.display = 'flex';
    content.style.display = 'none';
    content.innerHTML = '';
    document.getElementById('modalInvoiceTitle').textContent = number;
    document.getElementById('downloadPdfBtn').href = pdfUrl || '#';
    fetch(url)
        .then(r => r.json())
        .then(data => {
            content._data = data;
            content.innerHTML = buildInvoiceHtml(data, operation);
            loading.style.display = 'none';
            content.style.display = 'block';
            lucide.createIcons();
        })
        .catch(() => {
            loading.innerHTML = '<p class="text-red-500 text-center py-8">حدث خطأ في تحميل الفاتورة</p>';
        });
}

new MutationObserver(() => {
    const content = document.getElementById('invoiceModalContent');
    if (content && content.style.display !== 'none' && content._data && _currentInvoiceOperation) {
        content.innerHTML = buildInvoiceHtml(content._data, _currentInvoiceOperation);
    }
}).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

function closeInvoiceModal() { document.getElementById('invoiceModal').style.display = 'none'; }

function buildInvoiceHtml(d, operation) {
    const dk = document.documentElement.classList.contains('dark');
    const T = {
        text: dk ? '#e2e8f0' : '#111111', sub: dk ? '#94a3b8' : '#374151',
        muted: dk ? '#64748b' : '#9CA3AF', border: dk ? '#2a354c' : '#E5E7EB',
        rowA: dk ? 'rgba(255,255,255,0.03)' : 'rgba(249,250,251,0.7)', rowB: 'transparent',
    };
    const fmt = v => { const n = parseFloat(String(v).replace(/,/g,'')); return isNaN(n) ? v : n.toLocaleString('en',{minimumFractionDigits:2,maximumFractionDigits:2}); };
    const statusMap = {
        completed: dk ? {label:'مكتمل', bg:'#064e3b', color:'#6ee7b7'} : {label:'مكتمل', bg:'#D1FAE5', color:'#065F46'},
        approved:  dk ? {label:'مكتمل', bg:'#064e3b', color:'#6ee7b7'} : {label:'مكتمل', bg:'#D1FAE5', color:'#065F46'},
        cancelled: dk ? {label:'ملغي',   bg:'#1f2937', color:'#9ca3af'} : {label:'ملغي',   bg:'#F3F4F6', color:'#374151'},
    };
    const st = statusMap[d.status] || statusMap.cancelled;
    const logoHtml = d.logo_base64 ? `<img src="data:image/png;base64,${d.logo_base64}" style="max-height:70px;max-width:130px;display:block">` : '';

    function makeHeader(badgeBg, badgeColor, badgeBorder, badgeText, title, subtitle) {
        return `<div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;color:${T.text}">
            <div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                    <span style="background:${badgeBg};color:${badgeColor};border:1px solid ${badgeBorder};padding:3px 10px;border-radius:6px;font-size:11px;font-weight:bold">${badgeText}</span>
                    <span style="color:${T.muted};font-size:11px">${d.date}</span>
                </div>
                <div style="font-size:24px;font-weight:900;color:${T.text}">${title}</div>
                ${subtitle ? `<div style="font-size:12px;color:${T.muted};margin-top:4px">${subtitle}</div>` : ''}
            </div>
            <div style="display:flex;flex-direction:column;align-items:flex-end;gap:8px">
                ${logoHtml}
                <span style="background:${st.bg};color:${st.color};padding:5px 14px;border-radius:10px;font-size:12px;font-weight:900">${st.label}</span>
            </div>
        </div>`;
    }
    function makeInfo(fields) {
        const cells = fields.map(([label, val]) => `<div><div style="font-size:11px;color:${T.muted};margin-bottom:3px">${label}</div><div style="font-weight:bold;font-size:14px;color:${T.text}">${val}</div></div>`).join('');
        return `<div style="border:1px solid ${T.border};border-radius:14px;padding:16px;margin-bottom:16px"><div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">${cells}</div></div>`;
    }
    function makeTable(heads, rows) {
        const ths = heads.map(h => `<th style="padding:10px 16px;text-align:${h.align||'right'};font-size:11px;font-weight:bold;color:${T.muted};border-bottom:1px solid ${T.border}">${h.label}</th>`).join('');
        const trs = rows.map((item, i) => `<tr style="background:${i%2===0?T.rowA:T.rowB}">
            <td style="padding:13px 16px;border-bottom:1px solid ${T.border};font-weight:bold;font-size:13px;color:${T.text}">${item.name}</td>
            <td style="padding:13px 16px;border-bottom:1px solid ${T.border};text-align:center"><span style="border:1px solid ${T.border};border-radius:10px;padding:3px 12px;font-weight:900;font-size:13px;color:${T.text}">${item.quantity}</span></td>
            ${item.price !== undefined ? `<td style="padding:13px 16px;border-bottom:1px solid ${T.border};text-align:center;font-size:13px;color:${T.sub}">${item.price} د</td>` : ''}
            ${item.total !== undefined ? `<td style="padding:13px 16px;border-bottom:1px solid ${T.border};text-align:center;font-weight:900;font-size:13px;color:${T.text}">${item.total} د</td>` : ''}
        </tr>`).join('');
        return `<div style="border:1px solid ${T.border};border-radius:14px;overflow:hidden;margin-bottom:16px"><table style="width:100%;border-collapse:collapse;color:${T.text}"><thead><tr>${ths}</tr></thead><tbody>${trs}</tbody></table></div>`;
    }
    function makeTotalBox(borderClr, divClr, label, value, valColor) {
        return `<div style="display:flex;justify-content:flex-end"><div style="border:2px solid ${borderClr};border-radius:18px;padding:20px 24px;min-width:280px;color:${T.text}"><div style="border-top:2px solid ${divClr};padding-top:12px;display:flex;justify-content:space-between;align-items:baseline"><span style="font-size:15px;font-weight:bold;color:${T.text}">${label}</span><span style="font-size:28px;font-weight:900;color:${valColor}">${fmt(value)} <span style="font-size:14px;font-weight:bold;color:${T.muted}">د</span></span></div></div></div>`;
    }
    const wrap = c => `<div dir="rtl" style="font-family:Cairo,Arial,sans-serif;color:${T.text}">${c}</div>`;

    if (operation === 'invoices') {
        const heads = [{label:'المنتج'},{label:'الكمية',align:'center'},{label:'السعر',align:'center'},{label:'الإجمالي',align:'center'}];
        const discounts = [
            parseFloat(String(d.invoice_discount).replace(/,/g,'')) > 0 ? `<div style="display:flex;justify-content:space-between;font-size:14px;padding:4px 0"><span style="color:${dk?'#60a5fa':'#2563EB'};font-weight:600">خصم الفاتورة:</span><span style="color:${dk?'#60a5fa':'#2563EB'};font-weight:900">- ${fmt(d.invoice_discount)} د</span></div>` : '',
        ].join('');
        const summary = `<div style="display:flex;justify-content:flex-end"><div style="border:2px solid ${dk?'#4338ca':'#C7D2FE'};border-radius:18px;padding:20px 24px;min-width:280px;color:${T.text}">
            <div style="display:flex;justify-content:space-between;font-size:14px;padding:4px 0"><span style="color:${T.sub};font-weight:600">عدد البضاعة:</span><span style="font-weight:900;color:${T.text}">${d.total_items}</span></div>
            <div style="display:flex;justify-content:space-between;font-size:14px;padding:4px 0"><span style="color:${T.sub};font-weight:600">المجموع الفرعي:</span><span style="font-weight:900;color:${T.text}">${fmt(d.subtotal)} د</span></div>
            ${discounts}
            <div style="border-top:2px solid ${dk?'#818cf8':'#818CF8'};margin-top:10px;padding-top:12px;display:flex;justify-content:space-between;align-items:baseline">
                <span style="font-size:15px;font-weight:bold;color:${T.text}">الإجمالي النهائي:</span>
                <span style="font-size:28px;font-weight:900;color:${dk?'#93c5fd':'#1D4ED8'}">${fmt(d.total)} <span style="font-size:14px;font-weight:bold;color:${T.muted}">د</span></span>
            </div>
        </div></div>`;
        return wrap(
            makeHeader(dk?'#1e1b4b':'#EEF2FF', dk?'#a5b4fc':'#4F46E5', dk?'#4338ca':'#C7D2FE', 'فاتورة مبيعات', `فاتورة #${d.invoice_number}`, null) +
            makeInfo([['العميل', d.store],['رقم الهاتف', d.store_phone],['الموظف', d.marketer]]) +
            makeTable(heads, d.items) + summary
        );
    }
    if (operation === 'payments') {
        return wrap(
            makeHeader(dk?'#052e16':'#ECFDF5', dk?'#4ade80':'#065F46', dk?'#16a34a':'#A7F3D0', 'إيصال قبض', `إيصال #${d.payment_number}`, null) +
            makeInfo([['العميل', d.store],['رقم الهاتف', d.store_phone],['الموظف', d.marketer],['طريقة الدفع', d.payment_method]]) +
            makeTotalBox(dk?'#16a34a':'#6EE7B7', dk?'#22c55e':'#34D399', 'المبلغ المسدد:', d.amount, dk?'#4ade80':'#065F46')
        );
    }
    if (operation === 'returns') {
        const heads = [{label:'المنتج'},{label:'الكمية',align:'center'},{label:'السعر',align:'center'},{label:'الإجمالي',align:'center'}];
        return wrap(
            makeHeader(dk?'#431407':'#FFF7ED', dk?'#fb923c':'#9A3412', dk?'#c2410c':'#FED7AA', 'مرتجع عميل', `مرتجع #${d.return_number}`, `فاتورة أصلية: ${d.invoice_number}`) +
            makeInfo([['العميل', d.store],['رقم الهاتف', d.store_phone],['الموظف', d.marketer]]) +
            makeTable(heads, d.items.map(i => ({name: i.name, quantity: i.quantity, price: i.unit_price, total: i.total_price}))) +
            makeTotalBox(dk?'#c2410c':'#FDBA74', dk?'#ea580c':'#FB923C', 'الإجمالي:', d.total, dk?'#fb923c':'#9A3412')
        );
    }
    return '';
}

function openBulkModal() {
    const modal = document.getElementById('bulkModal');
    document.getElementById('bulkModalLoading').style.display = 'flex';
    document.getElementById('bulkModalContent').style.display = 'none';
    document.getElementById('chunksPanel').style.display = 'none';
    _chunksOpen = false;
    document.getElementById('chunksChevron').style.transform = '';
    modal.style.display = 'flex';
    lucide.createIcons();

    const countUrl = new URL('{{ route("admin.customer-statistics.bulk-invoices-count") }}', window.location.origin);
    new URLSearchParams(window.location.search).forEach((v, k) => countUrl.searchParams.set(k, v));

    _bulkBaseUrl = new URL('{{ route("admin.customer-statistics.bulk-invoices-pdf") }}', window.location.origin);
    new URLSearchParams(window.location.search).forEach((v, k) => _bulkBaseUrl.searchParams.set(k, v));

    fetch(countUrl)
        .then(r => r.json())
        .then(data => {
            _bulkCount = data.count;
            document.getElementById('bulkCount').textContent = _bulkCount;
            document.getElementById('bulkSingleBtn').href = _bulkBaseUrl.toString();
            document.getElementById('bulkModalLoading').style.display = 'none';
            document.getElementById('bulkModalContent').style.display = 'block';
            lucide.createIcons();
        });
}
function closeBulkModal() { document.getElementById('bulkModal').style.display = 'none'; }
function toggleChunks() {
    _chunksOpen = !_chunksOpen;
    document.getElementById('chunksPanel').style.display = _chunksOpen ? 'block' : 'none';
    document.getElementById('chunksChevron').style.transform = _chunksOpen ? 'rotate(180deg)' : '';
    if (_chunksOpen) renderChunks();
}
function clampChunkSize(el) {
    let v = el.value.replace(/[^0-9]/g, '');
    if (!v) { el.value = ''; return; }
    const n = parseInt(v);
    if (v.length >= 2) { el.value = String(Math.min(70, Math.max(50, n))); }
    else { el.value = v; }
}
function renderChunks() {
    const size = Math.min(70, Math.max(50, parseInt(document.getElementById('chunkSize').value) || 70));
    const list = document.getElementById('chunksList');
    list.innerHTML = '';
    for (let offset = 0; offset < _bulkCount; offset += size) {
        const end = Math.min(offset + size, _bulkCount);
        const url = new URL(_bulkBaseUrl.toString());
        url.searchParams.set('offset', offset);
        url.searchParams.set('limit', size);
        const a = document.createElement('a');
        a.href = url.toString();
        a.target = '_blank';
        a.className = 'flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-dark-bg border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl hover:bg-violet-50 dark:hover:bg-violet-500/10 hover:border-violet-300 transition-colors';
        a.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-violet-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg><span class="text-sm font-bold">فواتير ${offset + 1} - ${end}</span><span class="text-xs text-gray-400 mr-auto">(${end - offset} فاتورة)</span>`;
        list.appendChild(a);
    }
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeInvoiceModal(); closeBulkModal(); } });
</script>
@endpush
@endsection
