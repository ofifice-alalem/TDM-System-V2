@extends('layouts.app')

@section('title', 'فاتورة بيع جديدة')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto space-y-8 px-3 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in-down">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        فاتورة جديدة
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    إنشاء فاتورة بيع
                </h1>
            </div>

            <div class="flex gap-3 w-full md:w-auto">
                <a href="{{ route('marketer.sales.index') }}" class="px-12 py-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center justify-center gap-2 flex-1 md:flex-auto">
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    عودة
                </a>
            </div>
        </div>

        <form action="{{ route('marketer.sales.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- القسم الأيمن: المنتجات -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- اختيار المتجر - يظهر فقط في النقال -->
                    <div id="store-select-mobile" class="lg:hidden bg-white dark:bg-dark-card rounded-[2rem] p-3 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                                <i data-lucide="store" class="w-5 h-5"></i>
                            </span>
                            <h3 class="font-bold text-xl text-gray-900 dark:text-white">اختيار المتجر</h3>
                        </div>
                    </div>
                    
                    <div class="bg-white dark:bg-dark-card rounded-[2rem] p-3 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border relative overflow-hidden animate-slide-up">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="font-bold text-xl text-gray-900 dark:text-white flex items-center gap-3">
                            <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                                <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                            </span>
                            المنتجات
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-dark-muted mt-2 mr-14 font-medium">اختر المنتجات والكميات</p>
                    </div>
                </div>

                <div id="items-container" class="space-y-4">
                    <div class="item-row bg-gray-50/50 dark:bg-dark-bg/60 rounded-2xl p-4 md:p-6 border border-gray-100 dark:border-dark-border">
                        <div class="grid grid-cols-12 gap-3">
                            <div class="col-span-12 md:col-span-4">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">المنتج</label>
                                <select name="items[0][product_id]" class="product-select w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm" required>
                                    <option value="">اختر المنتج</option>
                                    @foreach($products as $product)
                                        @if($product->stock > 0)
                                        <option value="{{ $product->id }}" 
                                                data-stock="{{ $product->stock }}" 
                                                data-price="{{ $product->current_price }}"
                                                data-promotion-free="{{ $product->activePromotion->free_quantity ?? 0 }}"
                                                data-promotion-buy="{{ $product->activePromotion->min_quantity ?? 0 }}">
                                            {{ $product->name }} @if($product->activePromotion) ⭐ @endif  |  متوفر: {{ $product->stock }}  |  {{ $product->current_price }} دينار
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                                <div class="promotion-label hidden mt-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg px-3 py-2 text-amber-700 dark:text-amber-400 text-xs font-bold flex items-center gap-2">
                                    <i data-lucide="gift" class="w-4 h-4"></i>
                                    <span class="promotion-text"></span>
                                </div>
                            </div>
                            <div class="col-span-6 md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الكمية</label>
                                <input type="number" name="items[0][quantity]" class="quantity-input w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm" min="1" max="" placeholder="0" required>
                            </div>
                            <div class="col-span-6 md:col-span-2">
                                <label class="block text-sm font-bold text-emerald-600 dark:text-emerald-400 mb-2">هدية</label>
                                <input type="text" class="gift-display w-full bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl px-4 py-3 text-emerald-700 dark:text-emerald-400 text-sm font-bold text-center" value="0" readonly>
                            </div>
                            <div class="col-span-6 md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">سعر الوحدة</label>
                                <input type="text" class="unit-price w-full bg-gray-100 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white text-sm font-bold text-center" value="0" readonly>
                            </div>
                            <div class="col-span-6 md:col-span-2">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الإجمالي</label>
                                <input type="text" class="row-total w-full bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-xl px-4 py-3 text-primary-700 dark:text-primary-400 text-sm font-bold text-center" value="0" readonly>
                            </div>
                        </div>
                        <button type="button" class="remove-item hidden mt-3 w-full px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl font-bold transition-all text-sm flex items-center justify-center gap-2">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                            حذف
                        </button>
                    </div>
                </div>

                        <button type="button" id="add-item" class="mt-4 w-full md:w-auto px-6 py-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-xl font-bold transition-all shadow-lg shadow-emerald-200/50 dark:shadow-none flex items-center justify-center gap-2">
                            <i data-lucide="plus-circle" class="w-5 h-5"></i>
                            إضافة منتج جديد
                        </button>

                        <!-- ملخص الفاتورة -->
                        <div class="mt-6 bg-gradient-to-br from-primary-50 to-primary-100/50 dark:from-dark-bg dark:to-dark-border rounded-2xl p-6 border-2 border-primary-200 dark:border-dark-border">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <i data-lucide="calculator" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                                ملخص الفاتورة
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-primary-200 dark:border-dark-border">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">عدد البضاعة</span>
                                    <span id="total-items" class="text-gray-900 dark:text-white font-bold">0</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-primary-200 dark:border-dark-border">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">السعر الكلي</span>
                                    <span id="subtotal" class="text-gray-900 dark:text-white font-bold">0.00 دينار</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-primary-200 dark:border-dark-border">
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">تخفيض المنتجات</span>
                                    <span id="products-discount" class="text-emerald-600 dark:text-emerald-400 font-bold">0.00 دينار</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b border-primary-200 dark:border-dark-border">
                                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">تخفيض الفاتورة</span>
                                    <span id="invoice-discount" class="text-emerald-600 dark:text-emerald-400 font-bold">0.00 دينار</span>
                                </div>
                                <div class="flex justify-between items-center py-3 bg-primary-600 dark:bg-primary-700 rounded-xl px-4 mt-2">
                                    <span class="text-white font-bold text-lg">المجموع النهائي</span>
                                    <span id="final-total" class="text-white font-black text-xl">0.00 دينار</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- القسم الأيسر: المتجر والملاحظات والإجراءات -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- اختيار المتجر - يظهر فقط في الشاشات الكبيرة -->
                    <div id="store-select-desktop" class="hidden lg:block bg-white dark:bg-dark-card rounded-[2rem] p-3 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                                <i data-lucide="store" class="w-5 h-5"></i>
                            </span>
                            <h3 class="font-bold text-xl text-gray-900 dark:text-white">اختيار المتجر</h3>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-dark-card rounded-[2rem] p-3 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                                <i data-lucide="sticky-note" class="w-5 h-5"></i>
                            </span>
                            <h3 class="font-bold text-xl text-gray-900 dark:text-white">ملاحظات</h3>
                        </div>
                        <textarea name="notes" rows="4" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all" placeholder="أضف أي ملاحظات إضافية..."></textarea>
                    </div>

                    <div class="bg-white dark:bg-dark-card rounded-[2rem] p-3 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                                <i data-lucide="zap" class="w-5 h-5"></i>
                            </span>
                            <h3 class="font-bold text-xl text-gray-900 dark:text-white">الإجراءات</h3>
                        </div>
                        <div class="space-y-3">
                            <button type="submit" class="w-full px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2">
                                <i data-lucide="send" class="w-5 h-5"></i>
                                إنشاء الفاتورة
                            </button>
                            <a href="{{ route('marketer.sales.index') }}" class="w-full px-8 py-4 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center justify-center gap-2">
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
@endsection

@push('scripts')
<script>
// Create single store select and move it based on screen size
const storeSelect = document.createElement('select');
storeSelect.name = 'store_id';
storeSelect.required = true;
storeSelect.className = 'w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all';
storeSelect.innerHTML = `
    <option value="">اختر المتجر</option>
    @foreach($stores as $store)
        <option value="{{ $store->id }}">{{ $store->name }} - {{ $store->owner_name }}</option>
    @endforeach
`;

function moveStoreSelect() {
    const isMobile = window.innerWidth < 1024;
    const container = isMobile ? document.getElementById('store-select-mobile') : document.getElementById('store-select-desktop');
    if (container && !container.contains(storeSelect)) {
        container.appendChild(storeSelect);
    }
}

window.addEventListener('resize', moveStoreSelect);
document.addEventListener('DOMContentLoaded', moveStoreSelect);

let itemIndex = 1;

function calculateGift(buyQty, quantity) {
    if (!buyQty || buyQty == 0) return 0;
    return Math.floor(quantity / buyQty);
}

function calculateRowTotal(row) {
    const select = row.querySelector('.product-select');
    const quantityInput = row.querySelector('.quantity-input');
    const giftDisplay = row.querySelector('.gift-display');
    const unitPriceDisplay = row.querySelector('.unit-price');
    const rowTotalDisplay = row.querySelector('.row-total');
    
    const selectedOption = select.options[select.selectedIndex];
    const price = parseFloat(selectedOption?.dataset.price || 0);
    const quantity = parseInt(quantityInput.value || 0);
    const promotionBuy = parseInt(selectedOption?.dataset.promotionBuy || 0);
    const promotionFree = parseInt(selectedOption?.dataset.promotionFree || 0);
    
    const gift = promotionBuy > 0 ? Math.floor(quantity / promotionBuy) * promotionFree : 0;
    const total = (quantity + gift) * price;
    
    giftDisplay.value = gift;
    unitPriceDisplay.value = price.toFixed(2);
    rowTotalDisplay.value = total.toFixed(2);
    
    calculateInvoiceSummary();
}

function calculateInvoiceSummary() {
    let totalItems = 0;
    let subtotal = 0;
    let productsDiscount = 0;
    
    document.querySelectorAll('.item-row').forEach(row => {
        const select = row.querySelector('.product-select');
        const quantityInput = row.querySelector('.quantity-input');
        const selectedOption = select.options[select.selectedIndex];
        
        if (select.value && quantityInput.value) {
            const price = parseFloat(selectedOption?.dataset.price || 0);
            const quantity = parseInt(quantityInput.value || 0);
            const promotionBuy = parseInt(selectedOption?.dataset.promotionBuy || 0);
            const promotionFree = parseInt(selectedOption?.dataset.promotionFree || 0);
            
            const gift = promotionBuy > 0 ? Math.floor(quantity / promotionBuy) * promotionFree : 0;
            const itemTotal = (quantity + gift) * price;
            const itemDiscount = gift * price;
            
            totalItems += quantity + gift;
            subtotal += itemTotal;
            productsDiscount += itemDiscount;
        }
    });
    
    // Fetch invoice discount from server
    const subtotalBeforeDiscount = subtotal - productsDiscount;
    fetch(`/calculate-invoice-discount?amount=${subtotalBeforeDiscount}`)
        .then(response => response.json())
        .then(data => {
            const invoiceDiscount = data.discount_amount || 0;
            const finalTotal = subtotal - productsDiscount - invoiceDiscount;
            
            document.getElementById('total-items').textContent = totalItems;
            document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' دينار';
            document.getElementById('products-discount').textContent = productsDiscount.toFixed(2) + ' دينار';
            document.getElementById('invoice-discount').textContent = invoiceDiscount.toFixed(2) + ' دينار';
            document.getElementById('final-total').textContent = finalTotal.toFixed(2) + ' دينار';
        })
        .catch(() => {
            const invoiceDiscount = 0;
            const finalTotal = subtotal - productsDiscount - invoiceDiscount;
            
            document.getElementById('total-items').textContent = totalItems;
            document.getElementById('subtotal').textContent = subtotal.toFixed(2) + ' دينار';
            document.getElementById('products-discount').textContent = productsDiscount.toFixed(2) + ' دينار';
            document.getElementById('invoice-discount').textContent = invoiceDiscount.toFixed(2) + ' دينار';
            document.getElementById('final-total').textContent = finalTotal.toFixed(2) + ' دينار';
        });
}

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
    const promotionLabel = row.querySelector('.promotion-label');
    const promotionText = row.querySelector('.promotion-text');
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    const stock = selectedOption?.dataset.stock || 0;
    const promotionBuy = parseInt(selectedOption?.dataset.promotionBuy || 0);
    const promotionFree = parseInt(selectedOption?.dataset.promotionFree || 0);
    
    quantityInput.max = stock;
    quantityInput.value = '';
    quantityInput.placeholder = `الحد الأقصى: ${stock}`;
    quantityInput.disabled = !stock || stock == 0;
    
    if (promotionBuy > 0 && promotionFree > 0) {
        promotionLabel.classList.remove('hidden');
        promotionText.textContent = `عرض: اشتري ${promotionBuy} واحصل على ${promotionFree} مجاناً`;
        lucide.createIcons();
    } else {
        promotionLabel.classList.add('hidden');
    }
    
    calculateRowTotal(row);
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
            calculateRowTotal(this.closest('.item-row'));
        });
    });
});

document.getElementById('add-item').addEventListener('click', function() {
    const container = document.getElementById('items-container');
    const newItem = container.firstElementChild.cloneNode(true);
    
    newItem.querySelectorAll('select, input').forEach(el => {
        el.name = el.name?.replace(/\[\d+\]/, `[${itemIndex}]`);
        if (el.classList.contains('quantity-input')) {
            el.value = '';
            el.max = '';
            el.placeholder = 'اختر المنتج أولاً';
            el.disabled = false;
        } else if (el.classList.contains('product-select')) {
            el.value = '';
        } else if (el.classList.contains('gift-display') || el.classList.contains('unit-price') || el.classList.contains('row-total')) {
            el.value = '0';
        }
    });
    
    const promotionLabel = newItem.querySelector('.promotion-label');
    if (promotionLabel) promotionLabel.classList.add('hidden');
    
    newItem.querySelector('.remove-item').classList.remove('hidden');
    container.appendChild(newItem);
    
    newItem.querySelector('.product-select').addEventListener('change', function() {
        updateMaxQuantity(this);
    });
    
    newItem.querySelector('.quantity-input').addEventListener('input', function() {
        enforceMaxValue(this);
        calculateRowTotal(this.closest('.item-row'));
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
            calculateInvoiceSummary();
        }
    }
});
</script>
@endpush
