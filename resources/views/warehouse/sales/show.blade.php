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
                <a href="{{ route('warehouse.sales.index') }}" class="px-12 py-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center justify-center gap-2 flex-1 md:flex-auto">
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
                    <div class="bg-gray-50/50 dark:bg-dark-bg/60 rounded-2xl p-6 border border-gray-100 dark:border-dark-border">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="text-xs text-gray-400 dark:text-dark-muted mb-2 font-bold uppercase tracking-wider">المسوق</p>
                                <p class="font-black text-gray-900 dark:text-white text-lg">{{ $invoice->marketer->full_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 dark:text-dark-muted mb-2 font-bold uppercase tracking-wider">اسم المتجر</p>
                                <p class="font-black text-gray-900 dark:text-white text-lg">{{ $invoice->store->name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 dark:text-dark-muted mb-2 font-bold uppercase tracking-wider">الرقم</p>
                                <p class="font-black text-gray-900 dark:text-white text-lg">{{ $invoice->store->phone ?? '---' }}</p>
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
                                            <div class="hidden md:flex w-12 h-12 rounded-xl bg-white dark:bg-dark-card items-center justify-center text-gray-400 dark:text-gray-500 border border-gray-100 dark:border-dark-border shadow-sm group-hover:text-primary-600 dark:group-hover:text-accent-400 transition-colors shrink-0">
                                                <i data-lucide="package" class="w-6 h-6"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-bold text-gray-900 dark:text-gray-100 text-base md:text-lg truncate">{{ $item->product->name }}</div>
                                                <div class="text-xs text-gray-400 dark:text-dark-muted mt-1 font-mono">{{ $item->product->barcode ?? '---' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 border-y border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all text-center">
                                        <span class="inline-flex items-center justify-center bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-900 dark:text-gray-100 font-black px-6 py-2 rounded-xl text-lg shadow-sm">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 border-y border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all text-center">
                                        <span class="inline-flex items-center justify-center bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 font-black px-6 py-2 rounded-xl text-lg shadow-sm">{{ $item->free_quantity }}</span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 border-y border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all text-center">
                                        <span class="font-bold text-gray-700 dark:text-gray-300">{{ number_format($item->unit_price, 2) }} دينار</span>
                                    </td>
                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 rounded-l-2xl border border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all text-center">
                                        <span class="font-black text-gray-900 dark:text-gray-100 text-lg">{{ number_format(($item->quantity + $item->free_quantity) * $item->unit_price, 2) }} دينار</span>
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
                                <span class="font-black text-base md:text-xl text-gray-900 dark:text-white">{{ number_format($invoice->subtotal + $invoice->product_discount, 2) }} دينار</span>
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
                        <a href="{{ route('marketer.sales.pdf', $invoice) }}" target="_blank" class="w-full bg-gray-900 dark:bg-dark-bg text-white hover:bg-gray-800 dark:hover:bg-dark-card border border-transparent dark:border-dark-border py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-gray-200 dark:shadow-none flex items-center justify-center gap-2 group mb-4">
                            <i data-lucide="printer" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                            طباعة PDF
                        </a>

                        @if($invoice->status === 'pending')
                            <form action="{{ route('warehouse.sales.approve', $invoice) }}" method="POST" enctype="multipart/form-data" class="mb-4">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">صورة الفاتورة المختومة</label>
                                    <div class="relative">
                                        <input type="file" name="stamped_invoice_image" id="invoice_image" accept="image/*" required class="hidden">
                                        <label for="invoice_image" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-300 dark:border-dark-border rounded-xl cursor-pointer bg-gray-50 dark:bg-dark-bg hover:bg-gray-100 dark:hover:bg-dark-card transition-all group">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <i data-lucide="upload" class="w-8 h-8 mb-2 text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors"></i>
                                                <p class="mb-1 text-sm text-gray-500 dark:text-gray-400 font-bold">
                                                    <span class="text-primary-600 dark:text-primary-400">اضغط للرفع</span> أو اسحب الصورة
                                                </p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500">PNG, JPG أو JPEG (الحد الأقصى 2MB)</p>
                                            </div>
                                        </label>
                                        <div id="preview" class="hidden mt-3 relative">
                                            <img id="preview_image" class="w-full h-40 object-cover rounded-xl border-2 border-gray-200 dark:border-dark-border">
                                            <button type="button" onclick="document.getElementById('invoice_image').value=''; document.getElementById('preview').classList.add('hidden')" class="absolute top-2 right-2 bg-red-500 hover:bg-red-600 text-white rounded-full p-1.5 shadow-lg transition-colors">
                                                <i data-lucide="x" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-xl font-bold transition-all shadow-lg flex items-center justify-center gap-2 group">
                                    <i data-lucide="check" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                                    توثيق الفاتورة
                                </button>
                            </form>

                            <div x-data="{ showReject: false }">
                                <button 
                                    type="button" 
                                    x-show="!showReject"
                                    @click="showReject = true"
                                    class="w-full bg-white dark:bg-dark-card border-2 border-red-50 dark:border-red-900/30 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:border-red-100 dark:hover:border-red-800 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 group shadow-sm">
                                    <i data-lucide="x-circle" class="w-5 h-5 group-hover:rotate-90 transition-transform"></i>
                                    رفض الفاتورة
                                </button>

                                <div 
                                    x-show="showReject" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="bg-red-50 dark:bg-red-900/10 rounded-2xl p-4 border border-red-100 dark:border-red-900/30"
                                    style="display: none;">
                                    
                                    <form action="{{ route('warehouse.sales.reject', $invoice) }}" method="POST">
                                        @csrf
                                        
                                        <label class="block text-xs font-bold text-red-800 dark:text-red-300 mb-2 mr-1">سبب الرفض:</label>
                                        <textarea 
                                            name="notes" 
                                            rows="2" 
                                            class="w-full bg-white dark:bg-dark-bg border border-red-200 dark:border-red-800 rounded-xl p-3 text-sm focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 dark:focus:ring-red-900/50 transition-all placeholder:text-red-300 dark:placeholder:text-red-700 dark:text-white mb-3" 
                                            placeholder="اكتب السبب هنا..." 
                                            required></textarea>
                                        
                                        <div class="flex gap-2">
                                            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors shadow-sm">
                                                تأكيد الرفض
                                            </button>
                                            <button 
                                                type="button" 
                                                @click="showReject = false"
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
        
        const input = document.getElementById('invoice_image');
        if (input) {
            input.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('preview_image').src = e.target.result;
                        document.getElementById('preview').classList.remove('hidden');
                        lucide.createIcons();
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endpush

@if($invoice->status === 'approved' && $invoice->stamped_invoice_image)
    @include('shared.modals.documentation-image', [
        'imageUrl' => route('warehouse.sales.documentation', $invoice->id),
        'invoiceNumber' => $invoice->invoice_number,
        'documentedAt' => $invoice->confirmed_at ?? $invoice->updated_at
    ])
@endif
@endsection
