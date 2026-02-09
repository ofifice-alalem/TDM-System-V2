@extends('layouts.app')

@section('title', 'تفاصيل الإيصال #' . $payment->payment_number)

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-5xl mx-auto space-y-8 px-4 lg:px-8">
        
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

            <a href="{{ route('warehouse.payments.index') }}" class="px-12 py-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center gap-2">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                عودة
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <div class="lg:col-span-2 space-y-6 animate-slide-up">
                
                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-6 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                            <i data-lucide="users" class="w-5 h-5"></i>
                        </span>
                        <h2 class="font-bold text-xl text-gray-900 dark:text-white">معلومات الأطراف</h2>
                    </div>
                    <div class="bg-gray-50/50 dark:bg-dark-bg/60 rounded-2xl p-6 border border-gray-100 dark:border-dark-border space-y-4">
                        <div>
                            <p class="text-xs text-gray-400 dark:text-dark-muted mb-2 font-bold uppercase">المتجر</p>
                            <p class="font-black text-gray-900 dark:text-white text-lg">{{ $payment->store->name }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 dark:text-dark-muted mb-2 font-bold uppercase">المسوق</p>
                            <p class="font-black text-gray-900 dark:text-white text-lg">{{ $payment->marketer->full_name }}</p>
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
                        <div class="flex justify-between items-center py-3">
                            <span class="text-gray-600 dark:text-gray-400 font-medium">طريقة الدفع</span>
                            <span class="font-bold text-gray-900 dark:text-white">
                                @if($payment->payment_method === 'cash') نقدي
                                @elseif($payment->payment_method === 'transfer') تحويل بنكي
                                @else شيك مصدق
                                @endif
                            </span>
                        </div>
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

            <div class="space-y-6 animate-slide-up" style="animation-delay: 0.1s">
                
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
                        <p class="text-xs text-gray-400 dark:text-dark-muted mt-2 font-medium">آخر تحديث: {{ $payment->created_at->diffForHumans() }}</p>
                    </div>

                    @if($payment->status === 'pending')
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border space-y-3">
                            <form action="{{ route('warehouse.payments.approve', $payment->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2">صورة الإيصال المختوم</label>
                                <input type="file" name="receipt_image" accept="image/*" class="w-full bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 mb-3" required>
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-lg">
                                    <i data-lucide="check-circle" class="w-5 h-5"></i>
                                    توثيق الإيصال
                                </button>
                            </form>

                            <div x-data="{ showReject: false }">
                                <button 
                                    type="button" 
                                    x-show="!showReject"
                                    @click="showReject = true"
                                    class="w-full bg-white dark:bg-dark-card border-2 border-red-50 dark:border-red-900/30 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-sm">
                                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                                    رفض الإيصال
                                </button>

                                <div 
                                    x-show="showReject" 
                                    x-transition
                                    class="bg-red-50 dark:bg-red-900/10 rounded-2xl p-4 border border-red-100 dark:border-red-900/30"
                                    style="display: none;">
                                    
                                    <form action="{{ route('warehouse.payments.reject', $payment->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        
                                        <label class="block text-xs font-bold text-red-800 dark:text-red-300 mb-2">سبب الرفض:</label>
                                        <textarea 
                                            name="notes" 
                                            rows="2" 
                                            class="w-full bg-white dark:bg-dark-bg border border-red-200 dark:border-red-800 rounded-xl p-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-200 mb-3" 
                                            placeholder="اكتب السبب هنا..." 
                                            required></textarea>
                                        
                                        <div class="flex gap-2">
                                            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                                                تأكيد الرفض
                                            </button>
                                            <button 
                                                type="button" 
                                                @click="showReject = false"
                                                class="px-4 py-2.5 bg-white dark:bg-dark-card border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 font-bold rounded-xl text-sm hover:bg-red-50 transition-colors">
                                                تراجع
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
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
