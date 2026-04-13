@extends('layouts.app')

@section('title', 'إدارة المنتجات')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">

        {{-- Header --}}
        <div class="animate-fade-in-down">
            <div class="flex items-center justify-between mb-2">
                <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                    إدارة المنتجات
                </span>
                <a href="{{ route('admin.products.create') }}" class="px-4 sm:px-6 py-2.5 sm:py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-md hover:shadow-lg flex items-center gap-2 text-sm sm:text-base">
                    <i data-lucide="plus" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                    <span class="hidden sm:inline">إضافة منتج جديد</span>
                    <span class="sm:hidden">إضافة</span>
                </a>
            </div>
            <h1 class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                المنتجات
            </h1>
        </div>

        @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl animate-fade-in">
            {{ session('success') }}
        </div>
        @endif

        {{-- Desktop Table --}}
        <div class="hidden md:block bg-white dark:bg-dark-card rounded-3xl shadow-lg border border-gray-200 dark:border-dark-border overflow-hidden animate-slide-up">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider">المنتج</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider">الباركود</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider">سعر الجملة</th>
                            <th class="px-6 py-4 text-right text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider">سعر جملة الجملة</th>
                            <th class="px-6 py-4 text-center text-xs font-black text-gray-700 dark:text-gray-300 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $product->name }}</div>
                                @if($product->description)
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($product->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($product->barcode)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 dark:bg-dark-bg rounded-lg text-sm text-gray-600 dark:text-gray-400 font-medium">
                                    <i data-lucide="scan-barcode" class="w-3.5 h-3.5"></i>
                                    {{ $product->barcode }}
                                </span>
                                @else
                                <span class="text-gray-400 dark:text-gray-600">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 dark:bg-dark-bg rounded-lg text-sm font-bold text-gray-900 dark:text-white border border-gray-200 dark:border-dark-border">
                                    {{ number_format($product->current_price, 2) }} دينار
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if($product->customer_price)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary-50 dark:bg-primary-500/10 rounded-lg text-sm font-bold text-primary-600 dark:text-primary-400 border border-primary-100 dark:border-primary-500/30">
                                    {{ number_format($product->customer_price, 2) }} دينار
                                </span>
                                @else
                                <span class="text-gray-400 dark:text-gray-600">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center">
                                    <a href="{{ route('admin.products.edit', $product) }}" class="w-9 h-9 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-all flex items-center justify-center shadow-sm hover:shadow-md">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="w-24 h-24 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="package" class="w-12 h-12 text-gray-400 dark:text-gray-600"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">لا توجد منتجات</h3>
                                <p class="text-gray-500 dark:text-dark-muted">قم بإضافة منتج جديد للبدء</p>
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

        {{-- Mobile Cards --}}
        <div class="md:hidden space-y-4 animate-slide-up">
            @forelse($products as $product)
            <div class="bg-white dark:bg-dark-card rounded-3xl p-5 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 group">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <div>
                        <h3 class="font-black text-gray-900 dark:text-white leading-snug">{{ $product->name }}</h3>
                        @if($product->description)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Str::limit($product->description, 55) }}</p>
                        @endif
                    </div>
                    <a href="{{ route('admin.products.edit', $product) }}" class="shrink-0 w-9 h-9 bg-primary-600 hover:bg-primary-700 text-white rounded-xl flex items-center justify-center transition-all shadow-sm">
                        <i data-lucide="edit" class="w-4 h-4"></i>
                    </a>
                </div>

                <div class="grid grid-cols-2 gap-2.5">
                    @if($product->barcode)
                    <div class="col-span-2 flex items-center gap-2 px-3 py-2.5 bg-gray-50 dark:bg-dark-bg rounded-xl border border-gray-200 dark:border-dark-border">
                        <i data-lucide="scan-barcode" class="w-4 h-4 text-gray-400 shrink-0"></i>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">الباركود</p>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $product->barcode }}</p>
                        </div>
                    </div>
                    @endif
                    <div class="flex items-center gap-2 px-3 py-2.5 bg-gray-50 dark:bg-dark-bg rounded-xl border border-gray-200 dark:border-dark-border">
                        <i data-lucide="tag" class="w-4 h-4 text-gray-400 shrink-0"></i>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">سعر الجملة</p>
                            <p class="text-sm font-black text-gray-900 dark:text-white">{{ number_format($product->current_price, 2) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 px-3 py-2.5 bg-primary-50 dark:bg-primary-500/10 rounded-xl border border-primary-100 dark:border-primary-500/30">
                        <i data-lucide="layers" class="w-4 h-4 text-primary-500 shrink-0"></i>
                        <div>
                            <p class="text-xs text-primary-500 dark:text-primary-400">جملة الجملة</p>
                            <p class="text-sm font-black text-primary-600 dark:text-primary-400">
                                {{ $product->customer_price ? number_format($product->customer_price, 2) : '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-16 bg-white dark:bg-dark-card rounded-3xl border border-gray-200 dark:border-dark-border">
                <div class="w-24 h-24 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="package" class="w-12 h-12 text-gray-400 dark:text-gray-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">لا توجد منتجات</h3>
                <p class="text-gray-500 dark:text-dark-muted">قم بإضافة منتج جديد للبدء</p>
            </div>
            @endforelse

            @if($products->hasPages())
            <div class="pt-2">
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
