@extends('layouts.app')

@section('title', 'تفاصيل طلب الإرجاع')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto space-y-8 px-4 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in-down">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        إرجاع بضاعة
                    </span>
                    <span class="text-gray-400 dark:text-dark-muted text-xs font-mono tracking-wider">
                        {{ $salesReturn->created_at->format('Y-m-d h:i A') }}
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    إرجاع #{{ $salesReturn->return_number }}
                </h1>
            </div>

            <div class="flex gap-3 w-full md:w-auto">
                <a href="{{ route('marketer.sales-returns.index') }}" class="px-12 py-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center justify-center gap-2 flex-1 md:flex-auto">
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    عودة
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <div class="lg:col-span-8 space-y-6 animate-slide-up order-2 lg:order-1">
                
                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-4 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border relative overflow-hidden">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="font-bold text-xl text-gray-900 dark:text-white flex items-center gap-3">
                                <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                                    <i data-lucide="store" class="w-5 h-5"></i>
                                </span>
                                معلومات المتجر
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-dark-muted mt-2 mr-14 font-medium">بيانات المتجر والفاتورة الأصلية</p>
                        </div>
                    </div>
                    <div class="bg-gray-50/50 dark:bg-dark-bg/60 rounded-2xl p-6 border border-gray-100 dark:border-dark-border">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-gray-400 dark:text-dark-muted mb-2 font-bold uppercase tracking-wider">اسم المتجر</p>
                                <p class="font-black text-gray-900 dark:text-white text-lg">{{ $salesReturn->store->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 dark:text-dark-muted mb-2 font-bold uppercase tracking-wider">الفاتورة الأصلية</p>
                                <p class="font-black text-gray-900 dark:text-white text-lg">#{{ $salesReturn->salesInvoice->invoice_number }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-4 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border relative overflow-hidden">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="font-bold text-xl text-gray-900 dark:text-white flex items-center gap-3">
                                <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                                    <i data-lucide="undo-2" class="w-5 h-5"></i>
                                </span>
                                المنتجات المرجعة
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-dark-muted mt-2 mr-14 font-medium">قائمة الأصناف المرجعة</p>
                        </div>
                        <span class="bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 px-4 py-2 rounded-xl text-xs font-black shadow-sm">
                            {{ $salesReturn->items->count() }} أصناف
                        </span>
                    </div>

                    <div class="overflow-x-auto negative-margin-x pb-2">
                        <table class="w-full border-separate border-spacing-y-3">
                            <thead>
                                <tr class="text-xs text-gray-400 dark:text-dark-muted font-bold uppercase tracking-wider">
                                    <th class="px-4 md:px-6 py-2 text-right">المنتج</th>
                                    <th class="px-4 md:px-6 py-2 text-center">الكمية</th>
                                    <th class="px-4 md:px-6 py-2 text-center">السعر</th>
                                    <th class="px-4 md:px-6 py-2 text-center">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($salesReturn->items as $item)
                                <tr class="group hover:-translate-y-0.5 transition-transform duration-300">
                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 rounded-r-2xl border border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all">
                                        <div class="flex items-center gap-4">
                                            <div class="hidden md:flex w-12 h-12 rounded-xl bg-white dark:bg-dark-card items-center justify-center text-gray-400 dark:text-gray-500 border border-gray-100 dark:border-dark-border shadow-sm group-hover:text-primary-600 dark:group-hover:text-accent-400 transition-colors shrink-0">
                                                <i data-lucide="package" class="w-6 h-6"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-bold text-gray-900 dark:text-gray-100 text-base md:text-lg truncate">{{ $item->product->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 border-y border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all text-center">
                                        <span class="inline-flex items-center justify-center bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-900 dark:text-gray-100 font-black px-6 py-2 rounded-xl text-lg shadow-sm">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 border-y border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all text-center">
                                        <span class="font-bold text-gray-700 dark:text-gray-300">{{ number_format($item->unit_price, 2) }} دينار</span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 rounded-l-2xl border border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all text-center">
                                        <span class="font-black text-gray-900 dark:text-gray-100 text-lg">{{ number_format($item->quantity * $item->unit_price, 2) }} دينار</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8 bg-gradient-to-br from-primary-50 to-primary-100/50 dark:from-gray-900 dark:to-black rounded-3xl p-8 border-2 border-primary-100 dark:border-transparent text-gray-900 dark:text-white flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-64 h-64 bg-primary-200 dark:bg-accent-500 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-[60px] opacity-30 dark:opacity-10 -translate-x-1/2 -translate-y-1/2 transition-transform duration-700 group-hover:scale-125"></div>
                        
                        <div class="relative z-10 w-full">
                            <div class="flex justify-between items-baseline">
                                <span class="text-lg font-bold uppercase tracking-wider text-gray-800 dark:text-white">الإجمالي:</span>
                                <div class="text-5xl font-black tracking-tighter flex items-baseline gap-2 text-gray-900 dark:text-white">
                                    {{ number_format($salesReturn->total_amount, 2) }}
                                    <span class="text-xl font-bold text-gray-600 dark:text-gray-300">دينار</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($salesReturn->notes)
                <div class="bg-white dark:bg-dark-card border-gray-100 dark:border-dark-border rounded-[1.5rem] shadow-sm border p-4 md:p-8 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-primary-50 dark:bg-primary-900/20 rounded-full mix-blend-multiply dark:mix-blend-overlay filter blur-3xl opacity-40 -translate-y-1/2 translate-x-1/2 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <h3 class="text-gray-800 dark:text-white font-bold text-lg md:text-2xl mb-4 md:mb-6 flex items-center gap-3 md:gap-4">
                            <span class="p-2 md:p-3 rounded-xl bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400 shadow-sm">
                                <i data-lucide="sticky-note" class="w-5 h-5 md:w-7 md:h-7"></i>
                            </span>
                            ملاحظات
                        </h3>
                        <div class="bg-gray-50/50 dark:bg-dark-bg/50 border-gray-100 dark:border-dark-border border backdrop-blur-sm rounded-2xl p-4 md:p-6 text-gray-600 dark:text-gray-300 text-sm md:text-lg font-medium leading-loose">
                            {{ $salesReturn->notes }}
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="lg:col-span-4 space-y-6 animate-slide-up order-1 lg:order-2" style="animation-delay: 0.1s">
                
                <div class="bg-gray-50 dark:bg-dark-card/50 rounded-[1.5rem] border-2 border-dashed border-gray-200 dark:border-dark-border p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gray-200 dark:bg-dark-bg rounded-full mix-blend-multiply dark:mix-blend-overlay filter blur-3xl opacity-40 -translate-y-1/2 translate-x-1/2"></div>

                    <h3 class="text-gray-800 dark:text-gray-200 font-bold text-lg mb-6 flex items-center gap-2 relative z-10">
                        <i data-lucide="activity" class="w-5 h-5 text-gray-400 dark:text-dark-muted"></i>
                        حالة الإرجاع الحالية
                    </h3>

                    <div class="relative z-10 text-center py-4">
                        @php
                            $statusConfig = [
                                'pending' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-600 dark:text-amber-400', 'icon' => 'clock', 'label' => 'قيد الانتظار'],
                                'approved' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-600 dark:text-emerald-400', 'icon' => 'check-circle', 'label' => 'موثق'],
                                'rejected' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-600 dark:text-red-400', 'icon' => 'x-circle', 'label' => 'مرفوض'],
                                'cancelled' => ['bg' => 'bg-gray-100 dark:bg-gray-800', 'text' => 'text-gray-500', 'icon' => 'slash', 'label' => 'ملغي'],
                            ][$salesReturn->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'icon' => 'help-circle', 'label' => $salesReturn->status];
                        @endphp
                        
                        <div class="inline-flex items-center justify-center p-4 rounded-full {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} mb-4 shadow-inner ring-4 ring-white dark:ring-dark-card">
                            <i data-lucide="{{ $statusConfig['icon'] }}" class="w-8 h-8"></i>
                        </div>
                        <h2 class="text-2xl font-black {{ $statusConfig['text'] }}">{{ $statusConfig['label'] }}</h2>
                        <p class="text-xs text-gray-400 dark:text-dark-muted mt-2 font-medium mb-4">آخر تحديث: {{ $salesReturn->updated_at->diffForHumans() }}</p>

                        @if($salesReturn->status === 'approved' && $salesReturn->stamped_image)
                            <button type="button" onclick="showDocumentationModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-dark-bg border border-blue-200 dark:border-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-bold hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors shadow-sm mb-2">
                                <i data-lucide="image" class="w-4 h-4"></i>
                                عرض صورة التوثيق
                            </button>
                        @endif
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-dark-border z-10 relative">
                        <a href="{{ route('marketer.sales-returns.pdf', $salesReturn) }}" class="w-full bg-gray-900 dark:bg-dark-bg text-white hover:bg-gray-800 dark:hover:bg-dark-card border border-transparent dark:border-dark-border py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-gray-200 dark:shadow-none flex items-center justify-center gap-2 group">
                            <i data-lucide="printer" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                            طباعة PDF
                        </a>

                        @if($salesReturn->status === 'pending')
                            <div x-data="{ showCancel: false }" class="mt-4">
                                <button 
                                    type="button" 
                                    x-show="!showCancel"
                                    @click="showCancel = true"
                                    class="w-full bg-white dark:bg-dark-card border-2 border-red-50 dark:border-red-900/30 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:border-red-100 dark:hover:border-red-800 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 group shadow-sm">
                                    <i data-lucide="x-circle" class="w-5 h-5 group-hover:rotate-90 transition-transform"></i>
                                    إلغاء الطلب
                                </button>

                                <div 
                                    x-show="showCancel" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="bg-red-50 dark:bg-red-900/10 rounded-2xl p-4 border border-red-100 dark:border-red-900/30"
                                    style="display: none;">
                                    
                                    <form action="{{ route('marketer.sales-returns.cancel', $salesReturn) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        
                                        <label class="block text-xs font-bold text-red-800 dark:text-red-300 mb-2 mr-1">سبب الإلغاء:</label>
                                        <textarea 
                                            name="notes" 
                                            rows="2" 
                                            class="w-full bg-white dark:bg-dark-bg border border-red-200 dark:border-red-800 rounded-xl p-3 text-sm focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 dark:focus:ring-red-900/50 transition-all placeholder:text-red-300 dark:placeholder:text-red-700 dark:text-white mb-3" 
                                            placeholder="اكتب السبب هنا..." 
                                            required></textarea>
                                        
                                        <div class="flex gap-2">
                                            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors shadow-sm">
                                                تأكيد الإلغاء
                                            </button>
                                            <button 
                                                type="button" 
                                                @click="showCancel = false"
                                                class="px-4 py-2.5 bg-white dark:bg-dark-card border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 font-bold rounded-xl text-sm hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                                تراجع
                                            </button>
                                        </div>
                                    </form>
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

@if($salesReturn->status === 'approved' && $salesReturn->stamped_image)
    @include('shared.modals.documentation-image', [
        'imageUrl' => route('warehouse.sales-returns.documentation', $salesReturn->id),
        'invoiceNumber' => $salesReturn->return_number,
        'documentedAt' => $salesReturn->confirmed_at ?? $salesReturn->updated_at
    ])
@endif
@endsection
