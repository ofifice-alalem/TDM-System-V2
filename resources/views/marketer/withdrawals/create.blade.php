@extends('layouts.app')

@section('title', 'طلب سحب جديد')

@section('content')
<div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-end">
        <a href="{{ route('marketer.withdrawals.index') }}" class="px-12 py-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center gap-2">
            <i data-lucide="arrow-right" class="w-5 h-5"></i>
            عودة
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-dark-card rounded-2xl p-8 shadow-lg border border-gray-200 dark:border-dark-border">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-100 to-amber-200 dark:from-amber-900/30 dark:to-amber-800/30 rounded-2xl flex items-center justify-center">
                        <i data-lucide="wallet" class="w-7 h-7 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-gray-900 dark:text-white">طلب سحب جديد</h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400">اسحب أرباحك المتاحة</p>
                    </div>
                </div>

                <div class="bg-emerald-50 dark:bg-emerald-900/20 rounded-xl p-4 mb-6 border border-emerald-200 dark:border-emerald-800/30">
                    <div class="flex items-center gap-3">
                        <i data-lucide="info" class="w-5 h-5 text-emerald-600 dark:text-emerald-400"></i>
                        <div>
                            <p class="font-bold text-emerald-900 dark:text-emerald-300 text-sm">الرصيد المتاح للسحب</p>
                            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ number_format($availableBalance, 2) }} دينار</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('marketer.withdrawals.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">المبلغ المطلوب *</label>
                        <input type="number" step="0.01" name="requested_amount" value="{{ old('requested_amount') }}" min="0.01" max="{{ $availableBalance }}" required class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-500 focus:border-transparent transition-all text-gray-900 dark:text-white">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">الحد الأقصى: {{ number_format($availableBalance, 2) }} دينار</p>
                        @error('requested_amount')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">ملاحظات</label>
                        <textarea name="notes" rows="3" class="w-full px-4 py-3 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl focus:ring-2 focus:ring-amber-500 dark:focus:ring-amber-500 focus:border-transparent transition-all text-gray-900 dark:text-white">{{ old('notes') }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-4 bg-gradient-to-r from-amber-500 to-amber-600 dark:from-amber-500 dark:to-amber-600 text-white rounded-xl font-bold hover:shadow-xl hover:scale-[1.02] transition-all flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-5 h-5"></i>
                        إنشاء طلب السحب
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1">
            @include('shared.withdrawals._timeline-guide')
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.querySelector('input[name="requested_amount"]');
        const maxAmount = {{ $availableBalance }};
        
        amountInput.addEventListener('input', function() {
            if (parseFloat(this.value) > maxAmount) {
                this.value = maxAmount;
            }
        });
    });
</script>
@endpush
@endsection
