@extends('layouts.app')

@section('title', 'الإحصائيات')

@section('content')

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
                    @if(request()->hasAny(['customer_id', 'operation', 'from_date', 'to_date']))
                        <button type="submit" name="export" value="1" class="flex-1 sm:flex-none px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all text-sm flex items-center justify-center gap-2">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            تصدير Excel
                        </button>
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
                                        @if($results['operation'] == 'invoices') {{ $item->invoice_number }}
                                        @elseif($results['operation'] == 'payments') {{ $item->payment_number }}
                                        @elseif($results['operation'] == 'returns') {{ $item->return_number }}
                                        @endif
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
@endpush
@endsection
