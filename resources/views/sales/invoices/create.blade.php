@extends('layouts.app')

@section('title', 'فاتورة مبيعات جديدة')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-in-down">
            <div class="lg:col-span-8">
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        مبيعات مباشرة
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    فاتورة مبيعات جديدة
                </h1>
            </div>

            <div class="lg:col-span-4 lg:translate-y-[30px]">
                <a href="{{ route('sales.invoices.index') }}" class="px-8 py-4 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 w-full">
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    عودة
                </a>
            </div>
        </div>

        {{-- Form --}}
        <form method="POST" action="{{ route('sales.invoices.store') }}" id="invoice-form">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                {{-- Right: Products --}}
                <div class="lg:col-span-8">
                    <div class="bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border mb-6">
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-6">معلومات العميل</h2>
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">العميل *</label>
                            <select name="customer_id" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">اختر العميل</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

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

                {{-- Left: Summary & Actions --}}
                <div class="lg:col-span-4">
                    <div class="bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border sticky top-8">
                        <h2 class="text-2xl font-black text-gray-900 dark:text-white mb-6">تفاصيل الفاتورة</h2>
                        
                        <div class="mb-6 bg-primary-50 dark:bg-primary-900/20 rounded-xl p-4 border border-primary-200 dark:border-primary-800">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">المجموع الفرعي:</span>
                                <span id="subtotal-display" class="text-lg font-bold text-gray-900 dark:text-white">0 د.ع</span>
                            </div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300">الخصم:</span>
                                <span id="discount-display" class="text-lg font-bold text-red-600">0 د.ع</span>
                            </div>
                            <div class="flex items-center justify-between pt-2 border-t border-primary-200 dark:border-primary-800">
                                <span class="text-base font-black text-gray-900 dark:text-white">الإجمالي:</span>
                                <span id="total-display" class="text-2xl font-black text-primary-600 dark:text-primary-400">0 د.ع</span>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">خصم إضافي</label>
                            <input type="number" id="discount_amount" name="discount_amount" value="0" min="0" step="0.01" onchange="calculateTotal()" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">المبلغ المدفوع الآن</label>
                            <input type="number" id="paid_amount" name="paid_amount" value="0" min="0" step="0.01" oninput="validatePaidAmount()" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="0">
                            <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">اتركه 0 إذا لم يدفع العميل شيئاً</p>
                            <p id="paid-error" class="text-xs text-red-500 mt-1" style="display: none;">المبلغ المدفوع أكبر من الإجمالي!</p>
                        </div>

                        <div class="mb-6" id="payment-method-container" style="display: none;">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">طريقة الدفع</label>
                            <select name="payment_method" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="cash">نقدي</option>
                                <option value="transfer">تحويل</option>
                                <option value="check">شيك</option>
                            </select>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">ملاحظات</label>
                            <textarea name="notes" rows="4" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="أضف ملاحظات...">{{ old('notes') }}</textarea>
                        </div>

                        <div class="space-y-3">
                            <button type="submit" id="submit-btn" class="w-full px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2">
                                <i data-lucide="save" class="w-5 h-5"></i>
                                حفظ الفاتورة
                            </button>
                            <a href="{{ route('sales.invoices.index') }}" class="w-full px-8 py-4 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
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
    const products = {!! json_encode($products) !!};
    let rowIndex = 0;
    let selectedProducts = new Set();
    let subtotal = 0;

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
                    ${availableProducts.map(p => `<option value="${p.id}" data-price="${p.price}" data-stock="${p.stock}">${p.name} - ${p.price} د.ع (متوفر: ${p.stock})</option>`).join('')}
                </select>
            </div>
            <div class="w-32">
                <input type="number" name="items[${rowIndex}][quantity]" min="1" max="0" required placeholder="الكمية" oninput="validateQuantity(${rowIndex}); calculateTotal();" class="w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                <p class="text-xs text-red-500 mt-1" id="qty-error-${rowIndex}" style="display: none;">تجاوز المتوفر!</p>
            </div>
            <button type="button" onclick="removeProductRow(${rowIndex})" class="w-10 h-10 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-xl hover:bg-red-200 dark:hover:bg-red-900/50 transition-colors flex items-center justify-center">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        `;
        
        row.dataset.selectedProduct = '';
        row.dataset.price = '0';
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
            
            const select = row.querySelector('select');
            const option = select.options[select.selectedIndex];
            const stock = option.dataset.stock;
            const price = option.dataset.price;
            
            row.dataset.price = price;
            const qtyInput = row.querySelector('input[type="number"]');
            qtyInput.max = stock;
            qtyInput.value = '';
        }
        
        updateAllDropdowns();
        calculateTotal();
    }

    function validateQuantity(index) {
        const row = document.getElementById(`product-row-${index}`);
        const qtyInput = row.querySelector('input[type="number"]');
        const error = document.getElementById(`qty-error-${index}`);
        const max = parseInt(qtyInput.max);
        const val = parseInt(qtyInput.value);
        
        if (val > max) {
            error.style.display = 'block';
            qtyInput.value = max;
        } else {
            error.style.display = 'none';
        }
    }

    function calculateTotal() {
        subtotal = 0;
        const container = document.getElementById('products-container');
        const rows = container.querySelectorAll('[id^="product-row-"]');
        
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('input[type="number"]').value) || 0;
            const price = parseFloat(row.dataset.price) || 0;
            subtotal += qty * price;
        });
        
        const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
        const total = subtotal - discount;
        
        document.getElementById('subtotal-display').textContent = subtotal.toFixed(0) + ' د.ع';
        document.getElementById('discount-display').textContent = discount.toFixed(0) + ' د.ع';
        document.getElementById('total-display').textContent = total.toFixed(0) + ' د.ع';
        
        validatePaidAmount();
    }

    function validatePaidAmount() {
        const paidInput = document.getElementById('paid_amount');
        const paid = parseFloat(paidInput.value) || 0;
        const discount = parseFloat(document.getElementById('discount_amount').value) || 0;
        const total = subtotal - discount;
        const error = document.getElementById('paid-error');
        const submitBtn = document.getElementById('submit-btn');
        const paymentMethodContainer = document.getElementById('payment-method-container');
        
        if (paid > total) {
            error.style.display = 'block';
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            error.style.display = 'none';
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        
        if (paid > 0) {
            paymentMethodContainer.style.display = 'block';
        } else {
            paymentMethodContainer.style.display = 'none';
        }
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
                ${availableProducts.map(p => `<option value="${p.id}" data-price="${p.price}" data-stock="${p.stock}" ${p.id == currentValue ? 'selected' : ''}>${p.name} - ${p.price} د.ع (متوفر: ${p.stock})</option>`).join('')}
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
        calculateTotal();
    }

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        addProductRow();
    });
</script>
@endpush
@endsection
