@extends('layouts.app')

@section('title', 'المخزن الرئيسي')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
            <div class="lg:col-span-8">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إدارة المخزون
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    المخزن الرئيسي
                </h1>
            </div>

            @if(request()->routeIs('warehouse.*'))
            <div class="lg:col-span-4 lg:translate-y-[30px]">
                <a href="{{ route('warehouse.factory-invoices.index') }}" class="px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2 w-full">
                    <i data-lucide="package-plus" class="w-5 h-5"></i>
                    فواتير المصنع
                </a>
            </div>
            @endif
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl p-4 shadow-lg shadow-gray-200/50 dark:shadow-none border border-gray-200 dark:border-dark-border">
            <form method="GET" class="flex flex-col md:flex-row gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">بحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث بالاسم أو الباركود..." class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">الحالة</label>
                    <select name="status" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="">الكل</option>
                        <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>متوفر</option>
                        <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>غير متوفر</option>
                    </select>
                </div>
                <div class="flex gap-2 items-end">
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2 justify-center">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        فلترة
                    </button>
                    @if(request('search') || request('status'))
                        <a href="{{ request()->url() }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2 justify-center">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            إلغاء
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Products Table --}}
        <div class="bg-white dark:bg-dark-card rounded-[2rem] shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider">المنتج</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider">الباركود</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider">السعر</th>
                            <th class="px-6 py-4 text-center text-xs font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider">الكمية المتوفرة</th>
                            <th class="px-6 py-4 text-center text-xs font-black text-gray-600 dark:text-gray-400 uppercase tracking-wider">آخر تحديث</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center text-primary-600 dark:text-primary-400">
                                        <i data-lucide="package" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $product->name }}</div>
                                        @if($product->description)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($product->description, 50) }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-mono text-gray-600 dark:text-gray-400">{{ $product->barcode ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ number_format($product->current_price, 2) }} ر.س</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @php
                                    $quantity = $product->stock_quantity ?? 0;
                                @endphp
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-bold {{ $quantity > 0 ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400' : 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' }}">
                                    <i data-lucide="{{ $quantity > 0 ? 'check-circle' : 'x-circle' }}" class="w-4 h-4"></i>
                                    {{ number_format($quantity) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $product->stock_updated_at ? \Carbon\Carbon::parse($product->stock_updated_at)->format('Y-m-d H:i') : '-' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="inbox" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد منتجات</h3>
                                <p class="text-gray-500 dark:text-dark-muted">لم يتم العثور على أي منتجات</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-dark-border">
                {{ $products->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
@endsection
