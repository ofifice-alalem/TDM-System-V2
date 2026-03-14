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
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-green-100 dark:bg-green-600/20 text-green-600 dark:text-green-400 px-3 py-1 rounded-lg text-xs font-bold border border-green-100 dark:border-green-600/30 cursor-pointer hover:bg-green-200 dark:hover:bg-green-600/30 transition-colors flex items-center gap-2 group" onclick="copyPaymentNumber('{{ $payment->payment_number }}')" title="انقر للنسخ">
                            إيصال #{{ $payment->payment_number }}
                            <i data-lucide="copy" class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-black text-gray-900 dark:text-white">تفاصيل الدفعة</h1>
                </div>
                <button onclick="window.location.href='{{ route('sales.payments.pdf', $payment) }}'" class="w-full sm:w-auto px-6 py-3 bg-gray-100 dark:bg-dark-bg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                    <i data-lucide="printer" class="w-5 h-5"></i>
                    طباعة
                </button>
            </div>
        </div>

        {{-- Payment Receipt Card --}}
        <div class="bg-white dark:bg-dark-card rounded-3xl shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border overflow-hidden animate-slide-up">
            
            {{-- Header Section --}}
            <div class="bg-white dark:bg-dark-card p-4 sm:p-8 border-b border-gray-200 dark:border-dark-border">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-start sm:items-center gap-3 sm:gap-4 w-full sm:w-auto">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 dark:bg-green-500/10 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="banknote" class="w-5 h-5 sm:w-7 sm:h-7 text-green-600 dark:text-green-400"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                                <div>
                                    <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">إيصال قبض</p>
                                    <div class="flex items-center gap-2 group">
                                        <h2 class="text-lg sm:text-2xl font-black text-gray-900 dark:text-white cursor-pointer hover:text-primary-600 dark:hover:text-primary-400 transition-colors" onclick="copyPaymentNumber('{{ $payment->payment_number }}')" title="انقر للنسخ">#{{ $payment->payment_number }}</h2>
                                        <button onclick="copyPaymentNumber('{{ $payment->payment_number }}')" class="opacity-0 group-hover:opacity-100 transition-opacity p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="نسخ الرقم">
                                            <i data-lucide="copy" class="w-4 h-4 text-gray-500 dark:text-gray-400"></i>
                                        </button>
                                    </div>
                                </div>
                                @if($payment->status === 'completed')
                                <span class="px-3 py-1.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-lg text-xs font-bold border border-emerald-200 dark:border-emerald-500/30 flex items-center gap-1.5 w-fit">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                    مكتمل
                                </span>
                                @else
                                <span class="px-3 py-1.5 bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 rounded-lg text-xs font-bold border border-red-200 dark:border-red-500/30 flex items-center gap-1.5 w-fit">
                                    <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                    ملغي
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right w-full sm:w-auto bg-gray-50 dark:bg-dark-bg sm:bg-transparent sm:dark:bg-transparent p-3 sm:p-0 rounded-xl">
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mb-1">المبلغ المدفوع</p>
                        <p class="text-2xl sm:text-3xl font-black text-gray-900 dark:text-white">{{ number_format($payment->amount, 0) }} دينار</p>
                    </div>
                </div>
            </div>

            @if($payment->status === 'cancelled')
            <div class="bg-red-50 dark:bg-red-500/10 border-t-4 border-red-500 dark:border-red-600 p-4 sm:p-6">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i data-lucide="x-octagon" class="w-6 h-6 sm:w-7 sm:h-7 text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-lg sm:text-xl font-black text-red-900 dark:text-red-300">تم إلغاء الدفعة</h3>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-6 w-full sm:w-auto">
                        <div class="flex items-center gap-2 text-sm text-red-700 dark:text-red-400">
                            <i data-lucide="calendar" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                            <span class="font-bold">بتاريخ:</span>
                            <span>{{ $payment->updated_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="hidden sm:block w-px h-6 bg-red-300 dark:bg-red-700"></div>
                        <div class="flex items-center gap-2 text-sm text-red-700 dark:text-red-400">
                            <i data-lucide="clock" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                            <span class="font-bold">الساعة:</span>
                            <span>{{ $payment->updated_at->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
                @if($payment->cancel_notes)
                <div class="mt-4 sm:mt-6 bg-white dark:bg-dark-bg rounded-xl p-3 sm:p-4 border border-red-200 dark:border-red-700/30">
                    <p class="text-xs text-red-600 dark:text-red-400 font-bold mb-2">سبب الإلغاء:</p>
                    <p class="text-sm text-red-800 dark:text-red-300">{{ $payment->cancel_notes }}</p>
                </div>
                @endif
            </div>
            @endif

            {{-- Details Section --}}
            <div class="p-4 sm:p-8">
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

                @if($payment->status === 'completed')
                    <div class="bg-red-50 dark:bg-red-500/10 rounded-2xl p-4 sm:p-5 border border-red-200 dark:border-red-500/30 mt-6">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                            <div class="flex items-start gap-3">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5"></i>
                                <div>
                                    <h3 class="text-sm font-bold text-red-900 dark:text-red-300">إلغاء الدفعة</h3>
                                    <p class="text-xs text-red-700 dark:text-red-400 mt-0.5">سيتم إرجاع المبلغ إلى دين العميل</p>
                                </div>
                            </div>
                            <button type="button" onclick="document.getElementById('cancelForm').classList.toggle('hidden')" class="w-full sm:w-auto px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-lg shadow-red-500/30">
                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                إلغاء الدفعة
                            </button>
                        </div>
                        <form id="cancelForm" action="{{ route('sales.payments.cancel', $payment) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                            <div class="space-y-3">
                                <textarea name="cancel_notes" rows="3" class="w-full px-4 py-3 bg-white dark:bg-dark-bg border border-red-300 dark:border-red-500/30 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="سبب الإلغاء (اختياري)"></textarea>
                                <button type="submit" class="w-full px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                    تأكيد الإلغاء
                                </button>
                            </div>
                        </form>
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

    function copyPaymentNumber(paymentNumber) {
        navigator.clipboard.writeText(paymentNumber).then(function() {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-600 text-white px-6 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2 animate-fade-in-down';
            toast.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
                <span class="font-bold">تم نسخ رقم الإيصال: ${paymentNumber}</span>
            `;
            document.body.appendChild(toast);
            
            // Remove toast after 3 seconds
            setTimeout(function() {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(function() {
                    document.body.removeChild(toast);
                }, 300);
            }, 3000);
        }).catch(function(err) {
            console.error('فشل النسخ:', err);
            alert('فشل نسخ الرقم');
        });
    }
</script>
@endpush
@endsection
