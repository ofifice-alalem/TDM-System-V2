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

        {{-- Tabs --}}
        <div class="flex gap-2">
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'financial']) }}"
               class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2
                   {{ $tab === 'financial' ? 'bg-primary-600 text-white shadow-md' : 'bg-white dark:bg-dark-card text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-dark-border hover:bg-gray-50 dark:hover:bg-dark-bg' }}">
                <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                الملخص المالي
            </a>
            <a href="{{ request()->fullUrlWithQuery(['tab' => 'clients']) }}"
               class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2
                   {{ $tab === 'clients' ? 'bg-teal-600 text-white shadow-md' : 'bg-white dark:bg-dark-card text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-dark-border hover:bg-gray-50 dark:hover:bg-dark-bg' }}">
                <i data-lucide="package" class="w-4 h-4"></i>
                الملخص الشامل لكل زبون
            </a>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.combined-summary.index') }}" class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-5 shadow-sm">
        <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">من تاريخ</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}"
                        class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm [color-scheme:light] dark:[color-scheme:dark]">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">إلى تاريخ</label>
                    <input type="date" name="to_date" value="{{ $toDate }}"
                        class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm [color-scheme:light] dark:[color-scheme:dark]">
                </div>

                {{-- فلتر الموظف --}}
                <div x-data="{
                    open: false,
                    search: '{{ optional($staff->firstWhere('id', $staffId))->full_name ?? '' }}',
                    value: '{{ $staffId ?? '' }}',
                    items: {{ Js::from($staff->map(fn($u) => ['id' => $u->id, 'name' => $u->full_name, 'role' => $u->role_id == 3 ? 'مسوق' : 'مبيعات'])) }},
                    get filtered() { return this.search.length < 1 ? this.items : this.items.filter(i => i.name.includes(this.search)); }
                }" class="relative" id="staff-filter-wrap">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">الموظف (اختياري)</label>
                    <input type="text" x-model="search" @focus="open=true" @click.outside="open=false" autocomplete="off"
                        @input="if(!search){ value=''; document.getElementById('staff-id-input').value=''; }"
                        placeholder="الكل..."
                        class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <input type="hidden" name="staff_id" id="staff-id-input" :value="value">
                    <div x-show="open" class="absolute z-50 mt-1 w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-xl max-h-52 overflow-y-auto">
                        <div @click="search=''; value=''; open=false; document.getElementById('staff-id-input').value='';" class="px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-dark-bg cursor-pointer text-sm text-gray-500">الكل</div>
                        <template x-for="item in filtered" :key="item.id">
                            <div @click="search=item.name; value=item.id; open=false; document.getElementById('staff-id-input').value=item.id;"
                                class="px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-dark-bg cursor-pointer border-t border-gray-100 dark:border-dark-border">
                                <div class="text-sm font-bold text-gray-900 dark:text-white" x-text="item.name"></div>
                                <div class="text-xs text-gray-400" x-text="item.role"></div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- تضمين الديون السابقة --}}
                <div class="flex items-end pb-1">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <div class="relative">
                            <input type="hidden" name="include_old_debt" value="0">
                            <input type="checkbox" name="include_old_debt" value="1" id="include-old-debt-cb"
                                {{ $includeOldDebt ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 dark:bg-gray-700 rounded-full peer peer-checked:bg-primary-600 transition-colors"></div>
                            <div class="absolute top-0.5 right-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-[-20px]"></div>
                        </div>
                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300">تضمين الديون السابقة</span>
                    </label>
                </div>

                {{-- فلتر النوع --}}
                <div x-data="{
                    type: '{{ $storeId ? 'store' : ($customerId ? 'customer' : (request('entity_type') === 'store' ? 'store' : (request('entity_type') === 'customer' ? 'customer' : 'all'))) }}',
                    storeSearch: '{{ optional($stores->firstWhere('id', $storeId))->name ?? request('store_name', '') }}',
                    storeValue: '{{ $storeId ?? '' }}',
                    storeItems: {{ Js::from($stores->map(fn($s) => ['id' => $s->id, 'name' => $s->name])) }},
                    storeOpen: false,
                    customerSearch: '{{ optional($customers->firstWhere('id', $customerId))->name ?? request('customer_name', '') }}',
                    customerValue: '{{ $customerId ?? '' }}',
                    customerItems: {{ Js::from($customers->map(fn($c) => ['id' => $c->id, 'name' => $c->name])) }},
                    customerOpen: false,
                    get storeFiltered() { return this.storeSearch.length < 1 ? this.storeItems : this.storeItems.filter(i => i.name.includes(this.storeSearch)); },
                    get customerFiltered() { return this.customerSearch.length < 1 ? this.customerItems : this.customerItems.filter(i => i.name.includes(this.customerSearch)); }
                }" class="space-y-3">

                    {{-- Radio buttons --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">عرض</label>
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" x-model="type" value="all" class="text-primary-600 focus:ring-primary-500">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">الكل</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" x-model="type" value="store" class="text-primary-600 focus:ring-primary-500">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">المتاجر</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" x-model="type" value="customer" class="text-primary-600 focus:ring-primary-500">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">العملاء</span>
                            </label>
                        </div>
                    </div>

                    {{-- دروبداون المتجر --}}
                    <div x-show="type === 'store'" class="relative">
                        <input type="text" x-model="storeSearch" @focus="storeOpen=true" @click.outside="storeOpen=false" autocomplete="off"
                            @input="storeValue=''"
                            placeholder="ابحث عن متجر..."
                            class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <input type="hidden" name="store_id" :value="type === 'store' ? storeValue : ''">
                        <input type="hidden" name="store_name" :value="type === 'store' && !storeValue ? storeSearch.trim() : ''">
                        <div x-show="storeOpen" class="absolute z-50 mt-1 w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-xl max-h-52 overflow-y-auto">
                            <div @click="storeSearch=''; storeValue=''; storeOpen=false" class="px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-dark-bg cursor-pointer text-sm text-gray-500">الكل</div>
                            <template x-for="item in storeFiltered" :key="item.id">
                                <div @click="storeSearch=item.name; storeValue=item.id; storeOpen=false"
                                    class="px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-dark-bg cursor-pointer border-t border-gray-100 dark:border-dark-border text-sm font-bold text-gray-900 dark:text-white" x-text="item.name">
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- دروبداون العميل --}}
                    <div x-show="type === 'customer'" class="relative">
                        <input type="text" x-model="customerSearch" @focus="customerOpen=true" @click.outside="customerOpen=false" autocomplete="off"
                            @input="customerValue=''"
                            placeholder="ابحث عن عميل..."
                            class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <input type="hidden" name="customer_id" :value="type === 'customer' ? customerValue : ''">
                        <input type="hidden" name="customer_name" :value="type === 'customer' && !customerValue ? customerSearch.trim() : ''">
                        <div x-show="customerOpen" class="absolute z-50 mt-1 w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-xl max-h-52 overflow-y-auto">
                            <div @click="customerSearch=''; customerValue=''; customerOpen=false" class="px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-dark-bg cursor-pointer text-sm text-gray-500">الكل</div>
                            <template x-for="item in customerFiltered" :key="item.id">
                                <div @click="customerSearch=item.name; customerValue=item.id; customerOpen=false"
                                    class="px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-dark-bg cursor-pointer border-t border-gray-100 dark:border-dark-border text-sm font-bold text-gray-900 dark:text-white" x-text="item.name">
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- hidden inputs للتصفية حسب النوع --}}
                    <input type="hidden" name="entity_type" :value="type">
                </div>
            </div>

            {{-- فلتر المنتج (تاب الزبائن فقط) --}}
            @if($tab === 'clients')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4 border-t border-gray-100 dark:border-dark-border pt-4"
                 x-data="{
                    search: '{{ $products->firstWhere('id', $productId)?->name ?? '' }}',
                    selectedId: '{{ $productId ?? '' }}',
                    open: false,
                    items: {{ Js::from($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name])) }},
                    get filtered() { return this.search.length < 1 ? this.items : this.items.filter(i => i.name.includes(this.search)); }
                 }" @click.outside="open=false">
                <div class="relative">
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">المنتج (اختياري)</label>
                    <input type="text" x-model="search" @focus="open=true" autocomplete="off"
                        @input="selectedId=''"
                        placeholder="كل المنتجات..."
                        class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <input type="hidden" name="product_id" :value="selectedId">
                    <div x-show="open" class="absolute z-50 mt-1 w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-xl max-h-52 overflow-y-auto">
                        <div @click="search=''; selectedId=''; open=false" class="px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-dark-bg cursor-pointer text-sm text-gray-500">كل المنتجات</div>
                        <template x-for="item in filtered" :key="item.id">
                            <div @click="search=item.name; selectedId=item.id; open=false"
                                class="px-4 py-2.5 hover:bg-gray-50 dark:hover:bg-dark-bg cursor-pointer border-t border-gray-100 dark:border-dark-border text-sm font-bold text-gray-900 dark:text-white" x-text="item.name">
                            </div>
                        </template>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">ترتيب حسب</label>
                    <select name="sort_by" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="amount" {{ ($sortBy ?? 'amount') === 'amount' ? 'selected' : '' }}>المبلغ</option>
                        <option value="qty" {{ ($sortBy ?? '') === 'qty' ? 'selected' : '' }}>الكمية</option>
                    </select>
                </div>
            </div>
            @endif

            <div class="flex flex-wrap gap-3">
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
                @if($storeId || $customerId || $staffId || request('entity_type', 'all') !== 'all' || $productId)
                <a href="{{ route('admin.combined-summary.index', ['tab' => $tab, 'from_date' => $fromDate, 'to_date' => $toDate]) }}"
                    class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i> إلغاء الفلاتر
                </a>
                @endif
            </div>
        </form>

        {{-- ===== تاب الملخص الشامل لكل زبون ===== --}}
        @if($tab === 'clients')

        @if($clientsData && count($clientsData) > 0)
        @php
            $grandQty    = array_sum(array_column($clientsData, 'total_qty'));
            $grandAmount = array_sum(array_column($clientsData, 'total_amount'));
        @endphp
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white dark:bg-dark-card rounded-2xl p-4 border border-gray-200 dark:border-dark-border shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 bg-teal-50 dark:bg-teal-500/10 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="package" class="w-5 h-5 text-teal-600 dark:text-teal-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold">إجمالي الكمية</p>
                    <p class="text-xl font-black text-gray-900 dark:text-white">{{ number_format($grandQty) }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-2xl p-4 border border-gray-200 dark:border-dark-border shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 bg-teal-50 dark:bg-teal-500/10 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="banknote" class="w-5 h-5 text-teal-600 dark:text-teal-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold">إجمالي المبلغ</p>
                    <p class="text-xl font-black text-teal-600 dark:text-teal-400">{{ number_format($grandAmount, 0) }} <span class="text-xs font-normal text-gray-500">د</span></p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 dark:border-dark-border">
                <h3 class="font-black text-gray-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="users" class="w-5 h-5 text-teal-500"></i>
                    الزبائن
                    <span class="text-xs font-bold text-gray-400 dark:text-gray-500 mr-1">— مرتب حسب {{ ($sortBy ?? 'amount') === 'qty' ? 'الكمية' : 'المبلغ' }}</span>
                </h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-dark-border">
                @foreach($clientsData as $index => $entry)
                @php
                    $isStore = $entry['type'] === 'متجر';
                    $pct = $grandAmount > 0 ? round(($entry['total_amount'] / $grandAmount) * 100, 1) : 0;
                @endphp
                <div x-data="{ open: false }">
                    <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-5 py-4 hover:bg-gray-50 dark:hover:bg-dark-bg/60 transition-colors text-right">
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-black shrink-0
                            {{ $index === 0 ? 'bg-amber-400 text-white' : ($index === 1 ? 'bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200' : ($index === 2 ? 'bg-orange-300 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400')) }}">
                            {{ $index + 1 }}
                        </span>
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $isStore ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-purple-100 dark:bg-purple-900/30' }}">
                            <i data-lucide="{{ $isStore ? 'store' : 'user' }}" class="w-4 h-4 {{ $isStore ? 'text-blue-600 dark:text-blue-400' : 'text-purple-600 dark:text-purple-400' }}"></i>
                        </div>
                        <div class="flex-1 min-w-0 text-right">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-black text-gray-900 dark:text-white text-sm">{{ $entry['name'] }}</span>
                                <span class="px-2 py-0.5 rounded-lg text-xs font-black {{ $isStore ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400' }}">
                                    {{ $entry['type'] }}
                                </span>
                            </div>
                            <div class="mt-1.5 h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden w-full max-w-xs">
                                <div class="h-full rounded-full {{ $isStore ? 'bg-blue-500' : 'bg-purple-500' }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 shrink-0">
                            <div class="text-center hidden sm:block">
                                <p class="text-xs text-gray-400 dark:text-gray-500">كمية</p>
                                <p class="text-sm font-black text-gray-700 dark:text-gray-300">{{ number_format($entry['total_qty']) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-400 dark:text-gray-500">مبلغ</p>
                                <p class="text-sm font-black text-teal-600 dark:text-teal-400">{{ number_format($entry['total_amount'], 0) }}</p>
                            </div>
                            <div class="text-center hidden sm:block">
                                <p class="text-xs text-gray-400 dark:text-gray-500">نسبة</p>
                                <p class="text-sm font-black text-gray-500 dark:text-gray-400">{{ $pct }}%</p>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''"></i>
                        </div>
                    </button>
                    <div x-show="open" x-collapse class="border-t border-gray-100 dark:border-dark-border bg-gray-50/50 dark:bg-dark-bg/30">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-gray-100/80 dark:bg-gray-800/50">
                                        <th class="px-6 py-2.5 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المنتج</th>
                                        <th class="px-5 py-2.5 text-center text-xs font-bold text-gray-600 dark:text-gray-400">السعر</th>
                                        <th class="px-5 py-2.5 text-center text-xs font-bold text-gray-600 dark:text-gray-400">مرات</th>
                                        <th class="px-5 py-2.5 text-center text-xs font-bold text-gray-600 dark:text-gray-400">كمية</th>
                                        <th class="px-5 py-2.5 text-center text-xs font-bold text-gray-600 dark:text-gray-400">مبلغ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($entry['products'] as $product)
                                    <tr class="bg-gray-100/80 dark:bg-gray-800/60 border-t-2 border-gray-300 dark:border-gray-600">
                                        <td class="px-6 py-2.5">
                                            <span class="font-black text-gray-800 dark:text-gray-200 text-xs">{{ $product['product_name'] }}</span>
                                        </td>
                                        <td class="px-5 py-2.5 text-center">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">متوسط: <span class="font-black text-gray-700 dark:text-gray-300">{{ number_format($product['avg_price'], 2) }}</span></span>
                                        </td>
                                        <td class="px-5 py-2.5 text-center">
                                            <span class="text-xs font-black text-gray-600 dark:text-gray-400">{{ number_format(array_sum(array_column($product['prices'], 'times'))) }}</span>
                                        </td>
                                        <td class="px-5 py-2.5 text-center">
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 rounded text-xs font-black">{{ number_format($product['total_qty']) }}</span>
                                        </td>
                                        <td class="px-5 py-2.5 text-center">
                                            <span class="text-xs font-black text-teal-600 dark:text-teal-400">{{ number_format($product['total_amount'], 0) }}</span>
                                        </td>
                                    </tr>
                                    @foreach($product['prices'] as $priceIndex => $price)
                                    <tr class="bg-white dark:bg-dark-card hover:bg-amber-50/30 dark:hover:bg-amber-900/5 transition-colors">
                                        <td class="px-6 py-2 pr-12 text-xs text-gray-400 dark:text-gray-500">سعر {{ $priceIndex + 1 }}</td>
                                        <td class="px-5 py-2 text-center">
                                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded text-xs font-black">
                                                {{ number_format($price['price'], 2) }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-2 text-center text-xs text-gray-500 dark:text-gray-400">{{ $price['times'] }}</td>
                                        <td class="px-5 py-2 text-center text-xs text-gray-600 dark:text-gray-300 font-bold">{{ number_format($price['total_qty']) }}</td>
                                        <td class="px-5 py-2 text-center text-xs text-gray-600 dark:text-gray-300 font-bold">{{ number_format($price['total_amount'], 0) }}</td>
                                    </tr>
                                    @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-16 text-center shadow-sm">
            <i data-lucide="inbox" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
            <p class="text-gray-500 dark:text-gray-400 font-bold">لا توجد بيانات في هذه الفترة</p>
        </div>
        @endif

        @else
        {{-- ===== تاب الملخص المالي (الأصلي) ===== --}}
        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-{{ $includeOldDebt ? '5' : '4' }} gap-4">
            @if($includeOldDebt)
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-4 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 bg-amber-50 dark:bg-amber-500/10 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="history" class="w-5 h-5 text-amber-500 dark:text-amber-400"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400">ديون سابقة</p>
                    <p class="text-lg font-black text-amber-600 dark:text-amber-400 truncate">{{ number_format($grandOldDebt, 2) }}</p>
                </div>
            </div>
            @endif
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
                    <div class="grid grid-cols-{{ $includeOldDebt ? '4' : '3' }} divide-x divide-x-reverse divide-gray-100 dark:divide-dark-border border border-gray-100 dark:border-dark-border rounded-2xl overflow-hidden">
                        @if($includeOldDebt)
                        <div class="p-3 text-center">
                            <div class="w-7 h-7 bg-amber-50 dark:bg-amber-500/10 rounded-lg flex items-center justify-center text-amber-500 mx-auto mb-1"><i data-lucide="history" class="w-3.5 h-3.5"></i></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">ديون سابقة</div>
                            <div class="text-sm font-black text-amber-600 dark:text-amber-400">{{ number_format($storeSummary['old_debt'], 2) }}</div>
                        </div>
                        @endif
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
                    <div class="grid grid-cols-{{ $includeOldDebt ? '4' : '3' }} divide-x divide-x-reverse divide-gray-100 dark:divide-dark-border border border-gray-100 dark:border-dark-border rounded-2xl overflow-hidden">
                        @if($includeOldDebt)
                        <div class="p-3 text-center">
                            <div class="w-7 h-7 bg-amber-50 dark:bg-amber-500/10 rounded-lg flex items-center justify-center text-amber-500 mx-auto mb-1"><i data-lucide="history" class="w-3.5 h-3.5"></i></div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">ديون سابقة</div>
                            <div class="text-sm font-black text-amber-600 dark:text-amber-400">{{ number_format($customerSummary['old_debt'], 2) }}</div>
                        </div>
                        @endif
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
                                @if($includeOldDebt)
                                <th class="px-4 py-3 text-left font-bold text-amber-600 dark:text-amber-400">ديون سابقة</th>
                                @endif
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
                                    @if($includeOldDebt)
                                    <td class="px-4 py-3 text-left font-mono {{ $row->old_debt > 0 ? 'text-amber-600 dark:text-amber-400 font-bold' : 'text-gray-400 dark:text-gray-600' }}">
                                        {{ $row->old_debt > 0 ? number_format($row->old_debt, 2) : '—' }}
                                    </td>
                                    @endif
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
                                <td colspan="{{ $includeOldDebt ? '3' : '2' }}" class="px-4 py-3 font-black text-gray-900 dark:text-white">الإجمالي</td>
                                @if($includeOldDebt)
                                <td class="px-4 py-3 text-left font-black font-mono {{ $grandOldDebt > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-400' }}">{{ $grandOldDebt > 0 ? number_format($grandOldDebt, 2) : '—' }}</td>
                                @endif
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

        @endif {{-- end tab financial --}}

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());
</script>
@endpush
@endsection
