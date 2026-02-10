@extends('layouts.app')

@section('title', 'طلب إرجاع جديد')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto space-y-8 px-3 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in-down">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        طلب جديد
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    إنشاء طلب إرجاع
                </h1>
            </div>

            <div class="flex gap-3 w-full md:w-auto">
                <a href="{{ route('marketer.sales-returns.index') }}" class="px-12 py-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center justify-center gap-2 flex-1 md:flex-auto">
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    عودة
                </a>
            </div>
        </div>

        <form action="{{ route('marketer.sales-returns.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-dark-card rounded-[2rem] p-3 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border relative overflow-hidden animate-slide-up">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h2 class="font-bold text-xl text-gray-900 dark:text-white flex items-center gap-3">
                                    <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                                        <i data-lucide="file-text" class="w-5 h-5"></i>
                                    </span>
                                    اختيار الفاتورة
                                </h2>
                                <p class="text-sm text-gray-500 dark:text-dark-muted mt-2 mr-14 font-medium">اختر الفاتورة المراد إرجاع بضاعتها</p>
                            </div>
                        </div>

                        <select name="sales_invoice_id" id="invoice-select" class="w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm" required>
                            <option value="">اختر الفاتورة</option>
                            @foreach($approvedInvoices as $invoice)
                                <option value="{{ $invoice->id }}" 
                                        data-store="{{ $invoice->store->name }}"
                                        data-items="{{ json_encode($invoice->items->map(fn($item) => [
                                            'id' => $item->id,
                                            'product_id' => $item->product_id,
                                            'product_name' => $item->product->name,
                                            'quantity' => $item->quantity,
                                            'free_quantity' => $item->free_quantity,
                                            'unit_price' => $item->unit_price,
                                            'total' => $item->quantity + $item->free_quantity
                                        ])) }}">
                                    #{{ $invoice->invoice_number }} - {{ $invoice->store->name }} - {{ number_format($invoice->total_amount, 2) }} دينار
                                </option>
                            @endforeach
                        </select>

                        <div id="items-container" class="mt-6 space-y-4 hidden">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-4">المنتجات المتاحة للإرجاع</h3>
                        </div>

                        <div id="summary-container" class="mt-6 bg-gradient-to-br from-primary-50 to-primary-100/50 dark:from-dark-bg dark:to-dark-border rounded-2xl p-6 border-2 border-primary-200 dark:border-dark-border hidden">
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                                <i data-lucide="calculator" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                                ملخص الإرجاع
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center py-2 border-b border-primary-200 dark:border-dark-border">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">عدد المنتجات</span>
                                    <span id="total-items" class="text-gray-900 dark:text-white font-bold">0</span>
                                </div>
                                <div class="flex justify-between items-center py-3 bg-primary-600 dark:bg-primary-700 rounded-xl px-4 mt-2">
                                    <span class="text-white font-bold text-lg">المبلغ الإجمالي</span>
                                    <span id="total-amount" class="text-white font-black text-xl">0.00 دينار</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-dark-card rounded-[2rem] p-3 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
                        <div class="flex items-center gap-3 mb-6">
                            <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                                <i data-lucide="sticky-note" class="w-5 h-5"></i>
                            </span>
                            <h3 class="font-bold text-xl text-gray-900 dark:text-white">ملاحظات</h3>
                        </div>
                        <textarea name="notes" rows="4" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all" placeholder="سبب الإرجاع..."></textarea>
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
                                إنشاء طلب الإرجاع
                            </button>
                            <a href="{{ route('marketer.sales-returns.index') }}" class="w-full px-8 py-4 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center justify-center gap-2">
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
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    const invoiceSelect = document.getElementById('invoice-select');
    const itemsContainer = document.getElementById('items-container');
    const summaryContainer = document.getElementById('summary-container');
    
    invoiceSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (!this.value) {
            itemsContainer.classList.add('hidden');
            summaryContainer.classList.add('hidden');
            return;
        }
        
        const items = JSON.parse(selectedOption.dataset.items || '[]');
        
        itemsContainer.innerHTML = '<h3 class="font-bold text-lg text-gray-900 dark:text-white mb-4">المنتجات المتاحة للإرجاع</h3>';
        
        items.forEach((item, index) => {
            const itemHtml = `
                <div class="bg-gray-50/50 dark:bg-dark-bg/60 rounded-2xl p-4 md:p-6 border border-gray-100 dark:border-dark-border">
                    <div class="grid grid-cols-12 gap-3">
                        <div class="col-span-12 md:col-span-5">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">${item.product_name}</label>
                            <input type="hidden" name="items[${index}][sales_invoice_item_id]" value="${item.id}">
                            <div class="text-xs text-gray-500 dark:text-gray-400">الكمية المتاحة: ${item.total}</div>
                        </div>
                        <div class="col-span-6 md:col-span-3">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الكمية المرجعة</label>
                            <input type="number" name="items[${index}][quantity]" class="return-quantity w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm" min="1" max="${item.total}" data-price="${item.unit_price}" data-max="${item.total}" placeholder="0" required>
                        </div>
                        <div class="col-span-6 md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">السعر</label>
                            <input type="text" class="w-full bg-gray-100 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white text-sm font-bold text-center" value="${item.unit_price}" readonly>
                        </div>
                        <div class="col-span-6 md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">الإجمالي</label>
                            <input type="text" class="item-total w-full bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-xl px-4 py-3 text-primary-700 dark:text-primary-400 text-sm font-bold text-center" value="0" readonly>
                        </div>
                    </div>
                </div>
            `;
            itemsContainer.insertAdjacentHTML('beforeend', itemHtml);
        });
        
        itemsContainer.classList.remove('hidden');
        summaryContainer.classList.remove('hidden');
        
        document.querySelectorAll('.return-quantity').forEach(input => {
            input.addEventListener('input', function() {
                const max = parseInt(this.dataset.max || this.max);
                const value = parseInt(this.value || 0);
                if (value > max) {
                    this.value = max;
                }
                calculateSummary();
            });
        });
        
        lucide.createIcons();
    });
    
    function calculateSummary() {
        let totalItems = 0;
        let totalAmount = 0;
        
        document.querySelectorAll('.return-quantity').forEach(input => {
            const quantity = parseInt(input.value || 0);
            const price = parseFloat(input.dataset.price || 0);
            const itemTotal = quantity * price;
            
            const row = input.closest('.bg-gray-50\\/50');
            row.querySelector('.item-total').value = itemTotal.toFixed(2);
            
            if (quantity > 0) {
                totalItems += quantity;
                totalAmount += itemTotal;
            }
        });
        
        document.getElementById('total-items').textContent = totalItems;
        document.getElementById('total-amount').textContent = totalAmount.toFixed(2) + ' دينار';
    }
});
</script>
@endpush
