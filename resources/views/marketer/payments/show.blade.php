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
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="font-bold text-xl text-gray-900 dark:text-white flex items-center gap-3">
                                <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                                    <i data-lucide="store" class="w-5 h-5"></i>
                                </span>
                                معلومات الدفعة
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-dark-muted mt-2 mr-14 font-medium">بيانات المسوق والمتجر</p>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pb-4 border-b border-gray-200 dark:border-dark-border">
                            <div class="flex items-center gap-3">
                                <i data-lucide="store" class="w-5 h-5 text-gray-400 dark:text-gray-500"></i>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">اسم المتجر</p>
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $payment->store->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i data-lucide="phone" class="w-5 h-5 text-gray-400 dark:text-gray-500"></i>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">رقم الهاتف</p>
                                    @if($payment->store->phone)
                                        <a href="tel:{{ $payment->store->phone }}" class="font-bold text-primary-600 dark:text-primary-400 hover:underline">{{ $payment->store->phone }}</a>
                                    @else
                                        <p class="font-bold text-gray-900 dark:text-white">---</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <i data-lucide="user" class="w-5 h-5 text-gray-400 dark:text-gray-500"></i>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">المسوق</p>
                                <p class="font-bold text-gray-900 dark:text-white">{{ $payment->marketer->full_name }}</p>
                            </div>
                        </div>
                    </div>

                    @if($payment->status === 'approved')
                        @php
                            $commission = \App\Models\MarketerCommission::where('payment_id', $payment->id)->first();
                        @endphp
                        @if($commission)
                        <div class="mt-6 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 rounded-2xl p-5 border-2 border-emerald-200 dark:border-emerald-800">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="bg-emerald-100 dark:bg-emerald-900/40 p-2 rounded-lg text-emerald-600 dark:text-emerald-400">
                                    <i data-lucide="percent" class="w-4 h-4"></i>
                                </span>
                                <h3 class="font-bold text-base text-emerald-900 dark:text-emerald-300">عمولتك من هذا الإيصال</h3>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="bg-white/60 dark:bg-dark-bg/40 rounded-xl p-3 border border-emerald-200 dark:border-emerald-800">
                                    <p class="text-[10px] text-emerald-600 dark:text-emerald-400 mb-1 font-bold uppercase">نسبة العمولة</p>
                                    <p class="text-2xl font-black text-emerald-700 dark:text-emerald-300">{{ number_format($commission->commission_rate, 1) }}%</p>
                                </div>
                                <div class="bg-white/60 dark:bg-dark-bg/40 rounded-xl p-3 border border-emerald-200 dark:border-emerald-800">
                                    <p class="text-[10px] text-emerald-600 dark:text-emerald-400 mb-1 font-bold uppercase">مبلغ العمولة</p>
                                    <p class="text-xl font-black text-emerald-700 dark:text-emerald-300">{{ number_format($commission->commission_amount, 2) }} <span class="text-xs">دينار</span></p>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endif
                </div>

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-6 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
                    <div class="flex items-center gap-3 mb-6">
                        <span class="bg-purple-50 dark:bg-purple-900/20 p-2.5 rounded-xl text-purple-600 dark:text-purple-400 shadow-sm border border-purple-100 dark:border-purple-600/30">
                            <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                        </span>
                        <h2 class="font-bold text-xl text-gray-900 dark:text-white">تفاصيل الدفع</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 dark:from-dark-bg dark:to-dark-border rounded-2xl p-6 border-2 border-emerald-200 dark:border-dark-border">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-emerald-700 dark:text-emerald-400 font-medium mb-2">المبلغ المسدد</p>
                                    <p class="text-4xl font-black text-emerald-600 dark:text-emerald-400">{{ number_format($payment->amount, 2) }}</p>
                                    <p class="text-sm text-emerald-600 dark:text-emerald-400 font-bold mt-1">دينار</p>
                                </div>
                                <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center">
                                    <i data-lucide="banknote" class="w-8 h-8 text-emerald-600 dark:text-emerald-400"></i>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 dark:from-dark-bg dark:to-dark-border rounded-2xl p-6 border-2 border-blue-200 dark:border-dark-border">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-blue-700 dark:text-blue-400 font-medium mb-2">طريقة الدفع</p>
                                    <p class="text-2xl font-black text-blue-600 dark:text-blue-400">
                                        @if($payment->payment_method === 'cash') نقدي
                                        @elseif($payment->payment_method === 'transfer') تحويل بنكي
                                        @else شيك مصدق
                                        @endif
                                    </p>
                                </div>
                                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                    @if($payment->payment_method === 'cash')
                                        <i data-lucide="wallet" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                                    @elseif($payment->payment_method === 'transfer')
                                        <i data-lucide="arrow-right-left" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                                    @else
                                        <i data-lucide="file-text" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(in_array($payment->status, ['approved', 'rejected']) && $payment->keeper)
                    <div class="mt-4 bg-gray-50/50 dark:bg-dark-bg/50 rounded-xl p-4 border border-gray-200 dark:border-dark-border">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center">
                                <i data-lucide="user-check" class="w-5 h-5 text-gray-600 dark:text-gray-400"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 dark:text-dark-muted font-medium">أمين المخزن</p>
                                <p class="font-bold text-gray-900 dark:text-white">{{ $payment->keeper->full_name }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
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
                        <div x-data="{ showPrintOptions: false }" class="relative">
                            <button 
                                @click="showPrintOptions = !showPrintOptions"
                                class="w-full bg-gray-900 dark:bg-dark-bg text-white hover:bg-gray-800 dark:hover:bg-dark-card border border-transparent dark:border-dark-border py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-gray-200 dark:shadow-none flex items-center justify-center gap-2 group">
                                <i data-lucide="printer" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                                طباعة PDF
                                <i data-lucide="chevron-down" class="w-4 h-4 transition-transform" :class="showPrintOptions ? 'rotate-180' : ''"></i>
                            </button>

                            <div 
                                x-show="showPrintOptions"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                class="mt-3 space-y-2"
                                style="display: none;">
                                <a href="{{ route('marketer.payments.pdf', $payment) }}" target="_blank" class="w-full bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-dark-bg py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2 group">
                                    <i data-lucide="file-text" class="w-5 h-5"></i>
                                    A4 - النظام القديم
                                </a>
                                <button onclick="printThermal()" type="button" class="w-full bg-white dark:bg-dark-card border-2 border-emerald-200 dark:border-emerald-900/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2 group">
                                    <i data-lucide="receipt" class="w-5 h-5"></i>
                                    80mm - X-Printer
                                </button>
                                <button onclick="previewThermal()" type="button" class="w-full bg-white dark:bg-dark-card border-2 border-blue-200 dark:border-blue-900/30 text-blue-700 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2 group">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                    معاينة الإيصال
                                </button>
                            </div>
                        </div>

                        @if($payment->status === 'pending' || $payment->status === 'approved')
                            <div x-data="{ showAdjust: false }" class="mt-2">
                                <button
                                    type="button"
                                    x-show="!showAdjust"
                                    @click="showAdjust = true"
                                    class="w-full bg-white dark:bg-dark-card border-2 border-amber-200 dark:border-amber-900/30 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-sm">
                                    <i data-lucide="pencil" class="w-5 h-5"></i>
                                    تعديل الإيصال
                                </button>

                                <div
                                    x-show="showAdjust"
                                    x-transition
                                    class="bg-amber-50 dark:bg-amber-900/10 rounded-2xl p-4 border border-amber-200 dark:border-amber-900/30"
                                    style="display: none;">

                                    <form action="{{ route('marketer.payments.adjust', $payment) }}" method="POST" class="space-y-3">
                                        @csrf
                                        @method('PATCH')

                                        <div>
                                            <label class="block text-xs font-bold text-amber-800 dark:text-amber-300 mb-1">المبلغ:</label>
                                            <input type="number" name="amount" step="0.01" min="0.01"
                                                value="{{ $payment->amount }}"
                                                class="w-full bg-white dark:bg-dark-bg border border-amber-200 dark:border-amber-800 rounded-xl p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-200"
                                                required>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-amber-800 dark:text-amber-300 mb-1">طريقة الدفع:</label>
                                            <select name="payment_method"
                                                class="w-full bg-white dark:bg-dark-bg border border-amber-200 dark:border-amber-800 rounded-xl p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-200">
                                                <option value="cash" {{ $payment->payment_method === 'cash' ? 'selected' : '' }}>نقدي</option>
                                                <option value="transfer" {{ $payment->payment_method === 'transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                                                <option value="certified_check" {{ $payment->payment_method === 'certified_check' ? 'selected' : '' }}>شيك مصدق</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-amber-800 dark:text-amber-300 mb-1">ملاحظات:</label>
                                            <textarea name="notes" rows="2"
                                                class="w-full bg-white dark:bg-dark-bg border border-amber-200 dark:border-amber-800 rounded-xl p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-200"
                                                placeholder="اختياري...">{{ $payment->notes }}</textarea>
                                        </div>

                                        <div class="flex gap-2">
                                            <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
                                                حفظ التعديل
                                            </button>
                                            <button type="button" @click="showAdjust = false"
                                                class="px-4 py-2.5 bg-white dark:bg-dark-card border border-amber-200 dark:border-amber-800 text-amber-600 dark:text-amber-400 font-bold rounded-xl text-sm hover:bg-amber-50 transition-colors">
                                                تراجع
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif

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

                        {{-- Step 2: Approved or Rejected --}}
                        @if($payment->status == 'approved' && $payment->confirmed_at)
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
                                @if($payment->keeper)
                                <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">بواسطة: {{ $payment->keeper->full_name }}</p>
                                @endif
                                @if($payment->confirmed_at)
                                <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $payment->confirmed_at->format('Y-m-d h:i A') }}</span>
                                @endif
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
<style>
@font-face {
    font-family: 'Cairo';
    src: url('{{ asset('fonts/Cairo-Bold.ttf') }}') format('truetype');
    font-weight: bold;
    font-display: swap;
}
@font-face {
    font-family: 'Cairo';
    src: url('{{ asset('fonts/Cairo-Regular.ttf') }}') format('truetype');
    font-weight: normal;
    font-display: swap;
}
</style>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        
        const cairoRegular = new FontFace('Cairo', 'url({{ asset('fonts/Cairo-Regular.ttf') }})');
        const cairoBold = new FontFace('Cairo', 'url({{ asset('fonts/Cairo-Bold.ttf') }})', { weight: 'bold' });
        
        Promise.all([cairoRegular.load(), cairoBold.load()]).then(fonts => {
            fonts.forEach(font => document.fonts.add(font));
        }).catch(err => console.error('Failed to load Cairo font:', err));
    });

    let bluetoothDevice = null;
    let bluetoothCharacteristic = null;

    async function previewThermal() {
        const statusText = document.createElement('div');
        statusText.style.cssText = 'position:fixed;top:20px;right:20px;background:#667eea;color:white;padding:15px 25px;border-radius:10px;z-index:9999;font-weight:bold;box-shadow:0 4px 20px rgba(0,0,0,0.3)';
        statusText.innerText = '⏳ جاري التحضير...';
        document.body.appendChild(statusText);

        try {
            try {
                await Promise.race([
                    document.fonts.ready,
                    new Promise((_, reject) => setTimeout(() => reject('timeout'), 3000))
                ]);
            } catch (e) {}
            
            statusText.innerText = '📡 جاري تحميل بيانات الإيصال...';
            const response = await fetch('{{ route('marketer.payments.payment-data', $payment) }}');
            const data = await response.json();

            statusText.innerText = '⚡ بناء الإيصال...';
            const canvas = await buildReceiptCanvas(data);
            
            const modal = document.createElement('div');
            modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.9);z-index:10000;display:flex;align-items:center;justify-content:center;padding:20px';
            modal.innerHTML = `
                <div style="background:white;border-radius:20px;padding:20px;max-width:90%;max-height:90%;overflow:auto;position:relative">
                    <button onclick="this.closest('div').parentElement.remove()" style="position:absolute;top:10px;left:10px;background:#ef4444;color:white;border:none;border-radius:10px;padding:10px 20px;font-weight:bold;cursor:pointer;z-index:1">✕ إغلاق</button>
                    <div style="text-align:center">
                        <h2 style="margin-bottom:20px;color:#1f2937;font-size:24px;font-weight:bold">معاينة الإيصال</h2>
                        <img src="${canvas.toDataURL()}" style="max-width:100%;border:2px solid #e5e7eb;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.1)">
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
            
            statusText.remove();

        } catch (error) {
            console.error(error);
            alert('❌ فشلت المعاينة: ' + error.message);
            statusText.remove();
        }
    }

    async function printThermal() {
        const statusText = document.createElement('div');
        statusText.style.cssText = 'position:fixed;top:20px;right:20px;background:#667eea;color:white;padding:15px 25px;border-radius:10px;z-index:9999;font-weight:bold;box-shadow:0 4px 20px rgba(0,0,0,0.3)';
        statusText.innerText = '⏳ جاري التحضير...';
        document.body.appendChild(statusText);

        try {
            if (!navigator.bluetooth) {
                alert('❌ متصفحك لا يدعم البلوتوث');
                statusText.remove();
                return;
            }

            try {
                await Promise.race([
                    document.fonts.ready,
                    new Promise((_, reject) => setTimeout(() => reject('timeout'), 3000))
                ]);
            } catch (e) {}

            statusText.innerText = '📡 جاري تحميل بيانات الإيصال...';
            const response = await fetch('{{ route('marketer.payments.payment-data', $payment) }}');
            const data = await response.json();

            statusText.innerText = '⚡ بناء الإيصال...';
            const canvas = await buildReceiptCanvas(data);
            const rasterData = canvasToRaster(canvas);

            if (bluetoothDevice && !bluetoothDevice.gatt.connected) {
                try {
                    statusText.innerText = '🔄 إعادة الاتصال...';
                    await bluetoothDevice.gatt.disconnect();
                    await new Promise(resolve => setTimeout(resolve, 500));
                    const server = await bluetoothDevice.gatt.connect();
                    const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
                    bluetoothCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');
                } catch (reconnectError) {
                    bluetoothDevice = null;
                    bluetoothCharacteristic = null;
                }
            }

            if (!bluetoothDevice || !bluetoothDevice.gatt.connected) {
                statusText.innerText = '📡 اختر الطابعة...';
                bluetoothDevice = await navigator.bluetooth.requestDevice({
                    filters: [{ services: ['000018f0-0000-1000-8000-00805f9b34fb'] }],
                    optionalServices: ['000018f0-0000-1000-8000-00805f9b34fb']
                });

                bluetoothDevice.addEventListener('gattserverdisconnected', () => {
                    bluetoothDevice = null;
                    bluetoothCharacteristic = null;
                });

                statusText.innerText = '🔌 جاري الاتصال...';
                const server = await bluetoothDevice.gatt.connect();
                const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
                bluetoothCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');
            }

            statusText.innerText = '🖨️ جاري الطباعة...';
            await sendInChunks(bluetoothCharacteristic, rasterData);

            statusText.innerText = '✅ تمت الطباعة بنجاح!';
            setTimeout(() => statusText.remove(), 2000);

        } catch (error) {
            console.error(error);
            if (error.name !== 'NotFoundError') {
                alert('❌ فشلت العملية: ' + error.message);
            }
            statusText.remove();
        }
    }

    async function buildReceiptCanvas(data) {
        const canvas = document.createElement('canvas');
        canvas.width = 576;
        
        let estimatedHeight = 1000;
        
        // إضافة مساحة للشعار
        estimatedHeight += 180;
        
        if (data.notes) {
            const noteLines = wrapText(data.notes, 500, '18px Arial');
            estimatedHeight += noteLines.length * 25 + 60;
        }
        canvas.height = estimatedHeight;
        
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#000000';
        ctx.textAlign = 'center';
        
        let y = 40;
        
        // إضافة الشعار
        try {
            const logo = new Image();
            await new Promise((resolve, reject) => {
                logo.onload = resolve;
                logo.onerror = reject;
                logo.src = '{{ asset('images/company_black_white.png') }}';
                setTimeout(reject, 2000);
            });
            const logoWidth = 200;
            const logoHeight = (logo.height / logo.width) * logoWidth;
            
            // تحويل الصورة للأبيض والأسود
            const tempCanvas = document.createElement('canvas');
            tempCanvas.width = logoWidth;
            tempCanvas.height = logoHeight;
            const tempCtx = tempCanvas.getContext('2d');
            tempCtx.drawImage(logo, 0, 0, logoWidth, logoHeight);
            const imageData = tempCtx.getImageData(0, 0, logoWidth, logoHeight);
            const data = imageData.data;
            for (let i = 0; i < data.length; i += 4) {
                const gray = data[i] * 0.299 + data[i + 1] * 0.587 + data[i + 2] * 0.114;
                data[i] = data[i + 1] = data[i + 2] = gray;
            }
            tempCtx.putImageData(imageData, 0, 0);
            
            ctx.drawImage(tempCanvas, (canvas.width - logoWidth) / 2, y, logoWidth, logoHeight);
            y += logoHeight + 20;
        } catch (e) {
            console.log('Logo not loaded, skipping');
        }
        
        ctx.font = 'bold 32px Cairo, Arial';
        ctx.fillText('شركة المتفوقون الأوائل', 288, y);
        
        y += 50;
        ctx.fillStyle = '#000000';
        ctx.fillRect(180, y, 216, 40);
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 26px Cairo, Arial';
        ctx.fillText('إيصال قبض', 288, y + 28);
        
        y += 60;
        ctx.fillStyle = '#000000';
        ctx.font = '20px Cairo, Arial';
        ctx.fillText('رقم: ' + data.payment_number, 288, y);
        y += 28;
        ctx.fillText('تاريخ: ' + data.date, 288, y);
        
        y += 40;
        ctx.beginPath();
        ctx.setLineDash([10, 5]);
        ctx.moveTo(30, y);
        ctx.lineTo(546, y);
        ctx.stroke();
        ctx.setLineDash([]);
        
        y += 35;
        ctx.textAlign = 'right';
        ctx.font = '22px Cairo, Arial';
        ctx.fillText('المتجر: ' + data.store, 520, y);
        y += 28;
        ctx.font = '20px Cairo, Arial';
        ctx.fillText('رقم: ' + data.store_phone, 520, y);
        
        y += 35;
        ctx.font = '22px Cairo, Arial';
        ctx.fillText('المسوق: ' + data.marketer, 520, y);
        y += 28;
        ctx.font = '20px Cairo, Arial';
        ctx.fillText('رقم: ' + data.marketer_phone, 520, y);
        
        y += 40;
        ctx.beginPath();
        ctx.moveTo(30, y);
        ctx.lineTo(546, y);
        ctx.lineWidth = 3;
        ctx.stroke();
        ctx.lineWidth = 1;
        
        y += 50;
        const boxY = y;
        const boxHeight = 80;
        
        ctx.fillStyle = '#f0f0f0';
        ctx.fillRect(40, boxY, 496, boxHeight);
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 2;
        ctx.strokeRect(40, boxY, 496, boxHeight);
        
        ctx.fillStyle = '#000000';
        ctx.font = '20px Cairo, Arial';
        ctx.textAlign = 'right';
        ctx.fillText('المبلغ المسدد:', 510, boxY + 30);
        
        ctx.font = 'bold 36px Cairo, Arial';
        ctx.textAlign = 'center';
        ctx.direction = 'rtl';
        ctx.fillText(data.amount + ' دينار', 288, boxY + 55);
        
        y = boxY + boxHeight + 35;
        
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 1;
        ctx.strokeRect(40, y, 496, 45);
        ctx.beginPath();
        ctx.moveTo(288, y);
        ctx.lineTo(288, y + 45);
        ctx.stroke();
        
        ctx.font = '20px Cairo, Arial';
        ctx.textAlign = 'right';
        ctx.fillText('طريقة الدفع', 520, y + 28);
        ctx.font = 'bold 22px Cairo, Arial';
        ctx.textAlign = 'center';
        ctx.fillText(data.payment_method, 169, y + 28);
        
        y += 45;
        
        if (data.notes) {
            y += 30;
            ctx.beginPath();
            ctx.setLineDash([10, 5]);
            ctx.moveTo(30, y);
            ctx.lineTo(546, y);
            ctx.stroke();
            ctx.setLineDash([]);
            
            y += 35;
            ctx.font = 'bold 22px Cairo, Arial';
            ctx.textAlign = 'right';
            ctx.fillText('ملاحظات:', 520, y);
            y += 30;
            ctx.font = '18px Cairo, Arial';
            const noteLines = wrapText(data.notes, 500, '18px Cairo, Arial');
            noteLines.forEach(line => {
                ctx.fillText(line, 520, y);
                y += 25;
            });
        }
        
        y += 50;
        ctx.beginPath();
        ctx.setLineDash([10, 5]);
        ctx.moveTo(30, y);
        ctx.lineTo(546, y);
        ctx.stroke();
        ctx.setLineDash([]);
        
        y += 35;
        ctx.font = '20px Cairo, Arial';
        ctx.textAlign = 'center';
        ctx.fillText('شكراً لتعاملكم معنا', 288, y);
        
        y += 30;
        ctx.font = '16px Cairo, Arial';
        ctx.fillStyle = '#666666';
        const now = new Date();
        const printDate = `${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(now.getDate()).padStart(2,'0')} ${String(now.getHours()).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
        ctx.fillText('تاريخ الطباعة: ' + printDate, 288, y);
        
        return canvas;
    }

    function wrapText(text, maxWidth, font) {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        ctx.font = font;
        
        const words = text.split(' ');
        const lines = [];
        let currentLine = '';
        
        words.forEach(word => {
            const testLine = currentLine ? currentLine + ' ' + word : word;
            const metrics = ctx.measureText(testLine);
            
            if (metrics.width > maxWidth && currentLine) {
                lines.push(currentLine);
                currentLine = word;
            } else {
                currentLine = testLine;
            }
        });
        
        if (currentLine) lines.push(currentLine);
        return lines;
    }

    function canvasToRaster(canvas) {
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        const imageData = ctx.getImageData(0, 0, width, height).data;
        const widthBytes = Math.ceil(width / 8);
        const raster = [];

        raster.push(0x1B, 0x40);
        raster.push(0x1D, 0x76, 0x30, 0x00);
        raster.push(widthBytes & 0xFF, (widthBytes >> 8) & 0xFF);
        raster.push(height & 0xFF, (height >> 8) & 0xFF);

        for (let y = 0; y < height; y++) {
            for (let x = 0; x < widthBytes; x++) {
                let byte = 0;
                for (let bit = 0; bit < 8; bit++) {
                    const xPos = x * 8 + bit;
                    if (xPos < width) {
                        const idx = (y * width + xPos) * 4;
                        const br = imageData[idx] * 0.299 + imageData[idx + 1] * 0.587 + imageData[idx + 2] * 0.114;
                        if (imageData[idx + 3] > 128 && br < 185) byte |= (0x80 >> bit);
                    }
                }
                raster.push(byte);
            }
        }

        raster.push(0x1B, 0x4A, 0x20, 0x1B, 0x64, 0x05, 0x1D, 0x56, 0x00);
        return new Uint8Array(raster);
    }

    async function sendInChunks(characteristic, data) {
        const CHUNK_SIZE = 180;
        for (let i = 0; i < data.length; i += CHUNK_SIZE) {
            const chunk = data.slice(i, i + CHUNK_SIZE);
            if (characteristic.writeValueWithoutResponse) {
                await characteristic.writeValueWithoutResponse(chunk);
            } else {
                await characteristic.writeValue(chunk);
            }
            await new Promise(r => setTimeout(r, 2));
        }
    }
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
