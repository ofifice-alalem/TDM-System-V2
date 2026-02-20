@extends('layouts.app')

@section('title', 'فاتورة مصنع جديدة')

@section('content')
@php
    $routePrefix = request()->routeIs('admin.*') ? 'admin' : 'warehouse';
@endphp

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
            <div class="lg:col-span-8">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        تعبئة المخزن
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    فاتورة مصنع جديدة
                </h1>
            </div>

            <div class="lg:col-span-4 lg:translate-y-[30px]">
                <a href="{{ route($routePrefix . '.factory-invoices.index') }}" class="px-8 py-4 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 w-full">
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    عودة
                </a>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route($routePrefix . '.factory-invoices.store') }}">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                {{-- Right: Products --}}
                <div class="lg:col-span-8">
                    <div class="bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white">المنتجات</h2>
                            <button type="button" onclick="addProductRow()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                إضافة منتج
                            </button>
                        </div>

                        <div id="products-container" class="space-y-3">
                            {{-- Product rows will be added here --}}
                        </div>
                    </div>
                </div>

                {{-- Left: Notes & Actions --}}
                <div class="lg:col-span-4">
                    <div class="bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border sticky top-8">
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-6">تفاصيل الفاتورة</h2>
                        
                        {{-- Notes --}}
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">ملاحظات</label>
                            <textarea name="notes" rows="4" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="أضف ملاحظات إضافية...">{{ old('notes') }}</textarea>
                        </div>

                        {{-- Actions --}}
                        <div class="space-y-3">
                            <button type="submit" class="w-full px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2">
                                <i data-lucide="save" class="w-5 h-5"></i>
                                حفظ الفاتورة
                            </button>
                            <a href="{{ route($routePrefix . '.factory-invoices.index') }}" class="w-full px-8 py-4 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                                <i data-lucide="x" class="w-5 h-5"></i>
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const products = @json($products);
    let rowIndex = 0;
    let selectedProducts = new Set();

    function addProductRow() {
        const container = document.getElementById('products-container');
        const row = document.createElement('div');
        row.className = 'flex gap-3 items-start bg-gray-50 dark:bg-dark-bg p-4 rounded-xl border border-gray-200 dark:border-dark-border';
        row.id = `product-row-${rowIndex}`;
        
        const availableProducts = products.filter(p => !selectedProducts.has(p.id));
        
        row.innerHTML = `
            <div class="flex-1">
                <select name="items[${rowIndex}][product_id]" required onchange="handleProductSelect(${rowIndex}, this.value)" class="w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">اختر المنتج</option>
                    ${availableProducts.map(p => `<option value="${p.id}">${p.name}</option>`).join('')}
                </select>
            </div>
            <div class="w-32">
                <input type="number" name="items[${rowIndex}][quantity]" min="1" required placeholder="الكمية" class="w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <button type="button" onclick="removeProductRow(${rowIndex})" class="w-10 h-10 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors flex items-center justify-center">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        `;
        
        row.dataset.selectedProduct = '';
        container.appendChild(row);
        lucide.createIcons();
        rowIndex++;
    }

    function handleProductSelect(index, productId) {
        const row = document.getElementById(`product-row-${index}`);
        const oldProduct = row.dataset.selectedProduct;
        
        if (oldProduct) {
            selectedProducts.delete(parseInt(oldProduct));
        }
        
        if (productId) {
            selectedProducts.add(parseInt(productId));
            row.dataset.selectedProduct = productId;
        }
        
        updateAllDropdowns();
    }

    function updateAllDropdowns() {
        const container = document.getElementById('products-container');
        const rows = container.querySelectorAll('[id^="product-row-"]');
        
        rows.forEach(row => {
            const select = row.querySelector('select');
            const currentValue = select.value;
            const availableProducts = products.filter(p => !selectedProducts.has(p.id) || p.id == currentValue);
            
            const options = `
                <option value="">اختر المنتج</option>
                ${availableProducts.map(p => `<option value="${p.id}" ${p.id == currentValue ? 'selected' : ''}>${p.name}</option>`).join('')}
            `;
            
            select.innerHTML = options;
        });
    }

    function removeProductRow(index) {
        const row = document.getElementById(`product-row-${index}`);
        const productId = row.dataset.selectedProduct;
        
        if (productId) {
            selectedProducts.delete(parseInt(productId));
        }
        
        row.remove();
        updateAllDropdowns();
    }

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        addProductRow();
    });
</script>
@endpush
@endsection
