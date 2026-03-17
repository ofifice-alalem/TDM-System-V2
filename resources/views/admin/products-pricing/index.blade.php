@extends('layouts.app')
@section('title', 'تسعير المنتجات')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-[1400px] mx-auto space-y-6 px-2">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">إدارة النظام</span>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white mt-2">تسعير المنتجات</h1>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">تحليل أسعار البيع لكل منتج من المتاجر والعملاء</p>
            </div>
        </div>

        {{-- Filter --}}
        <form method="GET" action="{{ route('admin.products-pricing.index') }}" class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-5 shadow-sm">
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
                <a href="{{ route('admin.products-pricing.index', array_merge(request()->query(), ['export_json' => 1])) }}"
                    class="px-6 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                    <i data-lucide="file-json" class="w-4 h-4"></i> تصدير JSON
                </a>
            </div>
        </form>

        {{-- Stats --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-5 shadow-sm">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">عدد المنتجات</p>
                <p class="text-2xl font-black text-primary-600 dark:text-primary-400">{{ count($products) }}</p>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-5 shadow-sm">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">إجمالي الكميات</p>
                <p class="text-2xl font-black text-blue-600 dark:text-blue-400">{{ number_format($grandQty) }}</p>
            </div>
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-5 shadow-sm lg:col-span-2">
                <p class="text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">إجمالي المبيعات الكلي</p>
                <p class="text-2xl font-black text-green-600 dark:text-green-400">{{ number_format($grandAmount, 2) }} <span class="text-sm font-bold text-gray-400">دينار</span></p>
            </div>
        </div>

        {{-- Table --}}
        @if(empty($products))
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border p-16 text-center shadow-sm">
                <i data-lucide="package-x" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
                <p class="text-gray-500 dark:text-gray-400 font-bold">لا توجد بيانات في هذه الفترة</p>
            </div>
        @else
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-sm overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                            <th class="px-4 py-3 text-right font-bold text-gray-700 dark:text-gray-300 w-8"></th>
                            <th class="px-4 py-3 text-right font-bold text-gray-700 dark:text-gray-300">المنتج</th>
                            <th class="px-4 py-3 text-center font-bold text-gray-700 dark:text-gray-300">عدد مرات البيع</th>
                            <th class="px-4 py-3 text-center font-bold text-gray-700 dark:text-gray-300">إجمالي الكمية</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-gray-300">متوسط السعر</th>
                            <th class="px-4 py-3 text-left font-bold text-gray-700 dark:text-gray-300">إجمالي المبلغ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $i => $product)
                            <tr x-data="{ open: false }"
                                class="border-b border-gray-100 dark:border-dark-border hover:bg-gray-50 dark:hover:bg-dark-bg/50 transition-colors cursor-pointer"
                                @click="open = !open">
                                <td class="px-4 py-3 text-center">
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200 mx-auto" :class="open ? 'rotate-180' : ''"></i>
                                </td>
                                <td class="px-4 py-3 font-bold text-gray-900 dark:text-white">{{ $product['product_name'] }}</td>
                                <td class="px-4 py-3 text-center font-mono text-gray-700 dark:text-gray-300">{{ number_format($product['total_times']) }}</td>
                                <td class="px-4 py-3 text-center font-mono text-blue-600 dark:text-blue-400 font-bold">{{ number_format($product['total_qty']) }}</td>
                                <td class="px-4 py-3 text-left font-mono text-amber-600 dark:text-amber-400 font-bold">{{ number_format($product['avg_price'], 2) }}</td>
                                <td class="px-4 py-3 text-left font-mono text-green-600 dark:text-green-400 font-black">{{ number_format($product['total_amount'], 2) }}</td>
                            </tr>

                            {{-- Accordion Detail --}}
                            <tr x-data="{ open: false }" x-show="$el.previousElementSibling.__x.$data.open" style="display:none">
                                <td colspan="6" class="px-0 py-0 bg-gray-50 dark:bg-dark-bg/30">
                                    <div class="px-8 py-4">
                                        <p class="text-xs font-black text-gray-500 dark:text-gray-400 mb-3 uppercase tracking-wider">تفصيل الأسعار</p>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                            @foreach($product['prices'] as $price)
                                                <div class="bg-white dark:bg-dark-card rounded-xl border border-gray-200 dark:border-dark-border p-4 flex items-center justify-between gap-4">
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-bold mb-0.5">السعر</p>
                                                        <p class="text-lg font-black text-gray-900 dark:text-white font-mono">{{ number_format($price['price'], 2) }}</p>
                                                    </div>
                                                    <div class="text-center">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-bold mb-0.5">مرات البيع</p>
                                                        <p class="text-lg font-black text-primary-600 dark:text-primary-400">{{ $price['times'] }}</p>
                                                    </div>
                                                    <div class="text-center">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-bold mb-0.5">الكمية</p>
                                                        <p class="text-lg font-black text-blue-600 dark:text-blue-400">{{ number_format($price['total_qty']) }}</p>
                                                    </div>
                                                    <div class="text-left">
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-bold mb-0.5">الإجمالي</p>
                                                        <p class="text-lg font-black text-green-600 dark:text-green-400 font-mono">{{ number_format($price['total_amount'], 2) }}</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-100 dark:bg-dark-bg border-t-2 border-gray-300 dark:border-dark-border">
                            <td colspan="2" class="px-4 py-3 font-black text-gray-900 dark:text-white">الإجمالي الكلي</td>
                            <td class="px-4 py-3 text-center font-black font-mono text-gray-700 dark:text-gray-300">—</td>
                            <td class="px-4 py-3 text-center font-black font-mono text-blue-600 dark:text-blue-400">{{ number_format($grandQty) }}</td>
                            <td class="px-4 py-3 text-left font-black font-mono text-gray-400">—</td>
                            <td class="px-4 py-3 text-left font-black font-mono text-green-600 dark:text-green-400">{{ number_format($grandAmount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    lucide.createIcons();

    // Accordion: ربط صف التفصيل بصف المنتج
    document.querySelectorAll('tbody tr[x-data]').forEach((row, i, rows) => {
        if (!row.nextElementSibling) return;
        const detail = row.nextElementSibling;
        if (!detail.querySelector('.grid')) return;

        row.addEventListener('click', () => {
            const isOpen = detail.style.display !== 'none';
            detail.style.display = isOpen ? 'none' : 'table-row';
            const icon = row.querySelector('[data-lucide="chevron-down"]');
            if (icon) icon.style.transform = isOpen ? '' : 'rotate(180deg)';
        });
    });
});
</script>
@endpush
@endsection
