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

                        <div class="flex gap-2">
                            <input type="text" id="invoice-search" placeholder="ادخل رقم الفاتورة..." class="flex-1 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm" autocomplete="off">
                            <button type="button" id="search-btn" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                                <i data-lucide="search" class="w-5 h-5"></i>
                                بحث
                            </button>
                        </div>
                        <input type="hidden" name="sales_invoice_id" id="selected-invoice-id" required>
                        
                        <div id="search-results" class="hidden mt-4 space-y-3"></div>

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
const allInvoices = {!! json_encode($approvedInvoices->map(function($invoice) {
    return [
        'id' => $invoice->id,
        'number' => $invoice->invoice_number,
        'store' => $invoice->store->name,
        'store_active' => $invoice->store->is_active,
        'amount' => $invoice->total_amount,
        'items' => $invoice->items->map(function($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'free_quantity' => $item->free_quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->quantity + $item->free_quantity
            ];
        })->toArray()
    ];
})->toArray()) !!};

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    const invoiceSearch = document.getElementById('invoice-search');
    const searchBtn = document.getElementById('search-btn');
    const searchResults = document.getElementById('search-results');
    const selectedInvoiceId = document.getElementById('selected-invoice-id');
    const itemsContainer = document.getElementById('items-container');
    const summaryContainer = document.getElementById('summary-container');
    
    // Search functionality
    searchBtn.addEventListener('click', performSearch);
    invoiceSearch.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });
    
    function performSearch() {
        const searchTerm = invoiceSearch.value.toLowerCase().trim();
        
        if (searchTerm === '') {
            alert('الرجاء إدخال رقم الفاتورة');
            return;
        }
        
        const results = allInvoices.filter(invoice => 
            invoice.number.toLowerCase().includes(searchTerm)
        );
        
        displayResults(results);
    }
    
    function displayResults(results) {
        searchResults.innerHTML = '';
        
        if (results.length === 0) {
            searchResults.innerHTML = '<div class="text-center py-8 text-gray-500 dark:text-gray-400">لم يتم العثور على فواتير</div>';
            searchResults.classList.remove('hidden');
            return;
        }
        
        results.forEach(invoice => {
            const resultHtml = `
                <div class="invoice-result bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-dark-border rounded-xl p-4 hover:border-primary-500 dark:hover:border-primary-500 cursor-pointer transition-all"
                     data-invoice='${JSON.stringify(invoice)}'>
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="font-black text-lg text-gray-900 dark:text-white">#${invoice.number}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 flex items-center gap-2">
                                <i data-lucide="store" class="w-4 h-4"></i>
                                <span>${invoice.store}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-xl font-bold text-primary-600 dark:text-primary-400">${parseFloat(invoice.amount).toFixed(2)}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">دينار</div>
                        </div>
                    </div>
                </div>
            `;
            searchResults.insertAdjacentHTML('beforeend', resultHtml);
        });
        
        searchResults.classList.remove('hidden');
        lucide.createIcons();
        
        // Add click handlers
        searchResults.querySelectorAll('.invoice-result').forEach(result => {
            result.addEventListener('click', function() {
                const invoice = JSON.parse(this.dataset.invoice);
                selectInvoice(invoice);
            });
        });
    }
    
    function selectInvoice(invoice) {
        // Check if store is inactive
        if (!invoice.store_active) {
            searchResults.innerHTML = `
                <div class="bg-red-50 dark:bg-red-900/20 border-2 border-red-500 dark:border-red-500 rounded-xl p-6">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 bg-red-100 dark:bg-red-500/20 rounded-xl flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                            <i data-lucide="alert-triangle" class="w-6 h-6"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-black text-lg text-red-900 dark:text-red-200 mb-2">تم إيقاف التعامل مع هذا المتجر</div>
                            <div class="text-sm text-red-700 dark:text-red-300 mb-3">لا يمكن إجراء عملية الإرجاع لهذه الفاتورة</div>
                            <div class="bg-white dark:bg-dark-card rounded-lg p-3 border border-red-200 dark:border-red-800">
                                <div class="font-bold text-gray-900 dark:text-white">#${invoice.number}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">${invoice.store}</div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            lucide.createIcons();
            itemsContainer.classList.add('hidden');
            summaryContainer.classList.add('hidden');
            selectedInvoiceId.value = '';
            return;
        }
        
        selectedInvoiceId.value = invoice.id;
        invoiceSearch.value = '#' + invoice.number;
        
        // Keep only selected result
        searchResults.innerHTML = `
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border-2 border-emerald-500 dark:border-emerald-500 rounded-xl p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="font-black text-lg text-gray-900 dark:text-white">#${invoice.number}</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1 flex items-center gap-2">
                            <i data-lucide="store" class="w-4 h-4"></i>
                            <span>${invoice.store}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">${parseFloat(invoice.amount).toFixed(2)}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">دينار</div>
                    </div>
                </div>
            </div>
        `;
        lucide.createIcons();
        
        loadInvoiceItems(invoice.items);
    }
    
    function loadInvoiceItems(items) {
        
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
                            <input type="number" name="items[${index}][quantity]" class="return-quantity w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all text-sm" min="0" max="${item.total}" data-price="${item.unit_price}" data-max="${item.total}" value="0">
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
    }
    
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
    
    // Handle form submission - remove items with quantity 0
    document.querySelector('form').addEventListener('submit', function(e) {
        const quantityInputs = document.querySelectorAll('.return-quantity');
        let hasItems = false;
        
        quantityInputs.forEach(input => {
            const quantity = parseInt(input.value || 0);
            if (quantity === 0) {
                // Remove the entire item container
                const container = input.closest('.bg-gray-50\\/50');
                if (container) {
                    container.remove();
                }
            } else {
                hasItems = true;
            }
        });
        
        if (!hasItems) {
            e.preventDefault();
            alert('يجب تحديد كمية واحدة على الأقل للإرجاع');
            return false;
        }
    });
});
</script>
@endpush
