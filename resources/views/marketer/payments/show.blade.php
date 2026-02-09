@extends('layouts.app')

@section('title', 'تفاصيل الإيصال #' . $payment->payment_number)

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto space-y-8 px-4 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in-down">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إيصال قبض
                    </span>
                    <span class="text-gray-400 dark:text-dark-muted text-xs font-mono tracking-wider">
                        {{ $payment->created_at->format('Y-m-d h:i A') }}
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    إيصال #{{ $payment->payment_number }}
                </h1>
            </div>

            <a href="{{ route('marketer.payments.index') }}" class="px-12 py-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center gap-2">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                عودة
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-8 space-y-6 animate-slide-up">
                
                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-6 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                            <i data-lucide="store" class="w-5 h-5"></i>
                        </span>
                        <h2 class="font-bold text-xl text-gray-900 dark:text-white">معلومات المتجر</h2>
                    </div>
                    <div class="bg-gray-50/50 dark:bg-dark-bg/60 rounded-2xl p-6 border border-gray-100 dark:border-dark-border">
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-gray-400 dark:text-dark-muted mb-2 font-bold uppercase">اسم المتجر</p>
                                <p class="font-black text-gray-900 dark:text-white text-lg">{{ $payment->store->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 dark:text-dark-muted mb-2 font-bold uppercase">الرقم</p>
                                <p class="font-black text-gray-900 dark:text-white text-lg">{{ $payment->store->phone ?? '---' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-6 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                            <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                        </span>
                        <h2 class="font-bold text-xl text-gray-900 dark:text-white">تفاصيل الدفع</h2>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-dark-border">
                            <span class="text-gray-600 dark:text-gray-400 font-medium">المبلغ المسدد</span>
                            <span class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($payment->amount, 2) }} د.ل</span>
                        </div>
                        <div class="flex justify-between items-center py-3 border-b border-gray-200 dark:border-dark-border">
                            <span class="text-gray-600 dark:text-gray-400 font-medium">طريقة الدفع</span>
                            <span class="font-bold text-gray-900 dark:text-white">
                                @if($payment->payment_method === 'cash') نقدي
                                @elseif($payment->payment_method === 'transfer') تحويل بنكي
                                @else شيك مصدق
                                @endif
                            </span>
                        </div>
                        @if($payment->keeper)
                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-600 dark:text-gray-400 font-medium">أمين المخزن</span>
                            <span class="font-bold text-gray-900 dark:text-white">{{ $payment->keeper->full_name }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($payment->notes)
                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-6 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                            <i data-lucide="sticky-note" class="w-5 h-5"></i>
                        </span>
                        <h3 class="font-bold text-lg text-gray-900 dark:text-white">ملاحظات</h3>
                    </div>
                    <div class="bg-gray-50/50 dark:bg-dark-bg/50 rounded-xl p-4 text-gray-600 dark:text-gray-300 text-sm">
                        {{ $payment->notes }}
                    </div>
                </div>
                @endif
            </div>

            <div class="lg:col-span-4 space-y-6 animate-slide-up" style="animation-delay: 0.1s">
                
                <div class="bg-gray-50 dark:bg-dark-card/50 rounded-[1.5rem] border-2 border-dashed border-gray-200 dark:border-dark-border p-6">
                    <h3 class="text-gray-800 dark:text-gray-200 font-bold text-lg mb-6 flex items-center gap-2">
                        <i data-lucide="activity" class="w-5 h-5 text-gray-400"></i>
                        حالة الإيصال
                    </h3>

                    <div class="text-center py-4">
                        @php
                            $statusConfig = [
                                'pending' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-600 dark:text-amber-400', 'icon' => 'clock', 'label' => 'قيد الانتظار'],
                                'approved' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-600 dark:text-emerald-400', 'icon' => 'check-circle', 'label' => 'موثق'],
                                'rejected' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-600 dark:text-red-400', 'icon' => 'x-circle', 'label' => 'مرفوض'],
                                'cancelled' => ['bg' => 'bg-gray-100 dark:bg-gray-800', 'text' => 'text-gray-500', 'icon' => 'slash', 'label' => 'ملغي'],
                            ][$payment->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'icon' => 'help-circle', 'label' => $payment->status];
                        @endphp
                        
                        <div class="inline-flex items-center justify-center p-4 rounded-full {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} mb-4 shadow-inner ring-4 ring-white dark:ring-dark-card">
                            <i data-lucide="{{ $statusConfig['icon'] }}" class="w-8 h-8"></i>
                        </div>
                        <h2 class="text-2xl font-black {{ $statusConfig['text'] }}">{{ $statusConfig['label'] }}</h2>
                        <p class="text-xs text-gray-400 dark:text-dark-muted mt-2 font-medium mb-4">آخر تحديث: {{ $payment->created_at->diffForHumans() }}</p>

                        @if($payment->status === 'approved' && $payment->receipt_image)
                            <button type="button" onclick="showDocumentationModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-dark-bg border border-emerald-200 dark:border-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-bold hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition-colors shadow-sm mb-2">
                                <i data-lucide="image" class="w-4 h-4"></i>
                                عرض صورة التوثيق
                            </button>
                        @endif
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-dark-border z-10 relative space-y-3">
                        <a href="{{ route('marketer.payments.pdf', $payment) }}" target="_blank" class="w-full bg-gray-900 dark:bg-dark-bg text-white hover:bg-gray-800 dark:hover:bg-dark-card border border-transparent dark:border-dark-border py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-gray-200 dark:shadow-none flex items-center justify-center gap-2 group">
                            <i data-lucide="printer" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                            طباعة PDF
                        </a>

                        @if($payment->status === 'pending')
                            <div x-data="{ showCancel: false }" class="mt-4">
                            <button 
                                type="button" 
                                x-show="!showCancel"
                                @click="showCancel = true"
                                class="w-full bg-white dark:bg-dark-card border-2 border-red-50 dark:border-red-900/30 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-sm">
                                <i data-lucide="x-circle" class="w-5 h-5"></i>
                                إلغاء الإيصال
                            </button>

                            <div 
                                x-show="showCancel" 
                                x-transition
                                class="bg-red-50 dark:bg-red-900/10 rounded-2xl p-4 border border-red-100 dark:border-red-900/30"
                                style="display: none;">
                                
                                <form action="{{ route('marketer.payments.cancel', $payment) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    
                                    <label class="block text-xs font-bold text-red-800 dark:text-red-300 mb-2">سبب الإلغاء:</label>
                                    <textarea 
                                        name="notes" 
                                        rows="2" 
                                        class="w-full bg-white dark:bg-dark-bg border border-red-200 dark:border-red-800 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 mb-3" 
                                        placeholder="اكتب السبب هنا..." 
                                        required></textarea>
                                    
                                    <div class="flex gap-2">
                                        <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                                            تأكيد الإلغاء
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="showCancel = false"
                                            class="px-4 py-2.5 bg-white dark:bg-dark-card border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 font-bold rounded-xl text-sm hover:bg-red-50 transition-colors">
                                            تراجع
                                        </button>
                                    </div>
                                </form>
                            </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Activity Timeline --}}
                <div class="bg-white dark:bg-dark-card rounded-[1.5rem] border border-gray-200 dark:border-dark-border p-6 shadow-lg shadow-gray-200/50 dark:shadow-sm">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <i data-lucide="list" class="w-5 h-5 text-primary-500"></i>
                        سجل العمليات
                    </h3>
                    
                    <div class="relative space-y-6 before:absolute before:inset-0 before:mr-[19px] before:h-full before:w-0.5 before:bg-gradient-to-b before:from-gray-200 dark:before:from-dark-border before:via-gray-100 dark:before:via-dark-bg before:to-transparent">
                        
                        {{-- Step 1: Created --}}
                        <div class="relative flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="plus" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">تم إنشاء الإيصال</h4>
                                <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">بواسطة: {{ $payment->marketer->full_name }}</p>
                                <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $payment->created_at->format('Y-m-d h:i A') }}</span>
                            </div>
                        </div>

                        {{-- Step 2: Approved --}}
                        @if($payment->confirmed_at)
                        <div class="relative flex items-start gap-4 animate-slide-up">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">تم التوثيق</h4>
                                <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">بواسطة: {{ $payment->keeper->full_name }}</p>
                                <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $payment->confirmed_at->format('Y-m-d h:i A') }}</span>
                            </div>
                        </div>
                        @elseif($payment->status == 'rejected')
                        <div class="relative flex items-start gap-4 animate-slide-up">
                            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-red-800 dark:text-red-400 text-sm">تم الرفض</h4>
                                <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">بواسطة: أمين المخزن</p>
                                <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $payment->created_at->format('Y-m-d h:i A') }}</span>
                            </div>
                        </div>
                        @else
                        <div class="relative flex items-start gap-4 opacity-50 grayscale">
                            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-dark-bg text-gray-400 dark:text-gray-600 flex items-center justify-center shrink-0 z-10 border-2 border-white dark:border-dark-card relative overflow-hidden">
                                <div class="absolute inset-0 bg-gray-200/50 dark:bg-gray-800/50 animate-pulse"></div>
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-400 dark:text-gray-600 text-sm">التوثيق</h4>
                                <div class="text-xs text-gray-400 dark:text-gray-600 mt-1">في الانتظار...</div>
                            </div>
                        </div>
                        @endif

                        {{-- Step 3: Cancelled --}}
                        @if($payment->status == 'cancelled')
                        <div class="relative flex items-start gap-4 animate-slide-up">
                            <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="slash" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-600 dark:text-gray-300 text-sm">تم الإلغاء</h4>
                                <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $payment->created_at->format('Y-m-d h:i A') }}</span>
                            </div>
                        </div>
                        @endif
                        
                    </div>
                </div>

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

@if($payment->status === 'approved' && $payment->receipt_image)
    @include('shared.modals.documentation-image', [
        'imageUrl' => asset('storage/' . $payment->receipt_image),
        'invoiceNumber' => $payment->payment_number,
        'documentedAt' => $payment->confirmed_at
    ])
@endif
