@extends('layouts.app')

@section('title', 'تفاصيل الفاتورة')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-5xl mx-auto px-4">
        
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('sales.invoices.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                        <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    </a>
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        فاتورة #{{ $invoice->invoice_number }}
                    </span>
                </div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white">تفاصيل الفاتورة</h1>
            </div>
        </div>

        {{-- Invoice Info --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border">
                <p class="text-xs text-gray-500 dark:text-dark-muted mb-1">العميل</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $invoice->customer->name }}</p>
                <p class="text-sm text-gray-500 dark:text-dark-muted">{{ $invoice->customer->phone }}</p>
            </div>

            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border">
                <p class="text-xs text-gray-500 dark:text-dark-muted mb-1">المبلغ الإجمالي</p>
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ number_format($invoice->total_amount, 0) }} دينار</p>
            </div>

            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border">
                <p class="text-xs text-gray-500 dark:text-dark-muted mb-1">نوع الدفع</p>
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-lg text-sm font-bold 
                    {{ $invoice->payment_type === 'cash' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                    {{ $invoice->payment_type === 'credit' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                    {{ $invoice->payment_type === 'partial' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}">
                    {{ $invoice->payment_type === 'cash' ? 'نقدي' : ($invoice->payment_type === 'credit' ? 'آجل' : 'جزئي') }}
                </span>
            </div>
        </div>

        {{-- Items --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border mb-8">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">المنتجات</h3>
            
            @foreach($invoice->items as $item)
                <div class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-dark-border last:border-0">
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-dark-muted">{{ $item->quantity }} × {{ number_format($item->unit_price, 0) }} دينار</p>
                    </div>
                    <p class="font-bold text-gray-900 dark:text-white">{{ number_format($item->total_price, 0) }} دينار</p>
                </div>
            @endforeach

            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">المجموع الفرعي:</span>
                    <span class="font-bold text-gray-900 dark:text-white">{{ number_format($invoice->subtotal, 0) }} دينار</span>
                </div>
                @if($invoice->discount_amount > 0)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">الخصم:</span>
                        <span class="font-bold text-red-600">{{ number_format($invoice->discount_amount, 0) }} دينار</span>
                    </div>
                @endif
                <div class="flex justify-between text-lg pt-2 border-t border-gray-200 dark:border-dark-border">
                    <span class="font-black text-gray-900 dark:text-white">الإجمالي:</span>
                    <span class="font-black text-primary-600 dark:text-primary-400">{{ number_format($invoice->total_amount, 0) }} دينار</span>
                </div>
            </div>
        </div>

        @if($invoice->notes)
            <div class="bg-white dark:bg-dark-card rounded-2xl p-6 shadow-lg border border-gray-200 dark:border-dark-border">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">ملاحظات</h3>
                <p class="text-gray-600 dark:text-gray-400">{{ $invoice->notes }}</p>
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
