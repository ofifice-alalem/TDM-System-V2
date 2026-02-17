@extends('layouts.app')

@section('title', 'تفاصيل الدفعة')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-4xl mx-auto px-4">
        
        {{-- Header --}}
        <div class="animate-fade-in-down mb-8">
            <a href="{{ route('sales.payments.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-4 transition-colors">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                <span class="font-bold">العودة للمدفوعات</span>
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-green-100 dark:bg-green-600/20 text-green-600 dark:text-green-400 px-3 py-1 rounded-lg text-xs font-bold border border-green-100 dark:border-green-600/30">
                            إيصال #{{ $payment->payment_number }}
                        </span>
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white">تفاصيل الدفعة</h1>
                </div>
                <button onclick="window.location.href='{{ route('sales.payments.pdf', $payment) }}'" class="px-6 py-3 bg-gray-100 dark:bg-dark-bg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all flex items-center gap-2">
                    <i data-lucide="printer" class="w-5 h-5"></i>
                    طباعة
                </button>
            </div>
        </div>

        {{-- Payment Receipt Card --}}
        <div class="bg-white dark:bg-dark-card rounded-3xl shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border overflow-hidden animate-slide-up">
            
            {{-- Header Section --}}
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <i data-lucide="banknote" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <p class="text-sm opacity-90">إيصال قبض</p>
                                <h2 class="text-2xl font-black">#{{ $payment->payment_number }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="text-left">
                        <p class="text-sm opacity-90 mb-1">المبلغ المدفوع</p>
                        <p class="text-3xl font-black">{{ number_format($payment->amount, 0) }} دينار</p>
                    </div>
                </div>
            </div>

            {{-- Details Section --}}
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-500/10 rounded-xl flex items-center justify-center">
                                <i data-lucide="user" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase">العميل</p>
                        </div>
                        <p class="text-lg font-black text-gray-900 dark:text-white mb-1">{{ $payment->customer->name }}</p>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                            <span>{{ $payment->customer->phone }}</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-500/10 rounded-xl flex items-center justify-center">
                                <i data-lucide="calendar" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase">معلومات الدفع</p>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">التاريخ:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $payment->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">طريقة الدفع:</span>
                                <span class="inline-flex items-center px-3 py-1 bg-blue-100 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 rounded-lg text-xs font-bold">
                                    {{ $payment->payment_method === 'cash' ? 'نقدي' : ($payment->payment_method === 'transfer' ? 'تحويل' : 'شيك') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">الموظف:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $payment->salesUser->full_name ?? 'غير متوفر' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($payment->notes)
                <div class="bg-amber-50 dark:bg-amber-500/10 rounded-2xl p-5 border border-amber-200 dark:border-amber-500/30">
                    <div class="flex items-center gap-2 mb-2">
                        <i data-lucide="message-square" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                        <h3 class="text-sm font-bold text-amber-900 dark:text-amber-300">ملاحظات</h3>
                    </div>
                    <p class="text-sm text-amber-800 dark:text-amber-200">{{ $payment->notes }}</p>
                </div>
                @endif
            </div>

        </div>
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
