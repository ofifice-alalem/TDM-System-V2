@extends('layouts.app')

@section('title', 'تسجيل دفعة جديدة')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-3xl mx-auto px-4">
        
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('sales.payments.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                </a>
                <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                    دفعة جديدة
                </span>
            </div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white">تسجيل دفعة جديدة</h1>
        </div>

        {{-- Form --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
            <form action="{{ route('sales.payments.store') }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">العميل *</label>
                        <select name="customer_id" id="customer_id" required onchange="loadCustomerDebt()" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">اختر العميل</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                            @endforeach
                        </select>
                        <p id="debt-text" class="text-base font-bold text-red-600 dark:text-red-400 mt-3 bg-red-50 dark:bg-red-900/20 px-4 py-2 rounded-lg border border-red-200 dark:border-red-800" style="display: none;">
                            <i data-lucide="alert-circle" class="w-4 h-4 inline-block ml-1"></i>
                            إجمالي دين العميل: <span id="debt-amount" class="text-lg"></span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">المبلغ المدفوع *</label>
                        <input type="number" name="amount" id="amount" required min="0" step="0.01" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <p id="amount-error" class="text-xs text-red-500 mt-1" style="display: none;">المبلغ أكبر من الدين!</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">طريقة الدفع *</label>
                        <select name="payment_method" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="cash">نقدي</option>
                            <option value="transfer">تحويل</option>
                            <option value="check">شيك</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">ملاحظات</label>
                        <textarea name="notes" rows="3" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
                    </div>

                    <div id="remaining-debt" class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 border border-green-200 dark:border-green-800" style="display: none;">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-bold text-gray-700 dark:text-gray-300">باقي الدين بعد التسديد:</p>
                            <p id="remaining-amount" class="text-2xl font-bold text-green-600"></p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-200 dark:border-dark-border">
                    <a href="{{ route('sales.payments.index') }}" class="px-6 py-3 bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-all">
                        إلغاء
                    </a>
                    <button type="submit" id="submit-btn" class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        حفظ الدفعة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentDebt = 0;

    async function loadCustomerDebt() {
        const customerId = document.getElementById('customer_id').value;
        if (!customerId) {
            document.getElementById('debt-text').style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`/sales/payments/customer/${customerId}/debt`);
            if (!response.ok) {
                console.error('Response not OK:', response.status);
                return;
            }
            const data = await response.json();
            console.log('Debt data:', data);
            currentDebt = parseFloat(data.debt) || 0;
            
            document.getElementById('debt-amount').textContent = currentDebt.toFixed(0) + ' دينار';
            document.getElementById('debt-text').style.display = 'block';
            lucide.createIcons();
            document.getElementById('amount').max = currentDebt;
        } catch (error) {
            console.error('Error loading debt:', error);
        }
    }

    document.getElementById('amount').addEventListener('input', function() {
        const amount = parseFloat(this.value) || 0;
        const error = document.getElementById('amount-error');
        const remainingDiv = document.getElementById('remaining-debt');
        const remainingAmount = document.getElementById('remaining-amount');
        
        if (amount > currentDebt) {
            error.style.display = 'block';
            this.value = currentDebt;
        } else {
            error.style.display = 'none';
        }
        
        // Calculate and show remaining debt
        if (amount > 0 && currentDebt > 0) {
            const remaining = currentDebt - amount;
            remainingAmount.textContent = remaining.toFixed(0) + ' دينار';
            remainingDiv.style.display = 'block';
        } else {
            remainingDiv.style.display = 'none';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
@endsection
