@extends('layouts.app')

@section('title', 'إيصال قبض جديد')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto space-y-8 px-3 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in-down">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إيصال جديد
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    إنشاء إيصال قبض
                </h1>
            </div>

            <a href="{{ route('marketer.payments.index') }}" class="px-12 py-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center gap-2">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                عودة
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2">
                <form action="{{ route('marketer.payments.store') }}" method="POST" id="payment-form">
                    @csrf
                    
                    <div class="bg-white dark:bg-dark-card rounded-[2rem] p-6 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up space-y-6">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">المتجر</label>
                            <div class="relative">
                                <input type="text" id="store-search" autocomplete="off" placeholder="ابحث عن المتجر..." class="w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all" required>
                                <input type="hidden" name="store_id" id="store-id" required>
                                <div id="store-dropdown" class="hidden absolute z-50 w-full mt-2 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl shadow-xl max-h-60 overflow-y-auto"></div>
                            </div>
                            <div id="debt-display" class="hidden mt-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-amber-700 dark:text-amber-400">الدين الحالي:</span>
                                    <span id="debt-amount" class="text-lg font-black text-amber-700 dark:text-amber-400">0.00 د.ل</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">المبلغ المسدد</label>
                            <input type="number" name="amount" id="amount-input" step="0.01" min="0.01" class="w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all" placeholder="0.00" required>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">طريقة الدفع</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="payment_method" value="cash" class="peer sr-only" required>
                                    <div class="bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-dark-border rounded-xl p-4 text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 hover:border-gray-300 dark:hover:border-gray-600">
                                        <i data-lucide="banknote" class="w-8 h-8 mx-auto mb-2 text-gray-400 peer-checked:text-emerald-600"></i>
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300 peer-checked:text-emerald-600">نقدي</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="payment_method" value="transfer" class="peer sr-only">
                                    <div class="bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-dark-border rounded-xl p-4 text-center transition-all peer-checked:border-blue-500 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 hover:border-gray-300 dark:hover:border-gray-600">
                                        <i data-lucide="arrow-right-left" class="w-8 h-8 mx-auto mb-2 text-gray-400 peer-checked:text-blue-600"></i>
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300 peer-checked:text-blue-600">حوالة</span>
                                    </div>
                                </label>
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="payment_method" value="certified_check" class="peer sr-only">
                                    <div class="bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-dark-border rounded-xl p-4 text-center transition-all peer-checked:border-purple-500 peer-checked:bg-purple-50 dark:peer-checked:bg-purple-900/20 hover:border-gray-300 dark:hover:border-gray-600">
                                        <i data-lucide="file-check" class="w-8 h-8 mx-auto mb-2 text-gray-400 peer-checked:text-purple-600"></i>
                                        <span class="text-sm font-bold text-gray-700 dark:text-gray-300 peer-checked:text-purple-600">شيك</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">ملاحظات</label>
                            <textarea name="notes" rows="3" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all" placeholder="أضف أي ملاحظات إضافية..."></textarea>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="submit" class="flex-1 px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all shadow-lg shadow-primary-200/50 dark:shadow-none flex items-center justify-center gap-2">
                                <i data-lucide="check" class="w-5 h-5"></i>
                                إنشاء الإيصال
                            </button>
                            <a href="{{ route('marketer.payments.index') }}" class="px-8 py-4 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center justify-center gap-2">
                                <i data-lucide="x" class="w-5 h-5"></i>
                                إلغاء
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-1">
                @include('shared.payments._timeline-guide')
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const stores = [
    @foreach($stores as $store)
        { id: {{ $store->id }}, name: '{{ addslashes($store->name) }}', owner: '{{ addslashes($store->owner_name) }}', debt: {{ $store->debt }} },
    @endforeach
];

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    const form = document.getElementById('payment-form');
    const searchInput = document.getElementById('store-search');
    const storeIdInput = document.getElementById('store-id');
    const dropdown = document.getElementById('store-dropdown');
    const debtDisplay = document.getElementById('debt-display');
    const debtAmount = document.getElementById('debt-amount');
    const amountInput = document.getElementById('amount-input');
    
    // Prevent form submission if store not selected
    form.addEventListener('submit', function(e) {
        if (!storeIdInput.value) {
            e.preventDefault();
            alert('الرجاء اختيار المتجر');
            return false;
        }
        
        const paymentMethod = form.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            e.preventDefault();
            alert('الرجاء اختيار طريقة الدفع');
            return false;
        }
    });
    
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        storeIdInput.value = '';
        debtDisplay.classList.add('hidden');
        
        if (query.length === 0) {
            dropdown.classList.add('hidden');
            return;
        }
        
        const filtered = stores.filter(store => 
            store.name.toLowerCase().includes(query) || 
            store.owner.toLowerCase().includes(query)
        );
        
        if (filtered.length === 0) {
            dropdown.innerHTML = '<div class="px-4 py-3 text-gray-500 dark:text-gray-400 text-sm">لا توجد نتائج</div>';
            dropdown.classList.remove('hidden');
            return;
        }
        
        dropdown.innerHTML = filtered.map(store => `
            <div class="store-option px-4 py-3 hover:bg-gray-100 dark:hover:bg-dark-bg cursor-pointer border-b border-gray-100 dark:border-dark-border last:border-0" data-id="${store.id}" data-name="${store.name} - ${store.owner}" data-debt="${store.debt}">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="font-bold text-gray-900 dark:text-white text-sm">${store.name}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">${store.owner}</div>
                    </div>
                    <div class="text-amber-600 dark:text-amber-400 font-bold text-sm">${store.debt.toFixed(2)} د.ل</div>
                </div>
            </div>
        `).join('');
        dropdown.classList.remove('hidden');
        
        document.querySelectorAll('.store-option').forEach(option => {
            option.addEventListener('click', function() {
                const debt = parseFloat(this.dataset.debt);
                storeIdInput.value = this.dataset.id;
                searchInput.value = this.dataset.name;
                dropdown.classList.add('hidden');
                
                if (debt > 0) {
                    debtDisplay.classList.remove('hidden');
                    debtAmount.textContent = debt.toFixed(2) + ' د.ل';

                } else {
                    debtDisplay.classList.add('hidden');

                }
            });
        });
    });
    
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
    
    amountInput.addEventListener('input', function() {
    });
});
</script>
@endpush
@endsection
