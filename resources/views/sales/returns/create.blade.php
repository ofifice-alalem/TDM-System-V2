@extends('layouts.app')

@section('title', 'إضافة مرتجع')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-5xl mx-auto px-4">
        
        <div class="mb-8">
            <a href="{{ route('sales.returns.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-4 transition-colors">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                <span class="font-bold">العودة للمرتجعات</span>
            </a>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white">إضافة مرتجع جديد</h1>
        </div>

        <form action="{{ route('sales.returns.store') }}" method="POST" class="bg-white dark:bg-dark-card rounded-3xl p-8 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
            @csrf

            {{-- Invoice Selection --}}
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">اختر الفاتورة</label>
                <select name="invoice_id" id="invoice_id" required class="w-full px-4 py-3 bg-white dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all">
                    <option value="">اختر فاتورة...</option>
                    @foreach($invoices as $invoice)
                        <option value="{{ $invoice->id }}" data-customer-id="{{ $invoice->customer_id }}" data-customer-name="{{ $invoice->customer->name }}">
                            #{{ $invoice->invoice_number }} - {{ $invoice->customer->name }} - {{ number_format($invoice->total_amount, 0) }} دينار
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="customer_id" id="customer_id">
            </div>

            {{-- Invoice Items --}}
            <div id="invoice-items" class="mb-6 hidden">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-4">منتجات الفاتورة</label>
                <div id="items-container" class="space-y-3"></div>
                <div id="total-section" class="mt-6 p-4 bg-gray-50 dark:bg-dark-bg rounded-xl">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">الإجمالي:</span>
                        <span id="total-amount" class="text-2xl font-black text-orange-600 dark:text-orange-400">0 دينار</span>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">ملاحظات</label>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-white dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 dark:focus:border-primary-500 focus:ring-4 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all"></textarea>
            </div>

            {{-- Submit --}}
            <div class="flex gap-3">
                <button type="submit" id="submit-btn" disabled class="flex-1 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    إضافة مرتجع
                </button>
                <a href="{{ route('sales.returns.index') }}" class="px-6 py-3 bg-gray-200 dark:bg-dark-bg hover:bg-gray-300 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();

        const invoiceSelect = document.getElementById('invoice_id');
        const customerIdInput = document.getElementById('customer_id');
        const invoiceItemsDiv = document.getElementById('invoice-items');
        const itemsContainer = document.getElementById('items-container');
        const totalAmountSpan = document.getElementById('total-amount');
        const submitBtn = document.getElementById('submit-btn');
        let invoiceItems = [];

        invoiceSelect.addEventListener('change', async function() {
            const invoiceId = this.value;
            const selectedOption = this.options[this.selectedIndex];
            
            if (!invoiceId) {
                invoiceItemsDiv.classList.add('hidden');
                itemsContainer.innerHTML = '';
                submitBtn.disabled = true;
                return;
            }

            customerIdInput.value = selectedOption.dataset.customerId;

            try {
                const response = await fetch(`/sales/returns/invoice/${invoiceId}/items`);
                const data = await response.json();
                invoiceItems = data.items;
                
                if (invoiceItems.length === 0) {
                    itemsContainer.innerHTML = '<div class="p-6 text-center bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-500/30"><p class="text-amber-700 dark:text-amber-400 font-bold">جميع منتجات هذه الفاتورة تم إرجاعها بالكامل</p></div>';
                    invoiceItemsDiv.classList.remove('hidden');
                    submitBtn.disabled = true;
                    return;
                }
                
                renderItems();
                invoiceItemsDiv.classList.remove('hidden');
            } catch (error) {
                console.error('Error:', error);
                alert('حدث خطأ في تحميل منتجات الفاتورة');
            }
        });

        function renderItems() {
            itemsContainer.innerHTML = '';
            
            invoiceItems.forEach((item, index) => {
                const itemDiv = document.createElement('div');
                itemDiv.className = 'bg-gray-50 dark:bg-dark-bg rounded-xl p-4 border border-gray-200 dark:border-dark-border';
                itemDiv.innerHTML = `
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <p class="font-bold text-gray-900 dark:text-white mb-1">${item.product_name}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                الكمية الأصلية: ${item.quantity} | 
                                المُرجع سابقاً: ${item.returned_quantity} | 
                                المتاح للإرجاع: <span class="font-bold text-orange-600">${item.available_quantity}</span> | 
                                السعر: ${Number(item.unit_price).toLocaleString()} دينار
                                ${item.previous_returns.length > 0 ? `<br><span class="text-xs">مرتجعات سابقة: ${item.previous_returns.map(r => `<a href="/sales/returns/${r.id}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">${r.number}</a>`).join(', ')}</span>` : ''}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            <input type="checkbox" 
                                   id="item-check-${index}" 
                                   class="item-checkbox w-5 h-5 text-primary-600 rounded" 
                                   data-index="${index}">
                            <input type="number" 
                                   id="item-qty-${index}" 
                                   class="item-quantity w-24 px-3 py-2 border-2 border-gray-200 dark:border-dark-border rounded-lg text-center" 
                                   min="1" 
                                   max="${item.available_quantity}" 
                                   value="1" 
                                   disabled 
                                   data-index="${index}" 
                                   data-price="${item.unit_price}">
                            <input type="hidden" name="items[${index}][invoice_item_id]" value="${item.id}" disabled id="hidden-${index}">
                            <input type="hidden" name="items[${index}][quantity]" value="1" disabled id="hidden-qty-${index}">
                        </div>
                    </div>
                `;
                itemsContainer.appendChild(itemDiv);
            });

            // Add event listeners
            document.querySelectorAll('.item-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', handleCheckboxChange);
            });

            document.querySelectorAll('.item-quantity').forEach(input => {
                input.addEventListener('input', function() {
                    const max = parseInt(this.max);
                    const value = parseInt(this.value);
                    
                    if (value > max) {
                        this.value = max;
                    }
                    if (value < 1) {
                        this.value = 1;
                    }
                    
                    calculateTotal();
                });
            });
        }

        function handleCheckboxChange(e) {
            const index = e.target.dataset.index;
            const qtyInput = document.getElementById(`item-qty-${index}`);
            const hiddenInput = document.getElementById(`hidden-${index}`);
            const hiddenQty = document.getElementById(`hidden-qty-${index}`);
            
            if (e.target.checked) {
                qtyInput.disabled = false;
                hiddenInput.disabled = false;
                hiddenQty.disabled = false;
            } else {
                qtyInput.disabled = true;
                hiddenInput.disabled = true;
                hiddenQty.disabled = true;
            }
            
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            let hasSelected = false;
            
            document.querySelectorAll('.item-checkbox:checked').forEach(checkbox => {
                hasSelected = true;
                const index = checkbox.dataset.index;
                const qtyInput = document.getElementById(`item-qty-${index}`);
                const hiddenQty = document.getElementById(`hidden-qty-${index}`);
                const quantity = parseInt(qtyInput.value) || 0;
                const price = parseFloat(qtyInput.dataset.price) || 0;
                
                hiddenQty.value = quantity;
                total += quantity * price;
            });
            
            totalAmountSpan.textContent = total.toLocaleString() + ' دينار';
            submitBtn.disabled = !hasSelected;
        }
    });
</script>
@endpush
@endsection
