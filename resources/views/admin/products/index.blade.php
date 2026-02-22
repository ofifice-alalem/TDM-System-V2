@extends('layouts.app')

@section('title', 'إدارة المنتجات')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4">
        
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">إدارة المنتجات</h1>
            <a href="{{ route('admin.products.create') }}" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                <i data-lucide="plus" class="w-5 h-5"></i>
                إضافة منتج
            </a>
        </div>

        @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-400 px-4 py-3 rounded-xl mb-6">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-white dark:bg-dark-card rounded-2xl shadow-xl overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-dark-bg">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المنتج</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400">الباركود</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400">السعر</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400">سعر الجملة</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                    @forelse($products as $product)
                    <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900 dark:text-white">{{ $product->name }}</div>
                            @if($product->description)
                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($product->description, 50) }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $product->barcode ?? '-' }}</td>
                        <td class="px-6 py-4 font-bold text-gray-900 dark:text-white">{{ number_format($product->current_price, 2) }} دينار</td>
                        <td class="px-6 py-4 font-bold text-primary-600 dark:text-primary-400">{{ $product->customer_price ? number_format($product->customer_price, 2) . ' دينار' : '-' }}</td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('admin.products.edit', $product) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm font-bold transition-all">
                                <i data-lucide="edit" class="w-4 h-4"></i>
                                تعديل
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="text-gray-500 dark:text-gray-400">لا توجد منتجات</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

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
    lucide.createIcons();
</script>
@endpush
@endsection
