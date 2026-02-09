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
                <form action="{{ route('marketer.payments.store') }}" method="POST">
                    @csrf
                    
                    <div class="bg-white dark:bg-dark-card rounded-[2rem] p-6 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up space-y-6">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">المتجر</label>
                            <select name="store_id" id="store-select" class="w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all" required>
                                <option value="">اختر المتجر</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" data-debt="{{ $store->debt }}">
                                        {{ $store->name }} - الدين: {{ number_format($store->debt, 2) }} د.ل
                                    </option>
                                @endforeach
                            </select>
                            <div id="debt-display" class="hidden mt-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-amber-700 dark:text-amber-400">الدين الحالي:</span>
                                    <span id="debt-amount" class="text-lg font-black text-amber-700 dark:text-amber-400">0.00 د.ل</span>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="keeper_id" value="2">

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
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
    
    const storeSelect = document.getElementById('store-select');
    const debtDisplay = document.getElementById('debt-display');
    const debtAmount = document.getElementById('debt-amount');
    const amountInput = document.getElementById('amount-input');
    
    storeSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const debt = parseFloat(selectedOption.dataset.debt || 0);
        
        if (this.value && debt > 0) {
            debtDisplay.classList.remove('hidden');
            debtAmount.textContent = debt.toFixed(2) + ' د.ل';
            amountInput.max = debt;
        } else {
            debtDisplay.classList.add('hidden');
            amountInput.max = '';
        }
    });
    
    amountInput.addEventListener('input', function() {
        const max = parseFloat(this.max);
        const value = parseFloat(this.value);
        if (max && value > max) {
            this.value = max;
        }
    });
});
</script>
@endpush
@endsection
