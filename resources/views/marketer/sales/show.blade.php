@extends('layouts.app')

@section('title', 'تفاصيل الفاتورة #' . $invoice->invoice_number)

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto space-y-8 px-4 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in-down">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        فاتورة بيع
                    </span>
                    <span class="text-gray-400 dark:text-dark-muted text-xs font-mono tracking-wider">
                        {{ $invoice->created_at->format('Y-m-d h:i A') }}
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    فاتورة #{{ $invoice->invoice_number }}
                </h1>
            </div>

            <div class="flex gap-3 w-full md:w-auto">
                <a href="{{ route('marketer.sales.index') }}" class="px-12 py-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center justify-center gap-2 flex-1 md:flex-auto">
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
                                معلومات الفاتورة
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
                                    <p class="font-bold text-gray-900 dark:text-white">{{ $invoice->store->name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <i data-lucide="phone" class="w-5 h-5 text-gray-400 dark:text-gray-500"></i>
                                <div class="flex-1">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">رقم الهاتف</p>
                                    @if($invoice->store->phone)
                                        <a href="tel:{{ $invoice->store->phone }}" class="font-bold text-primary-600 dark:text-primary-400 hover:underline">{{ $invoice->store->phone }}</a>
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
                                <p class="font-bold text-gray-900 dark:text-white">{{ $invoice->marketer->full_name }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-4 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border relative overflow-hidden">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="font-bold text-xl text-gray-900 dark:text-white flex items-center gap-3">
                                <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                                </span>
                                المنتجات المباعة
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-dark-muted mt-2 mr-14 font-medium">قائمة الأصناف في هذه الفاتورة</p>
                        </div>
                        <span class="bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 px-4 py-2 rounded-xl text-xs font-black shadow-sm">
                            {{ $invoice->items->count() }} أصناف
                        </span>
                    </div>

                    <div class="overflow-x-auto negative-margin-x pb-2">
                        <table class="w-full border-separate border-spacing-y-3">
                            <thead>
                                <tr class="text-xs text-gray-400 dark:text-dark-muted font-bold uppercase tracking-wider">
                                    <th class="px-4 md:px-6 py-2 text-right">المنتج</th>
                                    <th class="px-4 md:px-6 py-2 text-center">الكمية</th>
                                    <th class="px-4 md:px-6 py-2 text-center">مجاني</th>
                                    <th class="px-4 md:px-6 py-2 text-center">السعر</th>
                                    <th class="px-4 md:px-6 py-2 text-center">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
                                <tr class="group hover:-translate-y-0.5 transition-transform duration-300">
                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 rounded-r-2xl border border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all">
                                        <div class="flex items-center gap-4">
                                            <div class="flex-1 min-w-0">
                                                <div class="font-bold text-gray-900 dark:text-gray-100 text-xs md:text-base truncate">{{ $item->product->name }}</div>
                                                <div class="text-xs text-gray-400 dark:text-dark-muted mt-1 font-mono">{{ $item->product->barcode ?? '---' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 border-y border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all text-center">
                                        <span class="inline-flex items-center justify-center bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-900 dark:text-gray-100 font-black px-2 md:px-4 py-1 md:py-1.5 rounded-xl text-sm shadow-sm">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 border-y border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all text-center">
                                        <span class="inline-flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 font-black px-2 md:px-4 py-1 md:py-1.5 rounded-xl text-sm shadow-sm">{{ $item->free_quantity }}</span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 border-y border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all text-center">
                                        <span class="font-bold text-gray-700 dark:text-gray-300 text-xs">{{ number_format($item->unit_price, 2) }} دينار</span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 rounded-l-2xl border border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all text-center">
                                        <span class="font-black text-gray-900 dark:text-gray-100 text-sm">{{ number_format(($item->quantity + $item->free_quantity) * $item->unit_price, 2) }} دينار</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8 bg-gradient-to-br from-primary-50 to-primary-100/50 dark:from-gray-900 dark:to-black rounded-3xl p-4 md:p-8 border-2 border-primary-100 dark:border-transparent text-gray-900 dark:text-white flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-64 h-64 bg-primary-200 dark:bg-accent-500 rounded-full mix-blend-multiply dark:mix-blend-screen filter blur-[60px] opacity-30 dark:opacity-10 -translate-x-1/2 -translate-y-1/2 transition-transform duration-700 group-hover:scale-125"></div>
                        
                        <div class="relative z-10 w-full space-y-2 md:space-y-4">
                            <div class="flex justify-between text-sm md:text-base">
                                <span class="font-semibold text-gray-700 dark:text-white">عدد البضاعة:</span>
                                <span class="font-black text-base md:text-xl text-gray-900 dark:text-white">{{ $invoice->items->sum(fn($item) => $item->quantity + $item->free_quantity) }}</span>
                            </div>
                            <div class="flex justify-between text-sm md:text-base">
                                <span class="font-semibold text-gray-700 dark:text-white">المجموع الفرعي:</span>
                                <span class="font-black text-base md:text-xl text-gray-900 dark:text-white">{{ number_format($invoice->subtotal, 2) }} دينار</span>
                            </div>
                            @if($invoice->product_discount > 0)
                            <div class="flex justify-between text-sm md:text-base text-emerald-600 dark:text-emerald-300">
                                <span class="font-semibold">خصم المنتجات (هدايا):</span>
                                <span class="font-black text-base md:text-xl">- {{ number_format($invoice->product_discount, 2) }} دينار</span>
                            </div>
                            @endif
                            @if($invoice->invoice_discount_amount > 0)
                            <div class="flex justify-between text-sm md:text-base text-blue-600 dark:text-blue-300">
                                <span class="font-semibold">خصم الفاتورة:</span>
                                <span class="font-black text-base md:text-xl">- {{ number_format($invoice->invoice_discount_amount, 2) }} دينار</span>
                            </div>
                            @endif
                            <div class="pt-3 md:pt-4 border-t-2 border-primary-200 dark:border-white/20 flex flex-col md:flex-row justify-between md:items-baseline gap-2">
                                <span class="text-base md:text-lg font-bold uppercase tracking-wider text-gray-800 dark:text-white">الإجمالي النهائي:</span>
                                <div class="text-3xl md:text-5xl font-black tracking-tighter flex items-baseline gap-2 text-gray-900 dark:text-white">
                                    {{ number_format($invoice->total_amount, 2) }}
                                    <span class="text-base md:text-xl font-bold text-gray-600 dark:text-gray-300">دينار</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($invoice->notes)
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
                            {{ $invoice->notes }}
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
                        حالة الفاتورة الحالية
                    </h3>

                    <div class="relative z-10 text-center py-4">
                        @php
                            $statusConfig = [
                                'pending' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-600 dark:text-amber-400', 'icon' => 'clock', 'label' => 'قيد الانتظار'],
                                'approved' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-600 dark:text-emerald-400', 'icon' => 'check-circle', 'label' => 'موثق'],
                                'rejected' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-600 dark:text-red-400', 'icon' => 'x-circle', 'label' => 'مرفوض'],
                                'cancelled' => ['bg' => 'bg-gray-100 dark:bg-gray-800', 'text' => 'text-gray-500', 'icon' => 'slash', 'label' => 'ملغي'],
                            ][$invoice->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'icon' => 'help-circle', 'label' => $invoice->status];
                        @endphp
                        
                        <div class="inline-flex items-center justify-center p-4 rounded-full {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} mb-4 shadow-inner ring-4 ring-white dark:ring-dark-card">
                            <i data-lucide="{{ $statusConfig['icon'] }}" class="w-8 h-8"></i>
                        </div>
                        <h2 class="text-2xl font-black {{ $statusConfig['text'] }}">{{ $statusConfig['label'] }}</h2>
                        <p class="text-xs text-gray-400 dark:text-dark-muted mt-2 font-medium mb-4">آخر تحديث: {{ $invoice->updated_at->diffForHumans() }}</p>

                        @if($invoice->status === 'approved' && $invoice->stamped_invoice_image)
                            <button type="button" onclick="showDocumentationModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-dark-bg border border-blue-200 dark:border-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg text-xs font-bold hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors shadow-sm mb-2">
                                <i data-lucide="image" class="w-4 h-4"></i>
                                عرض صورة التوثيق
                            </button>
                        @endif
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-dark-border z-10 relative">
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
                                <a href="{{ route('marketer.sales.pdf', $invoice) }}" target="_blank" class="w-full bg-white dark:bg-dark-card border-2 border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-dark-bg py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2 group">
                                    <i data-lucide="file-text" class="w-5 h-5"></i>
                                    A4 - النظام القديم
                                </a>
                                <button onclick="printThermal()" type="button" class="w-full bg-white dark:bg-dark-card border-2 border-emerald-200 dark:border-emerald-900/30 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2 group">
                                    <i data-lucide="receipt" class="w-5 h-5"></i>
                                    80mm - X-Printer
                                </button>
                                <button onclick="previewThermal()" type="button" class="w-full bg-white dark:bg-dark-card border-2 border-blue-200 dark:border-blue-900/30 text-blue-700 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 py-3 rounded-xl font-bold transition-all flex items-center justify-center gap-2 group">
                                    <i data-lucide="eye" class="w-5 h-5"></i>
                                    معاينة الفاتورة
                                </button>
                            </div>
                        </div>

                        @if($invoice->status === 'pending' || $invoice->status === 'approved')
                            @php $hasPromotion = $invoice->items->whereNotNull('promotion_id')->isNotEmpty(); @endphp

                            @if($hasPromotion)
                            <div class="mt-2 flex items-center gap-2 bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800 rounded-xl px-4 py-3">
                                <i data-lucide="info" class="w-4 h-4 text-amber-500 shrink-0"></i>
                                <p class="text-xs text-amber-700 dark:text-amber-400 font-medium">لا يمكن تعديل فاتورة تحتوي على عروض ترويجية</p>
                            </div>
                            @else
                            <div x-data="{ showAdjust: false }" class="mt-2">
                                <button
                                    type="button"
                                    x-show="!showAdjust"
                                    @click="showAdjust = true"
                                    class="w-full bg-white dark:bg-dark-card border-2 border-amber-200 dark:border-amber-900/30 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 shadow-sm">
                                    <i data-lucide="pencil" class="w-5 h-5"></i>
                                    تعديل الفاتورة
                                </button>

                                <div
                                    x-show="showAdjust"
                                    x-transition
                                    class="bg-amber-50 dark:bg-amber-900/10 rounded-2xl p-4 border border-amber-200 dark:border-amber-900/30"
                                    style="display: none;">

                                    <form action="{{ route('marketer.sales.adjust', $invoice) }}" method="POST" class="space-y-3">
                                        @csrf
                                        @method('PATCH')

                                        <div x-data="storeSearch({{ $invoice->store_id }}, '{{ $invoice->store->name }} - {{ $invoice->store->owner_name }}')">
                                            <label class="block text-xs font-bold text-amber-800 dark:text-amber-300 mb-1">المتجر:</label>
                                            <div class="relative">
                                                <input type="text" x-model="search" @input="filter" @focus="open = true" @click.outside="open = false"
                                                    autocomplete="off"
                                                    placeholder="ابحث عن المتجر..."
                                                    class="w-full bg-white dark:bg-dark-bg border border-amber-200 dark:border-amber-800 rounded-xl p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-200">
                                                <input type="hidden" name="store_id" x-model="selectedId" required>
                                                <div x-show="open && results.length > 0"
                                                    class="absolute z-50 w-full mt-1 bg-white dark:bg-dark-card border border-amber-200 dark:border-amber-800 rounded-xl shadow-xl max-h-40 overflow-y-auto"
                                                    style="display:none">
                                                    <template x-for="store in results" :key="store.id">
                                                        <div @click="select(store)" class="px-3 py-2 hover:bg-amber-50 dark:hover:bg-amber-900/20 cursor-pointer border-b border-amber-100 dark:border-amber-900/20 last:border-0">
                                                            <div class="font-bold text-gray-900 dark:text-white text-xs" x-text="store.name"></div>
                                                            <div class="text-[10px] text-gray-500" x-text="store.owner"></div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-amber-800 dark:text-amber-300 mb-2">المنتجات:</label>
                                            <div id="adjust-items" class="space-y-2">
                                                @foreach($products as $p)
                                                    @php
                                                        $invoiceItem = $invoice->items->firstWhere('product_id', $p->id);
                                                        $availableStock = ($p->stock ?? 0) + ($invoiceItem ? $invoiceItem->quantity : 0);
                                                    @endphp
                                                    @if($availableStock > 0)
                                                    <div class="adjust-product-row {{ $invoiceItem ? '' : 'hidden' }}"
                                                         data-product-id="{{ $p->id }}"
                                                         data-stock="{{ $availableStock }}"
                                                         data-price="{{ $p->current_price }}">
                                                        <div class="flex items-center gap-2 bg-white dark:bg-dark-bg border border-amber-200 dark:border-amber-800 rounded-lg p-2">
                                                            <span class="flex-1 text-xs font-bold text-gray-800 dark:text-gray-200 truncate">{{ $p->name }}</span>
                                                            <span class="text-[10px] text-gray-400 shrink-0">{{ $availableStock }}</span>
                                                            <input type="number" name="" value="{{ $invoiceItem ? $invoiceItem->quantity : 1 }}"
                                                                min="1" max="{{ $availableStock }}"
                                                                oninput="calcAdjustTotal()"
                                                                class="qty-input w-16 border border-amber-200 dark:border-amber-700 rounded-lg p-1 text-sm text-center bg-amber-50 dark:bg-amber-900/20 focus:outline-none focus:ring-2 focus:ring-amber-300">
                                                            <button type="button" onclick="toggleAdjustProduct(this)" class="text-red-400 hover:text-red-600 shrink-0">
                                                                <i data-lucide="x" class="w-4 h-4"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                            <button type="button" id="adjust-add-btn" onclick="toggleAdjustDropdown()"
                                                class="mt-2 w-full flex items-center justify-center gap-1 py-2 bg-white dark:bg-dark-bg border border-dashed border-amber-300 dark:border-amber-700 text-amber-600 dark:text-amber-400 rounded-lg text-xs font-bold hover:bg-amber-50 transition-colors">
                                                <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                                                إضافة منتج
                                            </button>
                                            <div id="adjust-product-dropdown" class="hidden mt-1 bg-white dark:bg-dark-card border border-amber-200 dark:border-amber-800 rounded-xl shadow-xl max-h-48 overflow-y-auto"></div>
                                        </div>

                                        <div class="bg-white dark:bg-dark-bg rounded-xl p-3 border border-amber-200 dark:border-amber-800 space-y-1.5">
                                            <div class="flex justify-between text-xs">
                                                <span class="text-gray-500 dark:text-gray-400">عدد البضاعة</span>
                                                <span id="adj-total-items" class="font-bold text-gray-800 dark:text-gray-200">0</span>
                                            </div>
                                            <div class="flex justify-between text-xs">
                                                <span class="text-gray-500 dark:text-gray-400">المجموع الفرعي</span>
                                                <span id="adj-subtotal" class="font-bold text-gray-800 dark:text-gray-200">0.00</span>
                                            </div>
                                            <div class="flex justify-between text-xs text-blue-600 dark:text-blue-400" id="adj-discount-row" style="display:none!important">
                                                <span>خصم الفاتورة</span>
                                                <span id="adj-discount">0.00</span>
                                            </div>
                                            <div class="flex justify-between text-sm pt-1 border-t border-amber-200 dark:border-amber-800">
                                                <span class="font-bold text-gray-800 dark:text-gray-200">الإجمالي</span>
                                                <span id="adj-total" class="font-black text-amber-600 dark:text-amber-400">0.00 دينار</span>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold text-amber-800 dark:text-amber-300 mb-1">ملاحظات:</label>
                                            <textarea name="notes" rows="2"
                                                class="w-full bg-white dark:bg-dark-bg border border-amber-200 dark:border-amber-800 rounded-xl p-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-200"
                                                placeholder="اختياري...">{{ $invoice->notes }}</textarea>
                                        </div>

                                        <div class="flex gap-2">
                                            <button type="button" onclick="submitAdjustForm()" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2.5 rounded-xl text-sm transition-colors">
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
                        @endif

                        @if($invoice->status === 'pending')
                            <div x-data="{ showCancel: false }" class="mt-4">
                                <button 
                                    type="button" 
                                    x-show="!showCancel"
                                    @click="showCancel = true"
                                    class="w-full bg-white dark:bg-dark-card border-2 border-red-50 dark:border-red-900/30 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:border-red-100 dark:hover:border-red-800 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 group shadow-sm">
                                    <i data-lucide="x-circle" class="w-5 h-5 group-hover:rotate-90 transition-transform"></i>
                                    إلغاء الفاتورة
                                </button>

                                <div 
                                    x-show="showCancel" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="bg-red-50 dark:bg-red-900/10 rounded-2xl p-4 border border-red-100 dark:border-red-900/30"
                                    style="display: none;">
                                    
                                    <form action="{{ route('marketer.sales.cancel', $invoice) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        
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

                        @if($invoice->status === 'approved')
                            <div x-data="{ showCancelApproved: false }" class="mt-4">
                                <button
                                    type="button"
                                    x-show="!showCancelApproved"
                                    @click="showCancelApproved = true"
                                    class="w-full bg-white dark:bg-dark-card border-2 border-red-50 dark:border-red-900/30 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:border-red-100 dark:hover:border-red-800 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 group shadow-sm">
                                    <i data-lucide="x-circle" class="w-5 h-5 group-hover:rotate-90 transition-transform"></i>
                                    إلغاء الفاتورة
                                </button>

                                <div
                                    x-show="showCancelApproved"
                                    x-transition
                                    class="bg-red-50 dark:bg-red-900/10 rounded-2xl p-4 border border-red-100 dark:border-red-900/30"
                                    style="display: none;">

                                    <form action="{{ route('marketer.sales.cancel-approved', $invoice) }}" method="POST">
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
                                                @click="showCancelApproved = false"
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
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">تم إنشاء الفاتورة</h4>
                                <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">بواسطة: {{ $invoice->marketer->full_name }}</p>
                                <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $invoice->created_at->format('Y-m-d h:i A') }}</span>
                            </div>
                        </div>

                        {{-- Step 2: Approved --}}
                        @if($invoice->confirmed_at)
                        <div class="relative flex items-start gap-4 animate-slide-up">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">تم التوثيق</h4>
                                <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">بواسطة: {{ $invoice->keeper->full_name }}</p>
                                <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $invoice->confirmed_at->format('Y-m-d h:i A') }}</span>
                            </div>
                        </div>
                        @elseif($invoice->status == 'rejected')
                        <div class="relative flex items-start gap-4 animate-slide-up">
                            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-red-800 dark:text-red-400 text-sm">تم الرفض</h4>
                                <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">بواسطة: أمين المخزن</p>
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
                        @if($invoice->status == 'cancelled')
                        <div class="relative flex items-start gap-4 animate-slide-up">
                            <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="slash" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-600 dark:text-gray-300 text-sm">تم الإلغاء</h4>
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
        
        // تحميل خط Cairo مسبقاً
        const cairoRegular = new FontFace('Cairo', 'url({{ asset('fonts/Cairo-Regular.ttf') }})');
        const cairoBold = new FontFace('Cairo', 'url({{ asset('fonts/Cairo-Bold.ttf') }})', { weight: 'bold' });
        
        Promise.all([cairoRegular.load(), cairoBold.load()]).then(fonts => {
            fonts.forEach(font => document.fonts.add(font));
            console.log('Cairo font loaded successfully');
        }).catch(err => {
            console.error('Failed to load Cairo font:', err);
        });
    });

    const allStores = @json($storesJson);

    const allProducts = @json($productsJson);

    const invoiceItemsQty = @json($invoiceItemsQty);

    function toggleAdjustProduct(btn) {
        const row = btn.closest('.adjust-product-row');
        row.classList.add('hidden');
        updateAdjustAddBtn();
    }

    function toggleAdjustDropdown() {
        const dd = document.getElementById('adjust-product-dropdown');
        if (dd.classList.contains('hidden')) {
            renderAdjustDropdown();
            dd.classList.remove('hidden');
        } else {
            dd.classList.add('hidden');
        }
    }

    function renderAdjustDropdown() {
        const dd = document.getElementById('adjust-product-dropdown');
        const hiddenRows = document.querySelectorAll('#adjust-items .adjust-product-row.hidden');
        dd.innerHTML = '';
        hiddenRows.forEach(row => {
            const div = document.createElement('div');
            div.className = 'px-3 py-2 hover:bg-amber-50 dark:hover:bg-amber-900/20 cursor-pointer border-b border-amber-100 dark:border-amber-900/20 last:border-0 text-xs font-bold text-gray-800 dark:text-gray-200';
            div.textContent = row.querySelector('span').textContent.trim();
            div.onclick = () => {
                row.classList.remove('hidden');
                dd.classList.add('hidden');
                updateAdjustAddBtn();
                calcAdjustTotal();
                lucide.createIcons();
            };
            dd.appendChild(div);
        });
    }

    function updateAdjustAddBtn() {
        const btn = document.getElementById('adjust-add-btn');
        const hiddenCount = document.querySelectorAll('#adjust-items .adjust-product-row.hidden').length;
        btn.disabled = hiddenCount === 0;
        btn.classList.toggle('opacity-40', hiddenCount === 0);
        btn.classList.toggle('cursor-not-allowed', hiddenCount === 0);
        calcAdjustTotal();
    }

    function calcAdjustTotal() {
        let totalItems = 0, subtotal = 0;
        document.querySelectorAll('#adjust-items .adjust-product-row:not(.hidden)').forEach(row => {
            const qty = parseInt(row.querySelector('.qty-input').value) || 0;
            const price = parseFloat(row.dataset.price || 0);
            totalItems += qty;
            subtotal += qty * price;
        });

        document.getElementById('adj-total-items').textContent = totalItems;
        document.getElementById('adj-subtotal').textContent = subtotal.toFixed(2);

        fetch(`{{ url('/calculate-invoice-discount') }}?amount=${subtotal}`)
            .then(r => r.json())
            .then(data => {
                const disc = data.discount_amount || 0;
                const total = subtotal - disc;
                const discRow = document.getElementById('adj-discount-row');
                discRow.style.cssText = disc > 0 ? '' : 'display:none!important';
                document.getElementById('adj-discount').textContent = disc.toFixed(2);
                document.getElementById('adj-total').textContent = total.toFixed(2) + ' دينار';
            })
            .catch(() => {
                document.getElementById('adj-total').textContent = subtotal.toFixed(2) + ' دينار';
            });
    }

    function submitAdjustForm() {
        let idx = 0;
        document.querySelectorAll('#adjust-items .adjust-product-row:not(.hidden)').forEach(row => {
            const pid = row.dataset.productId;
            row.querySelector('.qty-input').name = 'items[' + idx + '][quantity]';
            let pidInput = row.querySelector('.pid-input');
            if (!pidInput) {
                pidInput = document.createElement('input');
                pidInput.type = 'hidden';
                pidInput.className = 'pid-input';
                row.appendChild(pidInput);
            }
            pidInput.name = 'items[' + idx + '][product_id]';
            pidInput.value = pid;
            idx++;
        });
        if (idx === 0) { alert('يجب اختيار منتج واحد على الأقل'); return; }
        document.getElementById('adjust-form').submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateAdjustAddBtn();
        document.addEventListener('click', function (e) {
            const dd = document.getElementById('adjust-product-dropdown');
            const btn = document.getElementById('adjust-add-btn');
            if (dd && btn && !dd.contains(e.target) && !btn.contains(e.target)) {
                dd.classList.add('hidden');
            }
        });
    });

    function storeSearch(defaultId, defaultLabel) {
        return {
            search: defaultLabel,
            selectedId: defaultId,
            open: false,
            results: [],
            filter() {
                this.selectedId = '';
                const q = this.search.toLowerCase();
                this.results = q.length === 0 ? [] : allStores.filter(
                    s => s.name.toLowerCase().includes(q) || s.owner.toLowerCase().includes(q)
                );
                this.open = this.results.length > 0;
            },
            select(store) {
                this.selectedId = store.id;
                this.search = store.name + ' - ' + store.owner;
                this.open = false;
            }
        };
    }

    let bluetoothDevice = null;
    let bluetoothCharacteristic = null;

    async function previewThermal() {
        const statusText = document.createElement('div');
        statusText.style.cssText = 'position:fixed;top:20px;right:20px;background:#667eea;color:white;padding:15px 25px;border-radius:10px;z-index:9999;font-weight:bold;box-shadow:0 4px 20px rgba(0,0,0,0.3)';
        statusText.innerText = '⏳ جاري التحضير...';
        document.body.appendChild(statusText);

        try {
            // انتظار تحميل الخطوط
            try {
                await Promise.race([
                    document.fonts.ready,
                    new Promise((_, reject) => setTimeout(() => reject('timeout'), 3000))
                ]);
            } catch (e) {
                console.log('Cairo font not loaded, using fallback');
            }
            
            statusText.innerText = '📡 جاري تحميل بيانات الفاتورة...';
            const response = await fetch('{{ route('marketer.sales.invoice-data', $invoice) }}');
            const data = await response.json();

            statusText.innerText = '⚡ بناء الفاتورة...';
            const canvas = await buildInvoiceCanvas(data);
            
            // عرض المعاينة
            const modal = document.createElement('div');
            modal.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,0.9);z-index:10000;display:flex;align-items:center;justify-content:center;padding:20px';
            modal.innerHTML = `
                <div style="background:white;border-radius:20px;padding:20px;max-width:90%;max-height:90%;overflow:auto;position:relative">
                    <button onclick="this.closest('div').parentElement.remove()" style="position:absolute;top:10px;left:10px;background:#ef4444;color:white;border:none;border-radius:10px;padding:10px 20px;font-weight:bold;cursor:pointer;z-index:1">✕ إغلاق</button>
                    <div style="text-align:center">
                        <h2 style="margin-bottom:20px;color:#1f2937;font-size:24px;font-weight:bold">معاينة الفاتورة</h2>
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

            // انتظار تحميل الخطوط
            try {
                await Promise.race([
                    document.fonts.ready,
                    new Promise((_, reject) => setTimeout(() => reject('timeout'), 3000))
                ]);
            } catch (e) {
                console.log('Cairo font not loaded, using fallback');
            }

            statusText.innerText = '📡 جاري تحميل بيانات الفاتورة...';
            const response = await fetch('{{ route('marketer.sales.invoice-data', $invoice) }}');
            const data = await response.json();

            statusText.innerText = '⚡ بناء الفاتورة...';
            const canvas = await buildInvoiceCanvas(data);
            const rasterData = canvasToRaster(canvas);

            // محاولة إعادة الاتصال إذا كان الجهاز موجود لكن غير متصل
            if (bluetoothDevice && !bluetoothDevice.gatt.connected) {
                try {
                    statusText.innerText = '🔄 إعادة الاتصال...';
                    await bluetoothDevice.gatt.disconnect();
                    await new Promise(resolve => setTimeout(resolve, 500));
                    const server = await bluetoothDevice.gatt.connect();
                    const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
                    bluetoothCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');
                } catch (reconnectError) {
                    console.log('Failed to reconnect, will request new device');
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

    async function buildInvoiceCanvas(data) {
        const canvas = document.createElement('canvas');
        canvas.width = 576;
        
        // حساب الارتفاع الديناميكي بناءً على أسماء المنتجات
        let estimatedHeight = 950;
        data.items.forEach(item => {
            const lines = wrapText(item.name, 300, '24px Arial');
            estimatedHeight += Math.max(lines.length * 28, 40) + 16;
        });
        
        // إضافة مساحة لجدول الملخص والمجموع النهائي
        let summaryRows = 2;
        if (parseFloat(data.product_discount) > 0) summaryRows++;
        if (parseFloat(data.invoice_discount) > 0) summaryRows++;
        estimatedHeight += (summaryRows * 35) + 200;
        
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
        ctx.fillText('فاتورة مبيعات', 288, y + 28);
        
        y += 60;
        ctx.fillStyle = '#000000';
        ctx.font = '20px Cairo, Arial';
        ctx.fillText('رقم: ' + data.invoice_number, 288, y);
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
        
        y += 35;
        ctx.font = 'bold 22px Cairo, Arial';
        ctx.textAlign = 'right';
        ctx.fillText('المنتج', 490, y);
        ctx.textAlign = 'center';
        ctx.fillText('كمية', 205, y);
        ctx.fillText('سعر', 135, y);
        ctx.fillText('إجمالي', 60, y);
        
        y += 15;
        ctx.beginPath();
        ctx.moveTo(20, y);
        ctx.lineTo(556, y);
        ctx.stroke();
        
        ctx.font = '24px Cairo, Arial';
        data.items.forEach((item, index) => {
            const nameLines = wrapText(item.name, 300, '24px Cairo, Arial');
            const itemHeight = Math.max(nameLines.length * 28, 40) + 18;
            const startY = y + (itemHeight / 2) + 5;
            
            // رسم حدود الصف
            ctx.strokeRect(20, y, 536, itemHeight);
            // خطوط عمودية
            ctx.beginPath();
            ctx.moveTo(240, y);
            ctx.lineTo(240, y + itemHeight);
            ctx.moveTo(170, y);
            ctx.lineTo(170, y + itemHeight);
            ctx.moveTo(100, y);
            ctx.lineTo(100, y + itemHeight);
            ctx.stroke();
            
            // طباعة اسم المنتج على عدة أسطر (بدون غامق)
            ctx.textAlign = 'right';
            nameLines.forEach((line, idx) => {
                ctx.fillText(line, 550, y + 32 + (idx * 28));
            });
            
            // طباعة الكمية والسعر والإجمالي في المنتصف
            ctx.font = '20px Cairo, Arial';
            ctx.textAlign = 'center';
            ctx.fillText(item.quantity, 205, startY);
            ctx.fillText(item.price, 135, startY);
            ctx.fillText(item.total, 60, startY);
            ctx.font = '24px Cairo, Arial';
            
            y += itemHeight;
        });
        
        y += 25;
        ctx.beginPath();
        ctx.setLineDash([10, 5]);
        ctx.moveTo(30, y);
        ctx.lineTo(546, y);
        ctx.stroke();
        ctx.setLineDash([]);
        
        y += 35;
        ctx.font = '20px Cairo, Arial';
        ctx.lineWidth = 1;
        ctx.strokeStyle = '#000000';
        
        const summaryData = [
            ['عدد البضاعة', data.total_items],
            ['المجموع الفرعي', data.subtotal]
        ];
        
        if (parseFloat(data.product_discount) > 0) {
            summaryData.push(['خصم المنتجات (هدايا)', '- ' + data.product_discount]);
        }
        
        if (parseFloat(data.invoice_discount) > 0) {
            summaryData.push(['خصم الفاتورة', '- ' + data.invoice_discount]);
        }
        
        const rowHeight = 35;
        const startY = y;
        
        // رسم الجدول
        summaryData.forEach((row, index) => {
            const currentY = startY + (index * rowHeight);
            
            // رسم الحدود
            ctx.strokeRect(30, currentY, 516, rowHeight);
            ctx.beginPath();
            ctx.moveTo(288, currentY);
            ctx.lineTo(288, currentY + rowHeight);
            ctx.stroke();
            
            // النصوص
            ctx.textAlign = 'right';
            ctx.fillText(row[0], 530, currentY + 23);
            ctx.textAlign = 'center';
            // إضافة دينار للصف الثاني وما بعده
            const value = index === 0 ? row[1] : '‏' + row[1] + ' دينار';
            ctx.fillText(value, 169, currentY + 23);
        });
        
        y = startY + (summaryData.length * rowHeight) + 40;
        ctx.beginPath();
        ctx.moveTo(30, y);
        ctx.lineTo(546, y);
        ctx.lineWidth = 2;
        ctx.stroke();
        ctx.lineWidth = 1;
        
        y += 35;
        ctx.font = 'bold 24px Cairo, Arial';
        ctx.textAlign = 'right';
        ctx.fillText('المجموع النهائي', 480, y);
        ctx.font = 'bold 32px Cairo, Arial';
        ctx.textAlign = 'center';
        ctx.fillText('‏' + data.total + ' دينار', 169, y);
        
        y += 50;
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
        
        if (currentLine) {
            lines.push(currentLine);
        }
        
        // إذا كان السطر الواحد أطول من العرض المسموح، قسمه بالقوة
        const finalLines = [];
        lines.forEach(line => {
            const metrics = ctx.measureText(line);
            if (metrics.width > maxWidth) {
                let remaining = line;
                while (remaining.length > 0) {
                    let chars = remaining.length;
                    let testStr = remaining;
                    while (ctx.measureText(testStr).width > maxWidth && chars > 1) {
                        chars--;
                        testStr = remaining.substring(0, chars);
                    }
                    finalLines.push(testStr);
                    remaining = remaining.substring(chars);
                }
            } else {
                finalLines.push(line);
            }
        });
        
        return finalLines;
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

@if($invoice->status === 'approved' && $invoice->stamped_invoice_image)
    @include('shared.modals.documentation-image', [
        'imageUrl' => route('marketer.sales.documentation', $invoice->id),
        'invoiceNumber' => $invoice->invoice_number,
        'documentedAt' => $invoice->confirmed_at ?? $invoice->updated_at
    ])
@endif
@endsection
