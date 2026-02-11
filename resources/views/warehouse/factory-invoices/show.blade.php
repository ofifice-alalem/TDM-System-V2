@extends('layouts.app')

@section('title', 'تفاصيل فاتورة المصنع #' . $invoice->invoice_number)

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto space-y-8 px-4 lg:px-8">
        
        {{-- Header & Quick Actions --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in-down">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        فاتورة مصنع
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
                <a href="{{ route('warehouse.factory-invoices.index') }}" class="px-12 py-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors shadow-lg shadow-gray-200/50 dark:shadow-none flex items-center justify-center gap-2 flex-1 md:flex-auto">
                    <i data-lucide="arrow-right" class="w-5 h-5"></i>
                    عودة
                </a>
            </div>
        </div>

        {{-- Main Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Right Column (Main Content - Table) --}}
            <div class="lg:col-span-8 space-y-6 animate-slide-up order-2 lg:order-1">
                
                {{-- Products Table Container --}}
                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-4 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border relative overflow-hidden">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="font-bold text-xl text-gray-900 dark:text-white flex items-center gap-3">
                                <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                                    <i data-lucide="package-plus" class="w-5 h-5"></i>
                                </span>
                                المنتجات الواردة
                            </h2>
                            <p class="text-sm text-gray-500 dark:text-dark-muted mt-2 mr-14 font-medium">قائمة الأصناف الواردة من المصنع</p>
                        </div>
                        <span class="bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 px-4 py-2 rounded-xl text-xs font-black shadow-sm">
                            {{ $invoice->items->count() }} أصناف
                        </span>
                    </div>

                    <div class="overflow-x-auto negative-margin-x pb-2">
                        <table class="w-full border-separate border-spacing-y-3">
                            <thead>
                                <tr class="text-xs text-gray-400 dark:text-dark-muted font-bold uppercase tracking-wider">
                                    <th class="px-4 md:px-6 py-2 text-right w-[65%]">المنتج</th>
                                    <th class="px-4 md:px-6 py-2 text-center w-[35%]">الكمية</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalQuantity = 0; @endphp
                                @foreach($invoice->items as $item)
                                @php $totalQuantity += $item->quantity; @endphp
                                <tr class="group hover:-translate-y-0.5 transition-transform duration-300">
                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 rounded-r-2xl border border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all">
                                        <div class="flex items-center gap-4">
                                            <div class="hidden md:flex w-12 h-12 rounded-xl bg-white dark:bg-dark-card items-center justify-center text-gray-400 dark:text-gray-500 border border-gray-100 dark:border-dark-border shadow-sm group-hover:text-primary-600 dark:group-hover:text-accent-400 transition-colors shrink-0">
                                                <i data-lucide="package" class="w-6 h-6"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-bold text-gray-900 dark:text-gray-100 text-base md:text-lg truncate">{{ $item->product->name }}</div>
                                                <div class="text-xs text-gray-400 dark:text-dark-muted mt-1 font-mono flex items-center gap-2">
                                                    <span>{{ $item->product->barcode ?? '---' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-50/50 dark:bg-dark-bg/60 rounded-l-2xl border border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all text-center">
                                        <span class="inline-flex items-center justify-center bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-900 dark:text-gray-100 font-black px-8 py-2 rounded-xl text-xl shadow-sm group-hover:border-primary-200 dark:group-hover:border-accent-500/50 group-hover:text-primary-700 dark:group-hover:text-accent-400 transition-all">
                                            {{ $item->quantity }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Total Banner --}}
                    <div class="mt-8 bg-gray-900 dark:bg-black/40 rounded-3xl p-8 shadow-xl shadow-gray-200 dark:shadow-none text-white flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden group">
                        <div class="absolute top-0 left-0 w-64 h-64 bg-emerald-500 dark:bg-accent-500 rounded-full mix-blend-overlay dark:mix-blend-screen filter blur-[60px] opacity-20 dark:opacity-10 -translate-x-1/2 -translate-y-1/2 transition-transform duration-700 group-hover:scale-125"></div>
                        
                        <div class="flex items-center gap-5 relative z-10">
                            <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-emerald-400 dark:text-accent-400 border border-white/10 shadow-inner">
                                <i data-lucide="layers" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <div class="text-base text-gray-100 dark:text-gray-200 font-bold uppercase tracking-wider mb-1">إجمالي القطع</div>
                                <div class="text-xs text-gray-500 font-medium">مجموع الكميات في هذه الفاتورة</div>
                            </div>
                        </div>
                        <div class="relative z-10">
                            <div class="text-6xl font-black tracking-tighter text-white flex items-baseline gap-2">
                                {{ $totalQuantity }}
                                <span class="text-lg font-bold text-gray-500">قطعة</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Notes --}}
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

            {{-- Left Column (Status & Actions) --}}
            <div class="lg:col-span-4 space-y-6 animate-slide-up order-1 lg:order-2" style="animation-delay: 0.1s">
                
                {{-- Current Status Card --}}
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
                                'documented' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-600 dark:text-emerald-400', 'icon' => 'file-check', 'label' => 'موثق'],
                                'cancelled' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-600 dark:text-red-400', 'icon' => 'x-circle', 'label' => 'ملغى'],
                            ][$invoice->status];
                        @endphp
                        
                        <div class="inline-flex items-center justify-center p-4 rounded-full {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} mb-4 shadow-inner ring-4 ring-white dark:ring-dark-card">
                            <i data-lucide="{{ $statusConfig['icon'] }}" class="w-8 h-8"></i>
                        </div>
                        <h2 class="text-2xl font-black {{ $statusConfig['text'] }}">{{ $statusConfig['label'] }}</h2>
                        <p class="text-xs text-gray-400 dark:text-dark-muted mt-2 font-medium">آخر تحديث: {{ $invoice->updated_at->diffForHumans() }}</p>
                        
                        @if($invoice->status === 'documented' && $invoice->stamped_image)
                        <button type="button" onclick="showDocumentationModal()" class="mt-4 w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-white dark:bg-dark-bg border border-blue-200 dark:border-blue-900/30 text-blue-600 dark:text-blue-400 rounded-xl text-sm font-bold hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors shadow-sm">
                            <i data-lucide="image" class="w-5 h-5"></i>
                            عرض صورة التوثيق
                        </button>
                        @endif
                    </div>

                    {{-- Actions Area --}}
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-dark-border z-10 relative">
                        <a href="{{ route('warehouse.factory-invoices.pdf', $invoice) }}" target="_blank" class="w-full bg-gray-900 dark:bg-dark-bg text-white hover:bg-gray-800 dark:hover:bg-dark-card border border-transparent dark:border-dark-border py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-gray-200 dark:shadow-none flex items-center justify-center gap-2 group">
                            <i data-lucide="printer" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                            طباعة PDF
                        </a>
                    </div>
                </div>

                @if($invoice->status === 'pending' && request()->routeIs('warehouse.*'))
                {{-- Document Form --}}
                <div class="bg-white dark:bg-dark-card rounded-[1.5rem] border border-gray-200 dark:border-dark-border p-6 shadow-lg shadow-gray-200/50 dark:shadow-sm">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <i data-lucide="file-check" class="w-5 h-5 text-emerald-500"></i>
                        توثيق الفاتورة
                    </h3>
                    
                    <form method="POST" action="{{ route('warehouse.factory-invoices.document', $invoice) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">صورة الفاتورة المختومة</label>
                            <div class="relative">
                                <input type="file" name="stamped_image" id="stamped_image" accept="image/*" required class="hidden" onchange="previewImage(this)">
                                <label for="stamped_image" id="upload-label" class="flex items-center justify-center gap-3 w-full bg-gray-50 dark:bg-dark-bg border-2 border-dashed border-gray-300 dark:border-dark-border rounded-xl px-4 py-6 text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-dark-card hover:border-emerald-400 dark:hover:border-emerald-500 transition-all cursor-pointer group">
                                    <i data-lucide="upload" class="w-5 h-5 text-gray-400 group-hover:text-emerald-500 transition-colors"></i>
                                    <span>اضغط لاختيار الصورة</span>
                                </label>
                                <div id="image-preview" class="hidden mt-4 relative">
                                    <img id="preview-img" src="" alt="Preview" class="w-full rounded-xl border-2 border-emerald-500 shadow-lg">
                                    <button type="button" onclick="removeImage()" class="absolute top-2 left-2 w-8 h-8 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center transition-colors shadow-lg">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">يرجى رفع صورة واضحة للفاتورة المختومة</p>
                        </div>

                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                            <i data-lucide="check" class="w-5 h-5"></i>
                            توثيق وإضافة للمخزن
                        </button>
                    </form>
                </div>

                {{-- Cancel Form --}}
                <div class="bg-white dark:bg-dark-card rounded-[1.5rem] border border-gray-200 dark:border-dark-border p-6 shadow-lg shadow-gray-200/50 dark:shadow-sm">
                    <h3 class="font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                        <i data-lucide="x-circle" class="w-5 h-5 text-red-500"></i>
                        إلغاء الفاتورة
                    </h3>
                    
                    <div id="cancel-form-container">
                        <button type="button" onclick="showCancelForm()" class="w-full bg-red-600 hover:bg-red-700 text-white py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                            <i data-lucide="x" class="w-5 h-5"></i>
                            إلغاء الفاتورة
                        </button>
                    </div>

                    <form id="cancel-form" method="POST" action="{{ route('warehouse.factory-invoices.cancel', $invoice) }}" class="hidden">
                        @csrf
                        
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3">سبب الإلغاء</label>
                            <textarea name="cancellation_reason" rows="3" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-300 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all" placeholder="اكتب سبب إلغاء الفاتورة..."></textarea>
                        </div>

                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                                <i data-lucide="check" class="w-5 h-5"></i>
                                تأكيد الإلغاء
                            </button>
                            <button type="button" onclick="hideCancelForm()" class="flex-1 bg-gray-200 dark:bg-dark-bg hover:bg-gray-300 dark:hover:bg-dark-card text-gray-700 dark:text-gray-300 py-3.5 rounded-xl font-bold transition-all">
                                إلغاء
                            </button>
                        </div>
                    </form>
                </div>
                @endif

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
                                <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">بواسطة: {{ $invoice->keeper->full_name }}</p>
                                <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $invoice->created_at->format('Y-m-d h:i A') }}</span>
                            </div>
                        </div>

                        {{-- Step 2: Documented --}}
                        @if($invoice->documented_at)
                        <div class="relative flex items-start gap-4 animate-slide-up">
                            <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="file-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">التوثيق والإضافة للمخزن</h4>
                                <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">بواسطة: {{ $invoice->documenter->full_name }}</p>
                                <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $invoice->documented_at->format('Y-m-d h:i A') }}</span>
                            </div>
                        </div>
                        @elseif($invoice->cancelled_at)
                        <div class="relative flex items-start gap-4 animate-slide-up">
                            <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="x-circle" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">تم إلغاء الفاتورة</h4>
                                <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">بواسطة: {{ $invoice->canceller->full_name }}</p>
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1 font-medium">السبب: {{ $invoice->cancellation_reason }}</p>
                                <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $invoice->cancelled_at->format('Y-m-d h:i A') }}</span>
                            </div>
                        </div>
                        @else
                        <div class="relative flex items-start gap-4 opacity-50 grayscale">
                            <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-dark-bg text-gray-400 dark:text-gray-600 flex items-center justify-center shrink-0 z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-400 dark:text-gray-600 text-sm">التوثيق والإضافة للمخزن</h4>
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
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('preview-img').src = e.target.result;
                document.getElementById('image-preview').classList.remove('hidden');
                document.getElementById('upload-label').classList.add('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
            setTimeout(() => lucide.createIcons(), 100);
        }
    }

    function removeImage() {
        document.getElementById('stamped_image').value = '';
        document.getElementById('preview-img').src = '';
        document.getElementById('image-preview').classList.add('hidden');
        document.getElementById('upload-label').classList.remove('hidden');
    }

    function showCancelForm() {
        document.getElementById('cancel-form-container').classList.add('hidden');
        document.getElementById('cancel-form').classList.remove('hidden');
        setTimeout(() => lucide.createIcons(), 100);
    }

    function hideCancelForm() {
        document.getElementById('cancel-form').classList.add('hidden');
        document.getElementById('cancel-form-container').classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
@endsection

@if($invoice->status === 'documented' && $invoice->stamped_image)
    @include('shared.modals.documentation-image', [
        'imageUrl' => asset('storage/' . $invoice->stamped_image),
        'invoiceNumber' => $invoice->invoice_number,
        'documentedAt' => $invoice->documented_at
    ])
@endif
