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
                            <div class="flex gap-2"
                                x-data="{
                                    search: '{{ request('store_id') == 'all' ? 'الكل' : ($stores->firstWhere('id', request('store_id'))?->name ?? '') }}',
                                    selectedId: '{{ request('store_id') ?? '' }}',
                                    open: false,
                                    stores: {{ Js::from($stores->map(fn($s) => ['id' => $s->id, 'name' => $s->name])) }},
                                    filtered: [],
                                    init() { this.filtered = this.stores; },
                                    filter() {
                                        this.selectedId = '';
                                        const q = this.search.toLowerCase();
                                        this.filtered = q ? this.stores.filter(s => s.name.toLowerCase().includes(q)) : this.stores;
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
                                        placeholder="ابحث عن متجر..."
                                        autocomplete="off"
                                        class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 pr-10"
                                    >
                                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute top-1/2 -translate-y-1/2 right-3 pointer-events-none"></i>
                                    <input type="hidden" name="store_id" x-model="selectedId">
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
                                        <template x-for="s in filtered" :key="s.id">
                                            <div
                                                @click="select(s.id, s.name)"
                                                class="px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-dark-bg cursor-pointer"
                                                x-text="s.name"
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

                        <div id="marketer_field" style="display: {{ request('stat_type') == 'marketers' ? 'block' : 'none' }}">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">اسم المسوق</label>
                            <select name="marketer_id" id="marketer_id" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">اختر المسوق...</option>
                                <option value="all" {{ request('marketer_id') == 'all' ? 'selected' : '' }}>الكل</option>
                                @foreach($marketers as $marketer)
                                    <option value="{{ $marketer->id }}" {{ request('marketer_id') == $marketer->id ? 'selected' : '' }}>{{ $marketer->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="marketer_store_field" style="display: {{ request('stat_type') == 'marketers' && in_array(request('operation'), ['sales', 'payments', 'sales_returns']) ? 'block' : 'none' }}">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">المتجر (اختياري)</label>
                            <div
                                x-data="{
                                    search: '{{ request('marketer_store_id') ? '' : '' }}',
                                    selectedId: '{{ request('marketer_store_id') ?? '' }}',
                                    open: false,
                                    stores: [],
                                    filtered: [],
                                    init() { this.filtered = this.stores; },
                                    setStores(list) {
                                        this.stores = list;
                                        this.filtered = list;
                                        const cur = this.stores.find(s => s.id == this.selectedId);
                                        this.search = cur ? cur.name : '';
                                    },
                                    filter() {
                                        this.selectedId = '';
                                        const q = this.search.toLowerCase();
                                        this.filtered = q ? this.stores.filter(s => s.name.toLowerCase().includes(q)) : this.stores;
                                        this.open = true;
                                    },
                                    select(id, name) {
                                        this.selectedId = id;
                                        this.search = name;
                                        this.open = false;
                                    },
                                    clear() {
                                        this.selectedId = '';
                                        this.search = '';
                                        this.open = false;
                                    }
                                }"
                                x-ref="marketerStoreWidget"
                                @click.outside="open = false"
                                id="marketer_store_widget"
                            >
                                <div class="relative">
                                    <input
                                        type="text"
                                        x-model="search"
                                        @focus="open = true"
                                        @input="filter()"
                                        placeholder="ابحث عن متجر..."
                                        autocomplete="off"
                                        class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 pr-10"
                                    >
                                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute top-1/2 -translate-y-1/2 right-3 pointer-events-none"></i>
                                    <input type="hidden" name="marketer_store_id" x-model="selectedId">
                                    <div
                                        x-show="open"
                                        x-transition
                                        class="absolute z-50 w-full mt-1 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-xl overflow-hidden"
                                        style="max-height: 220px; overflow-y: auto;"
                                    >
                                        <div
                                            @click="clear()"
                                            class="px-4 py-2.5 text-sm font-bold text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-500/10 cursor-pointer border-b border-gray-100 dark:border-dark-border"
                                        >الكل</div>
                                        <template x-for="s in filtered" :key="s.id">
                                            <div
                                                @click="select(s.id, s.name)"
                                                class="px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-dark-bg cursor-pointer"
                                                x-text="s.name"
                                            ></div>
                                        </template>
                                        <div x-show="filtered.length === 0" class="px-4 py-3 text-sm text-gray-400 dark:text-gray-500 text-center">لا توجد نتائج</div>
                                    </div>
                                </div>
                            </div>
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

                    <div id="status_field">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">الحالة</label>
                        <div id="status_checkboxes" class="flex flex-wrap gap-2 mt-1"></div>
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
                        <button type="submit" name="pdf" value="1" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            تصدير PDF
                        </button>
                        @if(!in_array(request('operation'), ['summary', '']))
                        <button type="button" onclick="openBulkModal()" class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                            <i data-lucide="files" class="w-4 h-4"></i>
                            تحميل كل الفواتير PDF
                        </button>
                        @endif
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
                @if(isset($results['is_marketer_summary']) && $results['is_marketer_summary'])
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
                                <div class="bg-{{ $results['remaining'] >= 0 ? 'blue' : 'red' }}-50 dark:bg-{{ $results['remaining'] >= 0 ? 'blue' : 'red' }}-900/20 rounded-xl p-4 border border-{{ $results['remaining'] >= 0 ? 'blue' : 'red' }}-200 dark:border-{{ $results['remaining'] >= 0 ? 'blue' : 'red' }}-800">
                                    <p class="text-sm text-{{ $results['remaining'] >= 0 ? 'blue' : 'red' }}-600 dark:text-{{ $results['remaining'] >= 0 ? 'blue' : 'red' }}-400 font-bold mb-2">المتبقي</p>
                                    <p class="text-2xl font-black text-{{ $results['remaining'] >= 0 ? 'blue' : 'red' }}-700 dark:text-{{ $results['remaining'] >= 0 ? 'blue' : 'red' }}-300">{{ number_format($results['remaining'], 2) }} دينار</p>
                                </div>
                            </div>
                        </div>
                        
                        @if(isset($results['marketers_data']) && count($results['marketers_data']) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                                    <tr>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المسوق</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">إجمالي الأرباح</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">إجمالي المسحوب</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المتبقي</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                                    @foreach($results['marketers_data'] as $marketerData)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">{{ $marketerData['marketer_name'] }}</td>
                                            <td class="px-6 py-4 text-sm text-emerald-600 dark:text-emerald-400 font-bold">{{ number_format($marketerData['commissions'], 2) }}</td>
                                            <td class="px-6 py-4 text-sm text-amber-600 dark:text-amber-400 font-bold">{{ number_format($marketerData['withdrawals'], 2) }}</td>
                                            <td class="px-6 py-4 text-sm font-bold {{ $marketerData['balance'] >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-red-600 dark:text-red-400' }}">{{ number_format($marketerData['balance'], 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                @else
                {{-- Store Financial Summary --}}
                <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200 dark:border-dark-border">
                        <h2 class="text-xl font-black text-gray-900 dark:text-white mb-6">الملخص المالي</h2>

                        <div class="flex flex-col lg:flex-row gap-4">
                            <div class="flex-1 space-y-3">
                                <div class="grid grid-cols-3 divide-x divide-x-reverse divide-gray-100 dark:divide-dark-border border border-gray-100 dark:border-dark-border rounded-2xl overflow-hidden">
                                    <div class="p-4 text-center">
                                        <div class="w-8 h-8 bg-blue-50 dark:bg-blue-500/10 rounded-lg flex items-center justify-center text-blue-500 mx-auto mb-2"><i data-lucide="shopping-cart" class="w-4 h-4"></i></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">المبيعات</div>
                                        <div class="text-sm font-black text-blue-600 dark:text-blue-400">{{ number_format($results['total_sales'], 2) }}</div>
                                    </div>
                                    <div class="p-4 text-center">
                                        <div class="w-8 h-8 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg flex items-center justify-center text-emerald-500 mx-auto mb-2"><i data-lucide="banknote" class="w-4 h-4"></i></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">المدفوعات</div>
                                        <div class="text-sm font-black text-emerald-600 dark:text-emerald-400">{{ number_format($results['total_payments'], 2) }}</div>
                                    </div>
                                    <div class="p-4 text-center">
                                        <div class="w-8 h-8 bg-orange-50 dark:bg-orange-500/10 rounded-lg flex items-center justify-center text-orange-500 mx-auto mb-2"><i data-lucide="package-x" class="w-4 h-4"></i></div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">المرتجعات</div>
                                        <div class="text-sm font-black text-orange-600 dark:text-orange-400">{{ number_format($results['total_returns'], 2) }}</div>
                                    </div>
                                </div>

                                @if($results['pending_sales'] > 0 || $results['pending_payments'] > 0 || $results['pending_returns'] > 0)
                                <div class="space-y-2">
                                    @if($results['pending_sales'] > 0)
                                    <div class="flex items-center justify-between bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 rounded-xl px-4 py-2.5">
                                        <div class="flex items-center gap-2 text-blue-700 dark:text-blue-400"><i data-lucide="clock" class="w-3.5 h-3.5"></i><span class="text-xs font-bold">فواتير معلقة</span></div>
                                        <span class="text-xs font-black text-blue-700 dark:text-blue-300">+ {{ number_format($results['pending_sales'], 2) }} دينار</span>
                                    </div>
                                    @endif
                                    @if($results['pending_payments'] > 0)
                                    <div class="flex items-center justify-between bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 rounded-xl px-4 py-2.5">
                                        <div class="flex items-center gap-2 text-emerald-700 dark:text-emerald-400"><i data-lucide="clock" class="w-3.5 h-3.5"></i><span class="text-xs font-bold">إيصالات معلقة</span></div>
                                        <span class="text-xs font-black text-emerald-700 dark:text-emerald-300">- {{ number_format($results['pending_payments'], 2) }} دينار</span>
                                    </div>
                                    @endif
                                    @if($results['pending_returns'] > 0)
                                    <div class="flex items-center justify-between bg-orange-50 dark:bg-orange-500/10 border border-orange-200 dark:border-orange-500/30 rounded-xl px-4 py-2.5">
                                        <div class="flex items-center gap-2 text-orange-700 dark:text-orange-400"><i data-lucide="clock" class="w-3.5 h-3.5"></i><span class="text-xs font-bold">مرتجعات معلقة</span></div>
                                        <span class="text-xs font-black text-orange-700 dark:text-orange-300">- {{ number_format($results['pending_returns'], 2) }} دينار</span>
                                    </div>
                                    @endif
                                </div>
                                @endif
                            </div>

                            <div class="w-full lg:w-96 shrink-0">
                                <div class="relative rounded-2xl overflow-hidden h-full">
                                    <div class="absolute inset-0 bg-gradient-to-br from-orange-500 via-orange-600 to-amber-700"></div>
                                    <div class="absolute inset-0 opacity-20"><div class="absolute -top-4 -left-4 w-32 h-32 bg-white rounded-full"></div><div class="absolute -bottom-6 -right-6 w-40 h-40 bg-white rounded-full"></div></div>
                                    <div class="relative p-6">
                                        <div class="flex items-center justify-between mb-3">
                                            <span class="text-white text-sm font-black tracking-wide">إجمالي الدين</span>
                                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center"><i data-lucide="trending-up" class="w-4 h-4 text-white"></i></div>
                                        </div>
                                        <div class="text-3xl font-black text-white tracking-tight">
                                            {{ number_format(abs($results['current_balance']), 2) }}
                                            <span class="text-base font-bold text-white/70">دينار</span>
                                        </div>
                                        @if($results['pending_sales'] > 0 || $results['pending_payments'] > 0 || $results['pending_returns'] > 0)
                                        <div class="mt-3 pt-3 border-t border-white/20 grid grid-cols-2 gap-3 text-sm">
                                            <div class="bg-white/20 rounded-xl px-4 py-3">
                                                <div class="text-white/80 text-xs font-bold mb-1">معتمد</div>
                                                <div class="text-white font-black text-base">{{ number_format($results['confirmed_balance'], 2) }} دينار</div>
                                            </div>
                                            <div class="bg-white/20 rounded-xl px-4 py-3">
                                                <div class="text-white/80 text-xs font-bold mb-1">معلق</div>
                                                <div class="text-white font-black text-base">{{ number_format($results['pending_net'], 2) }} دينار</div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    @if(isset($results['stores_data']) && count($results['stores_data']) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المتجر</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-blue-600 dark:text-blue-400">المبيعات</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-emerald-600 dark:text-emerald-400">المدفوعات</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-orange-600 dark:text-orange-400">المرتجعات</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">الدين</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">دائن / مدين</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                                @foreach($results['stores_data'] as $storeData)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                                        <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">{{ $storeData['store_name'] }}</td>
                                        <td class="px-6 py-4 text-sm text-blue-600 dark:text-blue-400 font-bold">{{ number_format($storeData['sales'], 2) }}</td>
                                        <td class="px-6 py-4 text-sm text-emerald-600 dark:text-emerald-400 font-bold">{{ number_format($storeData['payments'], 2) }}</td>
                                        <td class="px-6 py-4 text-sm text-orange-600 dark:text-orange-400 font-bold">{{ number_format($storeData['returns'], 2) }}</td>
                                        <td class="px-6 py-4 text-sm font-black {{ $storeData['balance'] > 0 ? 'text-red-600 dark:text-red-400' : ($storeData['balance'] < 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-400') }}">{{ number_format($storeData['balance'], 2) }}</td>
                                        <td class="px-6 py-4">
                                            @if($storeData['balance'] > 0)
                                                <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-2 py-0.5 rounded-lg text-xs font-black">مدين</span>
                                            @elseif($storeData['balance'] < 0)
                                                <span class="bg-emerald-500 text-white px-2 py-0.5 rounded-lg text-xs font-black">دائن</span>
                                            @else
                                                <span class="text-gray-400 text-xs">--</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                    </div>
                @endif
            @else
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-dark-border">
                    <h2 class="text-xl font-black text-gray-900 dark:text-white mb-4">النتائج</h2>
                    @if((request('stat_type') == 'stores' || request('stat_type') == 'marketers') && !request('status') && !request()->filled('statuses') && isset($results['status_totals']) && !in_array($results['operation'], ['requests', 'returns']))
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
                    @if((request('stat_type') == 'stores' || request('stat_type') == 'marketers') && !request('status') && !request()->filled('statuses'))
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
                                @elseif(request('stat_type') == 'marketers')
                                    @if(request('marketer_id') == 'all')
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المسوق</th>
                                    @endif
                                    @if(in_array($results['operation'], ['sales', 'payments', 'sales_returns']))
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المتجر</th>
                                    @endif
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
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-400">عرض</th>
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
                                        @php
                                            $showUrl = match($results['operation']) {
                                                'sales'         => route('warehouse.sales.show', $item->id),
                                                'payments'      => route('warehouse.payments.show', $item->id),
                                                'sales_returns' => route('warehouse.sales-returns.show', $item->id),
                                                'requests'      => route('warehouse.requests.show', $item->id),
                                                'returns'       => route('warehouse.returns.show', $item->id),
                                                'withdrawals'   => route('admin.withdrawals.show', $item->id),
                                                default         => null,
                                            };
                                        @endphp
                                        @if($showUrl)
                                            <a href="{{ $showUrl }}" target="_blank" class="text-primary-600 dark:text-primary-400 hover:underline">
                                        @endif
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
                                        @if($showUrl)
                                            </a>
                                        @endif
                                    </td>
                                    @if(request('stat_type') == 'stores')
                                        @if(request('store_id') == 'all')
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->store->name ?? '-' }}</td>
                                        @endif
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->marketer->full_name ?? '-' }}</td>
                                    @elseif(request('stat_type') == 'marketers')
                                        @if(request('marketer_id') == 'all')
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->marketer->full_name ?? '-' }}</td>
                                        @endif
                                        @if(in_array($results['operation'], ['sales', 'payments', 'sales_returns']))
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->store->name ?? '-' }}</td>
                                        @endif
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
                                    @php
                                        $invoiceDataUrl = match($results['operation']) {
                                            'sales'         => route('warehouse.sales.invoice-data', $item->id),
                                            'payments'      => route('warehouse.payments.invoice-data', $item->id),
                                            'sales_returns' => route('warehouse.sales-returns.invoice-data', $item->id),
                                            'requests'      => route('warehouse.requests.invoice-data', $item->id),
                                            'returns'       => route('warehouse.returns.invoice-data', $item->id),
                                            'withdrawals'   => route('admin.withdrawals.invoice-data', $item->id),
                                            default         => null,
                                        };
                                        $invoiceLabel = match($results['operation']) {
                                            'sales'         => $item->invoice_number,
                                            'payments'      => $item->payment_number,
                                            'sales_returns' => $item->return_number,
                                            'requests'      => $item->invoice_number,
                                            'returns'       => $item->invoice_number,
                                            'withdrawals'   => 'WD-' . $item->id,
                                            default         => $item->id,
                                        };
                                    @endphp
                                    <td class="px-6 py-4 text-center">
                                        @if($invoiceDataUrl)
                                        <button
                                            type="button"
                                            onclick="openInvoiceModal('{{ $invoiceDataUrl }}', '{{ $invoiceLabel }}', '{{ $results['operation'] }}')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-50 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400 border border-primary-200 dark:border-primary-500/30 rounded-lg text-xs font-bold hover:bg-primary-100 dark:hover:bg-primary-500/20 transition-colors">
                                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                                            عرض
                                        </button>
                                        @endif
                                    </td>
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

        {{-- loading --}}
        <div id="bulkModalLoading" class="flex items-center justify-center py-12">
            <div class="w-8 h-8 border-4 border-violet-200 border-t-violet-600 rounded-full animate-spin"></div>
        </div>

        {{-- content --}}
        <div id="bulkModalContent" class="px-6 py-5 space-y-4" style="display:none">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                عدد النتائج: <span id="bulkCount" class="font-black text-gray-900 dark:text-white"></span>
            </p>

            {{-- ملف واحد --}}
            <a id="bulkSingleBtn" href="#" target="_blank"
               class="flex items-center gap-3 w-full px-4 py-3 bg-violet-50 dark:bg-violet-500/10 border border-violet-200 dark:border-violet-500/30 rounded-xl hover:bg-violet-100 dark:hover:bg-violet-500/20 transition-colors">
                <i data-lucide="file-down" class="w-5 h-5 text-violet-600 dark:text-violet-400 shrink-0"></i>
                <div>
                    <div class="font-bold text-sm text-violet-700 dark:text-violet-300">تحميل في ملف واحد</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">جميع الفواتير في PDF واحد</div>
                </div>
            </a>

            {{-- دفعات --}}
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
<div id="invoiceModal" class="fixed inset-0 z-[9999] items-center justify-center p-4" style="display:none">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeInvoiceModal()"></div>
    <div class="relative bg-white dark:bg-dark-card rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-dark-border shrink-0">
            <h3 class="text-lg font-black text-gray-900 dark:text-white flex items-center gap-2">
                <i data-lucide="file-text" class="w-5 h-5 text-primary-500"></i>
                <span id="modalInvoiceTitle">فاتورة بيع</span>
            </h3>
            <div class="flex items-center gap-2">
                <a id="downloadPdfBtn" href="#" download class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-dark-bg text-white rounded-xl text-sm font-bold hover:bg-gray-700 transition-colors">
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
            {value: 'summary', text: 'الملخص المالي'},
            {value: 'requests', text: 'طلبات البضاعة'},
            {value: 'returns', text: 'إرجاعات البضاعة'},
            {value: 'sales_returns', text: 'إرجاعات المتاجر'},
            {value: 'sales', text: 'فواتير البيع'},
            {value: 'payments', text: 'إيصالات القبض'},
            {value: 'withdrawals', text: 'طلبات سحب الأرباح'}
        ];
        
        // Status options based on operation type
        const statusOptions = {
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
                {value: 'debt', text: 'الدين', color: 'orange'},
                {value: 'pending', text: 'معلق'},
                {value: 'approved', text: 'موثق'},
                {value: 'cancelled', text: 'ملغي'},
                {value: 'rejected', text: 'مرفوض'}
            ],
            'sales': [
                {value: 'debt', text: 'الدين', color: 'orange'},
                {value: 'pending', text: 'معلق'},
                {value: 'approved', text: 'موثق'},
                {value: 'cancelled', text: 'ملغي'},
                {value: 'rejected', text: 'مرفوض'}
            ],
            'payments': [
                {value: 'debt', text: 'الدين', color: 'orange'},
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
        
        const selectedStatuses = {!! json_encode(array_filter((array) request('statuses', []))) !!};
        const selectedStatusLegacy = '{{ request('status') }}';
        const selectedOperation = '{{ request('operation') }}';
        const statusContainer = document.getElementById('status_checkboxes');
        const statusField = document.getElementById('status_field');

        function updateStatusOptions(operationType) {
            const options = statusOptions[operationType] || [];
            statusContainer.innerHTML = '';

            if (options.length === 0) {
                statusField.style.display = 'none';
                return;
            }
            statusField.style.display = 'block';

            options.forEach(opt => {
                const isDebt = opt.value === 'debt';
                const isChecked = selectedStatuses.includes(opt.value)
                    || selectedStatusLegacy === opt.value
                    || (isDebt && selectedStatusLegacy === 'debt');

                const colorMap = {
                    orange: 'bg-orange-50 dark:bg-orange-900/20 border-orange-300 dark:border-orange-700 text-orange-700 dark:text-orange-300',
                    default: 'bg-gray-50 dark:bg-dark-bg border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300'
                };
                const colorClass = colorMap[opt.color] || colorMap.default;
                const checkedClass = isChecked ? '!bg-primary-600 !border-primary-600 !text-white' : '';

                const label = document.createElement('label');
                label.className = `inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-bold cursor-pointer transition-all select-none ${colorClass} ${checkedClass}`;
                label.innerHTML = `
                    <input type="checkbox" name="statuses[]" value="${opt.value}" class="hidden" ${isChecked ? 'checked' : ''}>
                    ${opt.text}
                `;

                label.addEventListener('click', function() {
                    const cb = this.querySelector('input');
                    cb.checked = !cb.checked;
                    if (cb.checked) {
                        this.classList.add('!bg-primary-600', '!border-primary-600', '!text-white');
                    } else {
                        this.classList.remove('!bg-primary-600', '!border-primary-600', '!text-white');
                    }
                });

                statusContainer.appendChild(label);
            });
        }
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
            
            if (selected) {
                updateStatusOptions(selected);
            }
            
            if (statType.value === 'marketers' && ['sales', 'payments', 'sales_returns'].includes(operation.value)) {
                marketerStoreField.style.display = 'block';
            } else {
                marketerStoreField.style.display = 'none';
            }
        }
        
        // Listen to operation changes
        operation.addEventListener('change', function() {
            updateStatusOptions(this.value);
            
            if (this.value === 'summary') {
                statusField.style.display = 'none';
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
        
        // Initialize status checkboxes on load
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


<script>
let _currentInvoiceUrl = null;
let _currentInvoiceOperation = null;

function openInvoiceModal(url, number, operation) {
    _currentInvoiceUrl = url;
    _currentInvoiceOperation = operation;
    const modal = document.getElementById('invoiceModal');
    const loading = document.getElementById('invoiceModalLoading');
    const content = document.getElementById('invoiceModalContent');
    modal.style.display = 'flex';
    loading.style.display = 'flex';
    content.style.display = 'none';
    content.innerHTML = '';
    document.getElementById('modalInvoiceTitle').textContent = number;
    document.getElementById('downloadPdfBtn').href = url.replace('/invoice-data', '/pdf');
    fetch(url)
        .then(r => r.json())
        .then(data => {
            content._data = data;
            content.innerHTML = buildInvoiceHtml(data, operation);
            loading.style.display = 'none';
            content.style.display = 'block';
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

function closeInvoiceModal() {
    document.getElementById('invoiceModal').style.display = 'none';
}

function buildInvoiceHtml(d, operation) {
    const dk = document.documentElement.classList.contains('dark');
    const T = {
        text:    dk ? '#e2e8f0' : '#111111',
        sub:     dk ? '#94a3b8' : '#374151',
        muted:   dk ? '#64748b' : '#9CA3AF',
        border:  dk ? '#2a354c' : '#E5E7EB',
        rowA:    dk ? 'rgba(255,255,255,0.03)' : 'rgba(249,250,251,0.7)',
        rowB:    dk ? 'transparent' : 'transparent',
    };
    const fmt = v => { const n = parseFloat(String(v).replace(/,/g,'')); return isNaN(n) ? v : n.toLocaleString('en',{minimumFractionDigits:2,maximumFractionDigits:2}); };

    const statusMap = {
        pending:    dk ? {label:'معلق',      bg:'#78350f', color:'#fde68a'} : {label:'معلق',      bg:'#FEF3C7', color:'#92400E'},
        approved:   dk ? {label:'موثق',      bg:'#064e3b', color:'#6ee7b7'} : {label:'موثق',      bg:'#D1FAE5', color:'#065F46'},
        documented: dk ? {label:'موثق',      bg:'#064e3b', color:'#6ee7b7'} : {label:'موثق',      bg:'#D1FAE5', color:'#065F46'},
        cancelled:  dk ? {label:'ملغي',      bg:'#1f2937', color:'#9ca3af'} : {label:'ملغي',      bg:'#F3F4F6', color:'#374151'},
        rejected:   dk ? {label:'مرفوض',    bg:'#7f1d1d', color:'#fca5a5'} : {label:'مرفوض',    bg:'#FEE2E2', color:'#991B1B'},
        debt:       dk ? {label:'دين',       bg:'#431407', color:'#fb923c'} : {label:'دين',       bg:'#FFF7ED', color:'#9A3412'},
    };
    const st = statusMap[d.status] || statusMap.cancelled;

    const logoHtml = d.logo_base64
        ? `<img src="data:image/png;base64,${d.logo_base64}" style="max-height:70px;max-width:130px;display:block">`
        : '';

    // header
    function makeHeader(badgeBg, badgeColor, badgeBorder, badgeText, title, subtitle) {
        return `
        <div style="display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:20px;color:${T.text}">
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

    // info box — border only, no background
    function makeInfo(fields) {
        const cells = fields.map(([label, val]) => `
            <div>
                <div style="font-size:11px;color:${T.muted};margin-bottom:3px">${label}</div>
                <div style="font-weight:bold;font-size:14px;color:${T.text}">${val}</div>
            </div>`).join('');
        return `<div style="border:1px solid ${T.border};border-radius:14px;padding:16px;margin-bottom:16px;color:${T.text}">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">${cells}</div>
        </div>`;
    }

    // table
    function makeTable(heads, rows) {
        const ths = heads.map(h => `<th style="padding:10px 16px;text-align:${h.align||'right'};font-size:11px;font-weight:bold;color:${T.muted};border-bottom:1px solid ${T.border}">${h.label}</th>`).join('');
        const trs = rows.map((item, i) => {
            const bg = i%2===0 ? T.rowA : T.rowB;
            return `<tr style="background:${bg}">
                <td style="padding:13px 16px;border-bottom:1px solid ${T.border};font-weight:bold;font-size:13px;color:${T.text}">${item.name}</td>
                <td style="padding:13px 16px;border-bottom:1px solid ${T.border};text-align:center">
                    <span style="border:1px solid ${T.border};border-radius:10px;padding:3px 12px;font-weight:900;font-size:13px;color:${T.text}">${item.quantity}</span>
                </td>
                ${item.price !== undefined ? `<td style="padding:13px 16px;border-bottom:1px solid ${T.border};text-align:center;font-size:13px;color:${T.sub}">${item.price} د</td>` : ''}
                ${item.total !== undefined ? `<td style="padding:13px 16px;border-bottom:1px solid ${T.border};text-align:center;font-weight:900;font-size:13px;color:${T.text}">${item.total} د</td>` : ''}
            </tr>`;
        }).join('');
        return `<div style="border:1px solid ${T.border};border-radius:14px;overflow:hidden;margin-bottom:16px;color:${T.text}">
            <table style="width:100%;border-collapse:collapse;color:${T.text}">
                <thead><tr>${ths}</tr></thead>
                <tbody>${trs}</tbody>
            </table>
        </div>`;
    }

    // total box — border only, no background
    function makeTotalBox(borderClr, divClr, label, value, valColor) {
        return `<div style="display:flex;justify-content:flex-end">
            <div style="border:2px solid ${borderClr};border-radius:18px;padding:20px 24px;min-width:280px;color:${T.text}">
                <div style="border-top:2px solid ${divClr};padding-top:12px;display:flex;justify-content:space-between;align-items:baseline">
                    <span style="font-size:15px;font-weight:bold;color:${T.text}">${label}</span>
                    <span style="font-size:28px;font-weight:900;color:${valColor}">${fmt(value)} <span style="font-size:14px;font-weight:bold;color:${T.muted}">د</span></span>
                </div>
            </div>
        </div>`;
    }

    const wrap = c => `<div dir="rtl" style="font-family:Cairo,Arial,sans-serif;color:${T.text}">${c}</div>`;

    // ===== SALES =====
    if (operation === 'sales') {
        const heads = [{label:'المنتج'},{label:'الكمية',align:'center'},{label:'السعر',align:'center'},{label:'الإجمالي',align:'center'}];
        const discounts = [
            parseFloat(String(d.product_discount).replace(/,/g,'')) > 0 ? `<div style="display:flex;justify-content:space-between;font-size:14px;padding:4px 0"><span style="color:#059669;font-weight:600">خصم المنتجات (هدايا):</span><span style="color:#059669;font-weight:900">- ${fmt(d.product_discount)} د</span></div>` : '',
            parseFloat(String(d.invoice_discount).replace(/,/g,'')) > 0 ? `<div style="display:flex;justify-content:space-between;font-size:14px;padding:4px 0"><span style="color:${dk?'#60a5fa':'#2563EB'};font-weight:600">خصم الفاتورة:</span><span style="color:${dk?'#60a5fa':'#2563EB'};font-weight:900">- ${fmt(d.invoice_discount)} د</span></div>` : '',
        ].join('');
        const summary = `<div style="display:flex;justify-content:flex-end">
            <div style="border:2px solid ${dk?'#4338ca':'#C7D2FE'};border-radius:18px;padding:20px 24px;min-width:280px;color:${T.text}">
                <div style="display:flex;justify-content:space-between;font-size:14px;padding:4px 0"><span style="color:${T.sub};font-weight:600">عدد البضاعة:</span><span style="font-weight:900;color:${T.text}">${d.total_items}</span></div>
                <div style="display:flex;justify-content:space-between;font-size:14px;padding:4px 0"><span style="color:${T.sub};font-weight:600">المجموع الفرعي:</span><span style="font-weight:900;color:${T.text}">${fmt(d.subtotal)} د</span></div>
                ${discounts}
                <div style="border-top:2px solid ${dk?'#818cf8':'#818CF8'};margin-top:10px;padding-top:12px;display:flex;justify-content:space-between;align-items:baseline">
                    <span style="font-size:15px;font-weight:bold;color:${T.text}">الإجمالي النهائي:</span>
                    <span style="font-size:28px;font-weight:900;color:${dk?'#93c5fd':'#1D4ED8'}">${fmt(d.total)} <span style="font-size:14px;font-weight:bold;color:${T.muted}">د</span></span>
                </div>
            </div>
        </div>`;
        return wrap(
            makeHeader(dk?'#1e1b4b':'#EEF2FF', dk?'#a5b4fc':'#4F46E5', dk?'#4338ca':'#C7D2FE', 'فاتورة بيع', `فاتورة #${d.invoice_number}`, null) +
            makeInfo([['اسم المتجر',d.store],['رقم الهاتف',d.store_phone],['المسوق',d.marketer]]) +
            makeTable(heads, d.items) +
            summary
        );
    }

    // ===== PAYMENTS =====
    if (operation === 'payments') {
        return wrap(
            makeHeader(dk?'#052e16':'#ECFDF5', dk?'#4ade80':'#065F46', dk?'#16a34a':'#A7F3D0', 'إيصال قبض', `إيصال #${d.payment_number}`, null) +
            makeInfo([['اسم المتجر',d.store],['رقم الهاتف',d.store_phone],['المسوق',d.marketer],['طريقة الدفع',d.payment_method]]) +
            makeTotalBox(dk?'#16a34a':'#6EE7B7', dk?'#22c55e':'#34D399', 'المبلغ المسدد:', d.amount, dk?'#4ade80':'#065F46')
        );
    }

    // ===== SALES RETURNS =====
    if (operation === 'sales_returns') {
        const heads = [{label:'المنتج'},{label:'الكمية',align:'center'},{label:'السعر',align:'center'},{label:'الإجمالي',align:'center'}];
        return wrap(
            makeHeader(dk?'#431407':'#FFF7ED', dk?'#fb923c':'#9A3412', dk?'#c2410c':'#FED7AA', 'إرجاع متجر', `إرجاع #${d.return_number}`, `فاتورة أصلية: ${d.invoice_number}`) +
            makeInfo([['اسم المتجر',d.store],['رقم الهاتف',d.store_phone],['المسوق',d.marketer]]) +
            makeTable(heads, d.items) +
            makeTotalBox(dk?'#c2410c':'#FDBA74', dk?'#ea580c':'#FB923C', 'الإجمالي:', d.total, dk?'#fb923c':'#9A3412')
        );
    }

    // ===== REQUESTS =====
    if (operation === 'requests') {
        const heads = [{label:'المنتج'},{label:'الكمية',align:'center'}];
        return wrap(
            makeHeader(dk?'#1e1b4b':'#EEF2FF', dk?'#a5b4fc':'#4338CA', dk?'#4338ca':'#C7D2FE', 'طلب بضاعة', `طلب #${d.invoice_number}`, null) +
            makeInfo([['المسوق',d.marketer]]) +
            makeTable(heads, d.items)
        );
    }

    // ===== RETURNS =====
    if (operation === 'returns') {
        const heads = [{label:'المنتج'},{label:'الكمية',align:'center'}];
        return wrap(
            makeHeader(dk?'#431407':'#FFF7ED', dk?'#fb923c':'#9A3412', dk?'#c2410c':'#FED7AA', 'إرجاع بضاعة', `إرجاع #${d.invoice_number}`, null) +
            makeInfo([['المسوق',d.marketer]]) +
            makeTable(heads, d.items)
        );
    }

    // ===== WITHDRAWALS =====
    if (operation === 'withdrawals') {
        const fields = [['المسوق', d.marketer]];
        if (d.notes) fields.push(['ملاحظات', d.notes]);
        return wrap(
            makeHeader(dk?'#052e16':'#F0FDF4', dk?'#4ade80':'#166534', dk?'#16a34a':'#BBF7D0', 'سحب أرباح', `سحب #WD-${d.withdrawal_id}`, null) +
            makeInfo(fields) +
            makeTotalBox(dk?'#16a34a':'#86EFAC', dk?'#22c55e':'#4ADE80', 'المبلغ المطلوب:', d.amount, dk?'#4ade80':'#166534')
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

    const countUrl = new URL('{{ route("admin.statistics.bulk-invoices-count") }}', window.location.origin);
    new URLSearchParams(window.location.search).forEach((v, k) => countUrl.searchParams.set(k, v));

    _bulkBaseUrl = new URL('{{ route("admin.statistics.bulk-invoices-pdf") }}', window.location.origin);
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

function closeBulkModal() {
    document.getElementById('bulkModal').style.display = 'none';
}

function toggleChunks() {
    _chunksOpen = !_chunksOpen;
    const panel   = document.getElementById('chunksPanel');
    const chevron = document.getElementById('chunksChevron');
    panel.style.display     = _chunksOpen ? 'block' : 'none';
    chevron.style.transform = _chunksOpen ? 'rotate(180deg)' : '';
    if (_chunksOpen) renderChunks();
}

function clampChunkSize(el) {
    let v = el.value.replace(/[^0-9]/g, '');
    if (v === '') { el.value = ''; return; }
    const n = parseInt(v);
    const first = parseInt(v[0]);
    if (first < 5) { el.value = '50'; return; }
    if (first > 7) { el.value = '70'; return; }
    if (v.length >= 2) {
        if (n < 50) { el.value = '50'; return; }
        if (n > 70) { el.value = '70'; return; }
        el.value = String(n);
    } else {
        el.value = v;
    }
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
        a.href   = url.toString();
        a.target = '_blank';
        a.className = 'flex items-center gap-2 px-4 py-2.5 bg-white dark:bg-dark-bg border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl hover:bg-violet-50 dark:hover:bg-violet-500/10 hover:border-violet-300 transition-colors';
        a.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-violet-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg><span class="text-sm font-bold text-gray-700 dark:text-gray-300">فواتير ${offset + 1} - ${end}</span><span class="text-xs text-gray-400 mr-auto">(${end - offset} فاتورة)</span>`;
        list.appendChild(a);
    }
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') { closeInvoiceModal(); closeBulkModal(); } });
</script>
@endpush
@endsection
