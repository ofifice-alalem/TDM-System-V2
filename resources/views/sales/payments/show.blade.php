@extends('layouts.app')

@section('title', 'تفاصيل الدفعة')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-3xl mx-auto px-4">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('sales.payments.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </a>
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إيصال #{{ $payment->payment_number }}
                    </span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white">تفاصيل الدفعة</h1>
            </div>
        </div>

        {{-- Payment Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border">
                <p class="text-xs text-gray-500 dark:text-dark-muted mb-1">العميل</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $payment->customer->name }}</p>
                <p class="text-sm text-gray-500 dark:text-dark-muted">{{ $payment->customer->phone }}</p>
            </div>

            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border">
                <p class="text-xs text-gray-500 dark:text-dark-muted mb-1">المبلغ المدفوع</p>
                <p class="text-2xl font-bold text-green-600">{{ number_format($payment->amount, 0) }} دينار</p>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border mb-8">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-gray-500 dark:text-dark-muted mb-1">طريقة الدفع</p>
                    <p class="text-base font-bold text-gray-900 dark:text-white">
                        {{ $payment->payment_method === 'cash' ? 'نقدي' : ($payment->payment_method === 'transfer' ? 'تحويل' : 'شيك') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 dark:text-dark-muted mb-1">التاريخ</p>
                    <p class="text-base font-bold text-gray-900 dark:text-white">{{ $payment->created_at->format('Y-m-d H:i') }}</p>
                </div>
            </div>
        </div>

        @if($payment->notes)
            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">ملاحظات</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $payment->notes }}</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
@endsection
