@extends('layouts.app')

@section('title', 'طلب بضاعة جديد')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto space-y-8 px-3 lg:px-8">
        
        {{-- Header & Quick Actions --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in-down">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        طلب جديد
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    إنشاء طلب بضاعة
                </h1>
            </div>

            <div class="flex gap-3 w-full md:w-auto">
                <a href="{{ route('marketer.requests.index') }}" class="px-12 py-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center justify-center gap-2 flex-1 md:flex-auto">
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    عودة
                </a>
            </div>
        </div>

        <form action="{{ route('marketer.requests.store') }}" method="POST" class="space-y-4 md:space-y-6">
            @csrf
            
            {{-- Products Container --}}
            <div class="bg-white dark:bg-dark-card rounded-[2rem] p-3 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border relative overflow-hidden animate-slide-up">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="font-bold text-xl text-gray-900 dark:text-white flex items-center gap-3">
                            <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                                <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                            </span>
                            المنتجات المطلوبة
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-dark-muted mt-2 mr-14 font-medium">اختر المنتجات والكميات المطلوبة</p>
                    </div>
                </div>

                <div id="items-container" class="space-y-4">
                    <div class="item-row bg-gray-50/50 dark:bg-dark-bg/60 rounded-2xl p-4 md:p-6 border border-gray-100 dark:border-dark-border">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-6">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">المنتج</label>
                                <select name="items[0][product_id]" class="product-select w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all" required>
                                    <option value="">اختر المنتج</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-stock="{{ $product->stock }}">
                                            {{ $product->name }} (متوفر: {{ $product->stock }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-5">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الكمية</label>
                                <div class="flex gap-2">
                                    <input type="number" name="items[0][quantity]" class="quantity-input flex-1 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all" min="1" max="" placeholder="اختر المنتج أولاً" required>
                                    <button type="button" class="remove-item hidden md:hidden px-4 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold transition-all">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="hidden md:flex md:col-span-1 items-end">
                                <button type="button" class="remove-item w-full px-3 py-3 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold transition-all">
                                    <i data-lucide="trash-2" class="w-4 h-4 mx-auto"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-item" class="mt-4 w-full md:w-auto px-6 py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold transition-all shadow-lg shadow-emerald-200/50 dark:shadow-none flex items-center justify-center gap-2">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i>
                    إضافة منتج جديد
                </button>
            </div>

            {{-- Notes Section --}}
            <div class="bg-white dark:bg-dark-card rounded-[2rem] p-3 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up" style="animation-delay: 0.1s">
                <div class="flex items-center gap-3 mb-6">
                    <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                        <i data-lucide="sticky-note" class="w-5 h-5"></i>
                    </span>
                    <h3 class="font-bold text-xl text-gray-900 dark:text-white">ملاحظات (اختياري)</h3>
                </div>
                <textarea name="notes" rows="4" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all" placeholder="أضف أي ملاحظات إضافية..."></textarea>
            </div>

            {{-- Submit Button --}}
            <div class="flex gap-4 animate-slide-up mt-6" style="animation-delay: 0.2s">
                <button type="submit" class="flex-1 px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl text-sm md:text-base font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2">
                    <i data-lucide="send" class="w-5 h-5"></i>
                    إرسال الطلب
                </button>
                <a href="{{ route('marketer.requests.index') }}" class="flex-1 px-8 py-4 bg-gray-500 hover:bg-gray-600 text-white rounded-xl text-sm md:text-base font-bold transition-all shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center justify-center gap-2">
                    <i data-lucide="x" class="w-5 h-5"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let itemIndex = 1;

function updateAvailableProducts() {
    const selectedProducts = [];
    document.querySelectorAll('.product-select').forEach(select => {
        if (select.value) selectedProducts.push(select.value);
    });
    
    document.querySelectorAll('.product-select').forEach(select => {
        const currentValue = select.value;
        Array.from(select.options).forEach(option => {
            if (option.value && option.value !== currentValue) {
                option.style.display = selectedProducts.includes(option.value) ? 'none' : 'block';
            }
        });
    });
}

function updateMaxQuantity(selectElement) {
    const row = selectElement.closest('.item-row');
    const quantityInput = row.querySelector('.quantity-input');
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const stock = selectedOption?.dataset.stock || 0;
    
    quantityInput.max = stock;
    quantityInput.value = '';
    quantityInput.placeholder = `الحد الأقصى: ${stock}`;
    quantityInput.disabled = !stock || stock == 0;
    
    updateAvailableProducts();
}

function enforceMaxValue(input) {
    const max = parseInt(input.max);
    const value = parseInt(input.value);
    if (value > max) input.value = max;
}

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    document.querySelectorAll('.product-select').forEach(select => {
        select.addEventListener('change', function() {
            updateMaxQuantity(this);
        });
    });
    
    document.querySelectorAll('.quantity-input').forEach(input => {
        input.addEventListener('input', function() {
            enforceMaxValue(this);
        });
    });
});

document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const newItem = container.firstElementChild.cloneNode(true);
    
    newItem.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${itemIndex}]`);
        el.value = '';
        if(el.classList.contains('quantity-input')) {
            el.max = '';
            el.placeholder = 'اختر المنتج أولاً';
            el.disabled = false;
        }
    });
    
    newItem.querySelector('.remove-item').classList.remove('hidden');
    container.appendChild(newItem);
    
    newItem.querySelector('.product-select').addEventListener('change', function() {
        updateMaxQuantity(this);
    });
    
    newItem.querySelector('.quantity-input').addEventListener('input', function() {
        enforceMaxValue(this);
    });
    
    lucide.createIcons();
    updateAvailableProducts();
    itemIndex++;
});

document.addEventListener('click', function(e) {
    if(e.target.closest('.remove-item')) {
        if(document.querySelectorAll('.item-row').length > 1) {
            e.target.closest('.item-row').remove();
            updateAvailableProducts();
        }
    }
});
</script>
@endpush
