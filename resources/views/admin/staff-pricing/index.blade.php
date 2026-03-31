@extends('layouts.app')

@section('title', 'معدل الموظفين')

@section('content')
<div class="min-h-screen py-6">
<div class="max-w-7xl mx-auto px-4">

    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">معدل الموظفين</h1>
        <p class="text-gray-500 dark:text-dark-muted mt-1 text-sm">تحليل مبيعات الموظفين والأسعار المستخدمة</p>
    </div>

    {{-- Mode Tabs --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ request()->fullUrlWithQuery(['mode' => 'single', 'user_id' => null]) }}"
           class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2
               {{ $mode === 'single' ? 'bg-primary-600 text-white shadow-md' : 'bg-white dark:bg-dark-card text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-dark-border hover:bg-gray-50 dark:hover:bg-dark-bg' }}">
            <i data-lucide="user" class="w-4 h-4"></i>
            موظف واحد
        </a>
        <a href="{{ request()->fullUrlWithQuery(['mode' => 'compare', 'user_id' => null]) }}"
           class="px-5 py-2.5 rounded-xl font-bold text-sm transition-all flex items-center gap-2
               {{ $mode === 'compare' ? 'bg-purple-600 text-white shadow-md' : 'bg-white dark:bg-dark-card text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-dark-border hover:bg-gray-50 dark:hover:bg-dark-bg' }}">
            <i data-lucide="users" class="w-4 h-4"></i>
            مقارنة الكل
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-dark-card rounded-2xl p-4 sm:p-6 border border-gray-200 dark:border-dark-border shadow-lg mb-6">
        <form method="GET" class="space-y-4">
            <input type="hidden" name="mode" value="{{ $mode }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- User selector (single mode only) --}}
                @if($mode === 'single')
                <div class="lg:col-span-2"
                    x-data="{
                        search: '{{ request('user_id') === 'all' ? 'الكل' : collect($marketers)->merge($salesUsers)->firstWhere('id', request('user_id'))?->full_name ?? '' }}',
                        selectedId: '{{ request('user_id') ?? '' }}',
                        open: false,
                        users: {{ Js::from(collect($marketers)->map(fn($u) => ['id' => $u->id, 'name' => $u->full_name, 'role' => 'مسوق'])->merge(collect($salesUsers)->map(fn($u) => ['id' => $u->id, 'name' => $u->full_name, 'role' => 'مبيعات']))->values()) }},
                        filtered: [],
                        init() { this.filtered = this.users; },
                        filter() {
                            this.selectedId = '';
                            const q = this.search.toLowerCase();
                            this.filtered = q ? this.users.filter(u => u.name.toLowerCase().includes(q)) : this.users;
                            this.open = true;
                        },
                        select(id, name) { this.selectedId = id; this.search = name; this.open = false; }
                    }"
                    @click.outside="open = false"
                >
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">اسم الموظف</label>
                    <div class="relative">
                        <input type="text" x-model="search" @focus="open = true" @input="filter()"
                            placeholder="ابحث عن موظف..."
                            autocomplete="off"
                            class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 pr-10">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute top-1/2 -translate-y-1/2 right-3 pointer-events-none"></i>
                        <input type="hidden" name="user_id" x-model="selectedId">
                        <div x-show="open" x-transition
                            class="absolute z-50 w-full mt-1 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-xl overflow-hidden"
                            style="max-height: 220px; overflow-y: auto;">
                            <div @click="select('all', 'الكل')"
                                class="px-4 py-2.5 text-sm font-bold text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-500/10 cursor-pointer border-b border-gray-100 dark:border-dark-border">
                                الكل
                            </div>
                            <template x-for="u in filtered" :key="u.id">
                                <div @click="select(u.id, u.name)"
                                    class="px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-dark-bg cursor-pointer flex items-center justify-between">
                                    <span x-text="u.name" class="font-bold"></span>
                                    <span x-text="u.role"
                                        :class="u.role === 'مسوق' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400'"
                                        class="text-xs px-2 py-0.5 rounded-lg font-bold"></span>
                                </div>
                            </template>
                            <div x-show="filtered.length === 0" class="px-4 py-3 text-sm text-gray-400 text-center">لا توجد نتائج</div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Product filter --}}
                <div x-data="{
                    search: '{{ $products->firstWhere('id', $productId)?->name ?? '' }}',
                    selectedId: '{{ $productId ?? '' }}',
                    open: false,
                    products: {{ Js::from($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name])) }},
                    filtered: [],
                    init() { this.filtered = this.products; },
                    filter() {
                        this.selectedId = '';
                        const q = this.search.toLowerCase();
                        this.filtered = q ? this.products.filter(p => p.name.toLowerCase().includes(q)) : this.products;
                        this.open = true;
                    },
                    select(id, name) { this.selectedId = id; this.search = name; this.open = false; },
                    clear() { this.selectedId = ''; this.search = ''; this.filtered = this.products; }
                }"
                @click.outside="open = false">
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">المنتج (اختياري)</label>
                    <div class="relative">
                        <input type="text" x-model="search" @focus="open = true" @input="filter()"
                            placeholder="كل المنتجات..."
                            autocomplete="off"
                            class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 pr-10">
                        <i data-lucide="package" class="w-4 h-4 text-gray-400 absolute top-1/2 -translate-y-1/2 right-3 pointer-events-none"></i>
                        <input type="hidden" name="product_id" x-model="selectedId">
                        <div x-show="open" x-transition
                            class="absolute z-50 w-full mt-1 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-xl overflow-hidden"
                            style="max-height: 220px; overflow-y: auto;">
                            <div @click="clear(); open = false"
                                class="px-4 py-2.5 text-sm font-bold text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-500/10 cursor-pointer border-b border-gray-100 dark:border-dark-border">
                                كل المنتجات
                            </div>
                            <template x-for="p in filtered" :key="p.id">
                                <div @click="select(p.id, p.name)"
                                    class="px-4 py-2.5 text-sm text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-dark-bg cursor-pointer"
                                    x-text="p.name"></div>
                            </template>
                            <div x-show="filtered.length === 0" class="px-4 py-3 text-sm text-gray-400 text-center">لا توجد نتائج</div>
                        </div>
                    </div>
                </div>

                {{-- From Date --}}
                <div>
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">من تاريخ</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}"
                        class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:[color-scheme:dark]">
                </div>

                {{-- To Date --}}
                <div>
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">إلى تاريخ</label>
                    <input type="date" name="to_date" value="{{ $toDate }}"
                        class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:[color-scheme:dark]">
                </div>

                {{-- Sort (compare mode) --}}
                @if($mode === 'compare')
                <div>
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">ترتيب حسب</label>
                    <select name="sort_by" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="amount" {{ $sortBy === 'amount' ? 'selected' : '' }}>المبلغ</option>
                        <option value="qty" {{ $sortBy === 'qty' ? 'selected' : '' }}>الكمية</option>
                    </select>
                </div>
                @endif
            </div>

            <div class="flex gap-2">
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold text-sm flex items-center gap-2 transition-all">
                    <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                    عرض
                </button>
                <button type="submit" name="export" value="1" class="px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold text-sm flex items-center gap-2 transition-all">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    تصدير Excel
                </button>
                <button type="submit" name="pdf" value="1" class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-sm flex items-center gap-2 transition-all">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    تصدير PDF
                </button>
                <a href="{{ route('admin.staff-pricing.index', ['mode' => $mode]) }}"
                   class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold text-sm flex items-center gap-2 transition-all">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    {{-- ===== SINGLE MODE ===== --}}
    @if($mode === 'single')
        @if($staffData)
        @php
            $user = $staffData['user'];
            $isAll = $user === null;
            $isMarketer = !$isAll && $user->role_id === 3;
        @endphp

        {{-- Staff Card --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl p-5 border border-gray-200 dark:border-dark-border shadow-lg mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 {{ $isAll ? 'bg-purple-100 dark:bg-purple-900/30' : ($isMarketer ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-green-100 dark:bg-green-900/30') }}">
                    <i data-lucide="{{ $isAll ? 'users' : 'user' }}" class="w-7 h-7 {{ $isAll ? 'text-purple-600 dark:text-purple-400' : ($isMarketer ? 'text-blue-600 dark:text-blue-400' : 'text-green-600 dark:text-green-400') }}"></i>
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-xl font-black text-gray-900 dark:text-white">{{ $isAll ? 'جميع الموظفين' : $user->full_name }}</h2>
                        @if(!$isAll)
                        <span class="px-2.5 py-1 rounded-lg text-xs font-black {{ $isMarketer ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' }}">
                            {{ $isMarketer ? 'مسوق' : 'موظف مبيعات' }}
                        </span>
                        @else
                        <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400">الكل</span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ $fromDate }} — {{ $toDate }}</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-3 text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold mb-1">إجمالي الكمية</p>
                    <p class="text-xl font-black text-gray-900 dark:text-white">{{ number_format($staffData['total_qty']) }}</p>
                </div>
                <div class="bg-primary-50 dark:bg-primary-900/20 rounded-xl p-3 text-center">
                    <p class="text-xs text-primary-600 dark:text-primary-400 font-bold mb-1">إجمالي المبلغ</p>
                    <p class="text-xl font-black text-primary-600 dark:text-primary-400">{{ number_format($staffData['total_amount'], 0) }} <span class="text-xs font-normal">د</span></p>
                </div>
            </div>
        </div>

        {{-- Products Table --}}
        @if(count($staffData['products']) > 0)
        <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-lg overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-gray-200 dark:border-dark-border">
                <h3 class="font-black text-gray-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="package" class="w-5 h-5 text-primary-500"></i>
                    تفصيل المنتجات والأسعار
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gradient-to-l from-gray-50 to-gray-100 dark:from-dark-bg dark:to-gray-800/50">
                        <tr>
                            <th class="px-5 py-3 text-right text-xs font-bold text-gray-700 dark:text-gray-300">المنتج</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-300">السعر</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-300">مرات البيع</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-300">الكمية</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-gray-700 dark:text-gray-300">المبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staffData['products'] as $product)
                            {{-- Product summary row --}}
                            <tr class="bg-gray-100/80 dark:bg-gray-800/60 border-t-2 border-gray-300 dark:border-gray-600">
                                <td class="px-5 py-3" colspan="1">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center shrink-0">
                                            <i data-lucide="box" class="w-3.5 h-3.5 text-primary-600 dark:text-primary-400"></i>
                                        </div>
                                        <span class="font-black text-gray-900 dark:text-white text-sm">{{ $product['product_name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">متوسط: <span class="font-black text-gray-700 dark:text-gray-300">{{ number_format($product['avg_price'], 2) }}</span></span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-black">
                                        {{ number_format(array_sum(array_column($product['prices'], 'times'))) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="inline-flex items-center justify-center px-2.5 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg text-xs font-black">
                                        {{ number_format($product['total_qty']) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="font-black text-primary-600 dark:text-primary-400 text-sm">{{ number_format($product['total_amount'], 0) }}</span>
                                </td>
                            </tr>
                            {{-- Price breakdown rows --}}
                            @foreach($product['prices'] as $priceIndex => $price)
                            <tr class="bg-white dark:bg-dark-card hover:bg-primary-50/20 dark:hover:bg-primary-900/5 transition-colors">
                                <td class="px-5 py-2.5 pr-12">
                                    <span class="text-xs text-gray-400 dark:text-gray-500">سعر {{ $priceIndex + 1 }}</span>
                                </td>
                                <td class="px-5 py-2.5 text-center">
                                    <span class="inline-flex items-center justify-center px-3 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-lg text-xs font-black">
                                        {{ number_format($price['price'], 2) }} د
                                    </span>
                                </td>
                                <td class="px-5 py-2.5 text-center text-xs text-gray-500 dark:text-gray-400 font-bold">{{ $price['times'] }}</td>
                                <td class="px-5 py-2.5 text-center text-xs text-gray-700 dark:text-gray-300 font-bold">{{ number_format($price['total_qty']) }}</td>
                                <td class="px-5 py-2.5 text-center text-xs text-gray-700 dark:text-gray-300 font-bold">{{ number_format($price['total_amount'], 0) }}</td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-12 text-center shadow-lg">
            <i data-lucide="inbox" class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-3"></i>
            <p class="text-gray-500 dark:text-gray-400">لا توجد مبيعات في هذه الفترة</p>
        </div>
        @endif

        @elseif(request()->filled('user_id'))
        <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-12 text-center shadow-lg">
            <i data-lucide="inbox" class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-3"></i>
            <p class="text-gray-500 dark:text-gray-400">لا توجد بيانات</p>
        </div>
        @else
        <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-12 text-center shadow-lg">
            <i data-lucide="user-search" class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-3"></i>
            <p class="text-gray-500 dark:text-gray-400 font-bold">اختر موظفاً لعرض بياناته</p>
        </div>
        @endif
    @endif

    {{-- ===== COMPARE MODE ===== --}}
    @if($mode === 'compare')
        @if($compareData && count($compareData) > 0)

        {{-- Grand totals --}}
        @php
            $grandQty    = array_sum(array_column($compareData, 'total_qty'));
            $grandAmount = array_sum(array_column($compareData, 'total_amount'));
        @endphp
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-white dark:bg-dark-card rounded-2xl p-4 border border-gray-200 dark:border-dark-border shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="package" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold">إجمالي الكمية</p>
                    <p class="text-xl font-black text-gray-900 dark:text-white">{{ number_format($grandQty) }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-2xl p-4 border border-gray-200 dark:border-dark-border shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="banknote" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 font-bold">إجمالي المبلغ</p>
                    <p class="text-xl font-black text-primary-600 dark:text-primary-400">{{ number_format($grandAmount, 0) }} <span class="text-xs font-normal text-gray-500">د</span></p>
                </div>
            </div>
        </div>

        {{-- Compare Table with accordion --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-lg overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-gray-200 dark:border-dark-border">
                <h3 class="font-black text-gray-900 dark:text-white flex items-center gap-2">
                    <i data-lucide="users" class="w-5 h-5 text-purple-500"></i>
                    مقارنة الموظفين
                    <span class="text-xs font-bold text-gray-400 dark:text-gray-500 mr-1">— مرتب حسب {{ $sortBy === 'qty' ? 'الكمية' : 'المبلغ' }}</span>
                </h3>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-dark-border">
                @foreach($compareData as $index => $entry)
                @php
                    $u = $entry['user'];
                    $isM = $u->role_id === 3;
                    $pct = $grandAmount > 0 ? round(($entry['total_amount'] / $grandAmount) * 100, 1) : 0;
                @endphp
                <div x-data="{ open: false }">
                    {{-- Summary Row --}}
                    <button @click="open = !open"
                        class="w-full flex items-center gap-3 px-5 py-4 hover:bg-gray-50 dark:hover:bg-dark-bg/60 transition-colors text-right">

                        {{-- Rank --}}
                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-black shrink-0
                            {{ $index === 0 ? 'bg-amber-400 text-white' : ($index === 1 ? 'bg-gray-300 dark:bg-gray-600 text-gray-700 dark:text-gray-200' : ($index === 2 ? 'bg-orange-300 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400')) }}">
                            {{ $index + 1 }}
                        </span>

                        {{-- Avatar --}}
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $isM ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-green-100 dark:bg-green-900/30' }}">
                            <i data-lucide="user" class="w-4 h-4 {{ $isM ? 'text-blue-600 dark:text-blue-400' : 'text-green-600 dark:text-green-400' }}"></i>
                        </div>

                        {{-- Name + badge --}}
                        <div class="flex-1 min-w-0 text-right">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-black text-gray-900 dark:text-white text-sm">{{ $u->full_name }}</span>
                                <span class="px-2 py-0.5 rounded-lg text-xs font-black {{ $isM ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' }}">
                                    {{ $isM ? 'مسوق' : 'مبيعات' }}
                                </span>
                            </div>
                            {{-- Progress bar --}}
                            <div class="mt-1.5 h-1.5 bg-gray-100 dark:bg-gray-800 rounded-full overflow-hidden w-full max-w-xs">
                                <div class="h-full rounded-full {{ $isM ? 'bg-blue-500' : 'bg-green-500' }}" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>

                        {{-- Stats --}}
                        <div class="flex items-center gap-4 shrink-0">
                            <div class="text-center hidden sm:block">
                                <p class="text-xs text-gray-400 dark:text-gray-500">كمية</p>
                                <p class="text-sm font-black text-gray-700 dark:text-gray-300">{{ number_format($entry['total_qty']) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-400 dark:text-gray-500">مبلغ</p>
                                <p class="text-sm font-black text-primary-600 dark:text-primary-400">{{ number_format($entry['total_amount'], 0) }}</p>
                            </div>
                            <div class="text-center hidden sm:block">
                                <p class="text-xs text-gray-400 dark:text-gray-500">نسبة</p>
                                <p class="text-sm font-black text-gray-500 dark:text-gray-400">{{ $pct }}%</p>
                            </div>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''"></i>
                        </div>
                    </button>

                    {{-- Detail accordion --}}
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
                                            <span class="inline-flex items-center justify-center px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded text-xs font-black">{{ number_format($product['total_qty']) }}</span>
                                        </td>
                                        <td class="px-5 py-2.5 text-center">
                                            <span class="text-xs font-black text-primary-600 dark:text-primary-400">{{ number_format($product['total_amount'], 0) }}</span>
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
        <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-12 text-center shadow-lg">
            <i data-lucide="inbox" class="w-12 h-12 text-gray-400 dark:text-gray-600 mx-auto mb-3"></i>
            <p class="text-gray-500 dark:text-gray-400">لا توجد بيانات في هذه الفترة</p>
        </div>
        @endif
    @endif

</div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() { lucide.createIcons(); });
</script>
@endpush
@endsection
