@extends('layouts.app')

@section('title', 'تفاصيل الفاتورة')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-5xl mx-auto px-4">
        
        {{-- Header --}}
        <div class="animate-fade-in-down mb-8">
            <a href="{{ route('sales.invoices.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-4 transition-colors">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                <span class="font-bold">العودة للفواتير</span>
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                            فاتورة #{{ $invoice->invoice_number }}
                        </span>
                        @if($invoice->status === 'completed')
                        <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-bold">مكتمل</span>
                        @else
                        <span class="px-3 py-1 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-full text-xs font-bold">ملغي</span>
                        @endif
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white">تفاصيل الفاتورة</h1>
                </div>
                <button onclick="window.print()" class="px-6 py-3 bg-gray-100 dark:bg-dark-bg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all flex items-center gap-2">
                    <i data-lucide="printer" class="w-5 h-5"></i>
                    طباعة
                </button>
            </div>
        </div>

        {{-- Customer & Invoice Info --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 animate-slide-up">
            <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-500/10 rounded-xl flex items-center justify-center">
                        <i data-lucide="user" class="w-6 h-6 text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">معلومات العميل</p>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white">{{ $invoice->customer->name }}</h3>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <i data-lucide="phone" class="w-4 h-4"></i>
                        <span>{{ $invoice->customer->phone }}</span>
                    </div>
                    @if($invoice->customer->address)
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <i data-lucide="map-pin" class="w-4 h-4"></i>
                        <span>{{ $invoice->customer->address }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-primary-100 dark:bg-primary-500/10 rounded-xl flex items-center justify-center">
                        <i data-lucide="file-text" class="w-6 h-6 text-primary-600 dark:text-primary-400"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">معلومات الفاتورة</p>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white">#{{ $invoice->invoice_number }}</h3>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">التاريخ:</span>
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $invoice->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">نوع الدفع:</span>
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold 
                            {{ $invoice->payment_type === 'cash' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                            {{ $invoice->payment_type === 'credit' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                            {{ $invoice->payment_type === 'partial' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}">
                            {{ $invoice->payment_type === 'cash' ? 'نقدي' : ($invoice->payment_type === 'credit' ? 'آجل' : 'جزئي') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="bg-white dark:bg-dark-card rounded-3xl shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border mb-8 overflow-hidden animate-slide-up" style="animation-delay: 0.1s">
            <div class="p-6 border-b border-gray-200 dark:border-dark-border">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center">
                        <i data-lucide="package" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 dark:text-white">المنتجات</h3>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 dark:bg-dark-bg">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">المنتج</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">الكمية</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">سعر الوحدة</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                        @foreach($invoice->items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</div>
                            </td>
                            <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-white">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">{{ number_format($item->unit_price, 0) }} دينار</td>
                            <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-white">{{ number_format($item->total_price, 0) }} دينار</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-gray-50 dark:bg-dark-bg border-t border-gray-200 dark:border-dark-border">
                <div class="max-w-md mr-auto space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">المجموع الفرعي:</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ number_format($invoice->subtotal, 0) }} دينار</span>
                    </div>
                    @if($invoice->discount_amount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">الخصم:</span>
                            <span class="font-bold text-red-600 dark:text-red-400">- {{ number_format($invoice->discount_amount, 0) }} دينار</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-xl pt-3 border-t-2 border-gray-300 dark:border-gray-600">
                        <span class="font-black text-gray-900 dark:text-white">الإجمالي:</span>
                        <span class="font-black text-primary-600 dark:text-primary-400">{{ number_format($invoice->total_amount, 0) }} دينار</span>
                    </div>
                </div>
            </div>
        </div>

        @if($invoice->notes)
            <div class="bg-white dark:bg-dark-card rounded-3xl p-6 shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up" style="animation-delay: 0.2s">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-amber-50 dark:bg-amber-500/10 rounded-xl flex items-center justify-center">
                        <i data-lucide="message-square" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                    </div>
                    <h3 class="text-lg font-black text-gray-900 dark:text-white">ملاحظات</h3>
                </div>
                <p class="text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-dark-bg rounded-xl p-4">{{ $invoice->notes }}</p>
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
