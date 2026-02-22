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
const allInvoices = {!! json_encode($approvedInvoices->map(function($invoice) use ($approvedInvoices) {
    return [
        'id' => $invoice->id,
        'number' => $invoice->invoice_number,
        'store' => $invoice->store->name,
        'store_active' => $invoice->store->is_active,
        'amount' => $invoice->total_amount,
        'items' => $invoice->items->map(function($item) use ($invoice) {
            $alreadyReturned = \App\Models\SalesReturnItem::whereHas('salesReturn', function ($q) use ($invoice) {
                $q->where('sales_invoice_id', $invoice->id)
                  ->whereIn('status', ['pending', 'approved']);
            })->where('sales_invoice_item_id', $item->id)
              ->sum('quantity');
            
            $previousReturns = \App\Models\SalesReturnItem::whereHas('salesReturn', function ($q) use ($invoice) {
                $q->where('sales_invoice_id', $invoice->id)
                  ->whereIn('status', ['pending', 'approved']);
            })->where('sales_invoice_item_id', $item->id)
              ->with('salesReturn:id,return_number')
              ->get()
              ->pluck('salesReturn')
              ->unique('id')
              ->map(function($return) {
                  return [
                      'id' => $return->id,
                      'number' => $return->return_number
                  ];
              })->values();
            
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'free_quantity' => $item->free_quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->quantity + $item->free_quantity,
                'already_returned' => $alreadyReturned,
                'available' => ($item->quantity + $item->free_quantity) - $alreadyReturned,
                'previous_returns' => $previousReturns
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
                <div class="bg-white dark:bg-dark-card rounded-2xl p-4 md:p-6 border-2 ${item.available === 0 ? 'border-red-200 dark:border-red-900/30 opacity-60' : 'border-gray-200 dark:border-dark-border'} shadow-sm">
                    <div class="flex flex-col gap-4">
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <label class="block text-lg font-bold text-gray-900 dark:text-white mb-2">${item.product_name}</label>
                                    <input type="hidden" name="items[${index}][sales_invoice_item_id]" value="${item.id}">
                                    <div class="flex flex-wrap gap-2 text-sm">
                                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 dark:bg-dark-bg rounded-lg">
                                            <i data-lucide="package" class="w-4 h-4 text-gray-500 dark:text-gray-400"></i>
                                            <span class="text-gray-700 dark:text-gray-300 font-medium">الأصلية: <span class="font-bold">${item.total}</span></span>
                                        </div>
                                        ${item.already_returned > 0 ? `
                                        <div class="flex items-center gap-1.5 px-3 py-1.5 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                            <i data-lucide="corner-up-left" class="w-4 h-4 text-red-500 dark:text-red-400"></i>
                                            <span class="text-red-700 dark:text-red-300 font-medium">مرجع: <span class="font-bold">${item.already_returned}</span></span>
                                        </div>` : ''}
                                        <div class="flex items-center gap-1.5 px-3 py-1.5 ${item.available > 0 ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-red-50 dark:bg-red-900/20'} rounded-lg">
                                            <i data-lucide="check-circle" class="w-4 h-4 ${item.available > 0 ? 'text-emerald-500 dark:text-emerald-400' : 'text-red-500 dark:text-red-400'}"></i>
                                            <span class="${item.available > 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-red-700 dark:text-red-300'} font-medium">متاح: <span class="font-bold">${item.available}</span></span>
                                        </div>
                                    </div>
                                    ${item.previous_returns && item.previous_returns.length > 0 ? `
                                    <div class="mt-2 flex items-center gap-1.5 text-sm">
                                        <i data-lucide="history" class="w-4 h-4 text-amber-500 dark:text-amber-400"></i>
                                        <span class="text-amber-700 dark:text-amber-300 font-medium">مرتجعات سابقة:</span>
                                        <div class="flex flex-wrap gap-1.5">
                                            ${item.previous_returns.map(r => `<span class="px-2.5 py-1 bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-200 rounded font-mono text-xs">#${r.number}</span>`).join('')}
                                        </div>
                                    </div>` : ''}
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row gap-3">
                            <div class="flex gap-3 flex-1">
                                <div class="flex-1">
                                    <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-2">الكمية المرجعة</label>
                                    <input type="number" name="items[${index}][quantity]" class="return-quantity w-full bg-gray-50 dark:bg-dark-bg border-2 border-gray-200 dark:border-dark-border rounded-xl px-4 py-3.5 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all text-base font-bold" min="0" max="${item.available}" data-price="${item.unit_price}" data-max="${item.available}" value="0" ${item.available === 0 ? 'disabled' : ''}>
                                </div>
                                <div class="w-28">
                                    <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-2">السعر</label>
                                    <input type="text" class="w-full bg-gray-100 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-3 py-3.5 text-gray-900 dark:text-white text-base font-bold text-center" value="${item.unit_price}" readonly>
                                </div>
                            </div>
                            <div class="w-full md:w-32">
                                <label class="block text-sm font-bold text-gray-600 dark:text-gray-400 mb-2">الإجمالي</label>
                                <input type="text" class="item-total w-full bg-primary-50 dark:bg-primary-900/20 border-2 border-primary-200 dark:border-primary-600/30 rounded-xl px-3 py-3.5 text-primary-700 dark:text-primary-400 text-base font-bold text-center" value="0.00" readonly>
                            </div>
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
                let value = parseInt(this.value || 0);
                if (value > max) {
                    this.value = max;
                    value = max;
                }
                if (value < 0) {
                    this.value = 0;
                }
                calculateSummary();
            });
        });
        
        lucide.createIcons();
        
        lucide.createIcons();
    }
    
    function calculateSummary() {
        let totalItems = 0;
        let totalAmount = 0;
        
        document.querySelectorAll('.return-quantity').forEach(input => {
            const quantity = parseInt(input.value || 0);
            const price = parseFloat(input.dataset.price || 0);
            const itemTotal = quantity * price;
            
            const row = input.closest('.bg-white');
            if (row) {
                const totalInput = row.querySelector('.item-total');
                if (totalInput) {
                    totalInput.value = itemTotal.toFixed(2);
                }
            }
            
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
        let hasItems = false;
        
        document.querySelectorAll('.return-quantity').forEach(input => {
            const quantity = parseInt(input.value || 0);
            if (quantity > 0) {
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
