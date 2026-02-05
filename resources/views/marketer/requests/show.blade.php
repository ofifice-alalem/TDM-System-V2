@extends('layouts.app')

@section('title', 'تفاصيل الطلب #' . $request->invoice_number)

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto space-y-8">
        
        {{-- Hero Header --}}
        <div class="flex flex-col md:flex-row justify-between items-end gap-6 animate-fade-in">
            <div class="space-y-2">
                <div class="flex items-center gap-2 text-primary-600 font-bold bg-primary-50 px-3 py-1 rounded-full w-fit text-xs tracking-wider uppercase">
                    <i data-lucide="hash" class="w-3 h-3"></i>
                    تفاصيل الفاتورة
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-gray-900 tracking-tight leading-tight">
                    {{ $request->invoice_number }}
                </h1>
                <div class="flex items-center gap-4 text-gray-500 text-sm font-medium">
                    <span class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-gray-100">
                        <i data-lucide="calendar" class="w-4 h-4 text-primary-500"></i>
                        {{ $request->created_at->format('Y-m-d') }}
                    </span>
                    <span class="flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-lg shadow-sm border border-gray-100">
                        <i data-lucide="clock" class="w-4 h-4 text-primary-500"></i>
                        {{ $request->created_at->format('h:i A') }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('marketer.requests.index') }}" class="group bg-white hover:bg-gray-50 text-gray-600 px-5 py-3 rounded-2xl font-bold text-sm shadow-sm border border-gray-200 transition-all flex items-center gap-2">
                    <i data-lucide="arrow-right" class="w-4 h-4 transition-transform group-hover:-translate-x-1"></i>
                    العودة
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Right Column (Main Content - WHITE BG) --}}
            <div class="lg:col-span-8 space-y-6 animate-slide-up" style="animation-delay: 0.1s">
                
                {{-- Products Table Container --}}
                <div class="bg-white rounded-[2rem] p-8 shadow-[0_2px_12px_-4px_rgba(0,0,0,0.05)] border border-gray-100 relative overflow-hidden">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="font-bold text-xl text-gray-900 flex items-center gap-3">
                                <span class="bg-primary-50 p-2.5 rounded-xl text-primary-600 shadow-sm border border-primary-100">
                                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                                </span>
                                المنتجات المطلوبة
                            </h2>
                            <p class="text-sm text-gray-400 mt-2 mr-14 font-medium">قائمة الأصناف المطلوب تجهيزها من المستودع</p>
                        </div>
                        <span class="bg-gray-50 border border-gray-200 text-gray-600 px-4 py-2 rounded-xl text-xs font-black shadow-sm">
                            {{ $request->items->count() }} أصناف
                        </span>
                    </div>

                    <div class="overflow-x-auto negative-margin-x pb-2">
                        <table class="w-full border-separate border-spacing-y-3">
                            <thead>
                                <tr class="text-xs text-gray-400 font-bold uppercase tracking-wider">
                                    <th class="px-6 py-2 text-right">المنتج</th>
                                    <th class="px-6 py-2 text-center w-40">الكمية المطلوبة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalQuantity = 0; @endphp
                                @foreach($request->items as $item)
                                @php 
                                    $totalQuantity += $item->quantity;
                                @endphp
                                <tr class="group hover:-translate-y-0.5 transition-transform duration-300">
                                    {{-- Product Info --}}
                                    <td class="px-6 py-5 bg-gray-50/50 rounded-r-2xl border border-gray-100 group-hover:bg-white group-hover:shadow-md group-hover:border-primary-100 transition-all">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-white flex items-center justify-center text-gray-400 border border-gray-100 shadow-sm group-hover:text-primary-600 transition-colors shrink-0">
                                                <i data-lucide="package" class="w-6 h-6"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 text-lg">{{ $item->product->name }}</div>
                                                <div class="text-xs text-gray-400 mt-1 font-mono flex items-center gap-2">
                                                    <span>{{ $item->product->barcode ?? '---' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Quantity --}}
                                    <td class="px-6 py-5 bg-gray-50/50 rounded-l-2xl border border-gray-100 group-hover:bg-white group-hover:shadow-md group-hover:border-primary-100 transition-all text-center">
                                        <span class="inline-flex items-center justify-center bg-white border border-gray-200 text-gray-900 font-black px-8 py-2 rounded-xl text-xl shadow-sm group-hover:border-primary-200 group-hover:text-primary-700 transition-all">
                                            {{ $item->quantity }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Total Banner (Quantity Focused) --}}
                    <div class="mt-8 bg-gray-900 rounded-3xl p-8 shadow-xl shadow-gray-200 text-white flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden group">
                        {{-- Background Decoration --}}
                        <div class="absolute top-0 left-0 w-64 h-64 bg-emerald-500 rounded-full mix-blend-overlay filter blur-[60px] opacity-20 -translate-x-1/2 -translate-y-1/2 transition-transform duration-700 group-hover:scale-125"></div>
                        
                        <div class="flex items-center gap-5 relative z-10">
                            <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-emerald-400 border border-white/10 shadow-inner">
                                <i data-lucide="layers" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <div class="text-base text-gray-100 font-bold uppercase tracking-wider mb-1">إجمالي القطع</div>
                                <div class="text-xs text-gray-500 font-medium">مجموع الكميات في هذا الطلب</div>
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
                @if($request->notes)
                    @php
                        $isRejected = $request->status === 'rejected';
                        $noteTitle = $isRejected ? 'ملاحظات أمين المخزن (سبب الرفض)' : 'ملاحظات المندوب';
                        $bgClass = $isRejected ? 'bg-red-50/50 border-red-100' : 'bg-white border-gray-100';
                        $titleColor = $isRejected ? 'text-red-800' : 'text-gray-800';
                        $textColor = $isRejected ? 'text-red-700' : 'text-gray-600';
                        $iconBg = $isRejected ? 'bg-red-100 text-red-600' : 'bg-primary-50 text-primary-600';
                        $icon = $isRejected ? 'shield-alert' : 'sticky-note';
                    @endphp

                    <div class="{{ $bgClass }} rounded-[1.5rem] shadow-sm border p-6 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                        {{-- Decorative Blur --}}
                        <div class="absolute top-0 right-0 w-32 h-32 {{ $isRejected ? 'bg-red-100' : 'bg-primary-50' }} rounded-full mix-blend-multiply filter blur-3xl opacity-40 -translate-y-1/2 translate-x-1/2 transition-transform group-hover:scale-110"></div>

                        <div class="relative z-10">
                            <h3 class="{{ $titleColor }} font-bold text-base mb-4 flex items-center gap-3">
                                <span class="p-2.5 rounded-xl {{ $iconBg }} shadow-sm">
                                    <i data-lucide="{{ $icon }}" class="w-5 h-5"></i>
                                </span>
                                {{ $noteTitle }}
                            </h3>
                            
                            <div class="{{ $isRejected ? 'bg-white/60 border-red-100' : 'bg-gray-50/50 border-gray-100' }} border backdrop-blur-sm rounded-2xl p-4 {{ $textColor }} text-sm font-medium leading-loose">
                                {{ $request->notes }}
                            </div>
                        </div>
                    </div>
                @endif

            </div>

            {{-- Left Column (Status & Actions - SLATE/GRAY BG to differentiate) --}}
            <div class="lg:col-span-4 space-y-6 animate-slide-up" style="animation-delay: 0.2s">
                
                {{-- Current Status Card (Slate BG) --}}
                <div class="bg-[#f8fafc] rounded-[1.5rem] border border-slate-200 p-6 relative overflow-hidden shadow-inner">
                    <div class="relative">
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <i data-lucide="activity" class="w-4 h-4"></i>
                            الحالة الحالية
                        </div>
                        
                        @php
                            $statusConfig = [
                                'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'border' => 'border-amber-200', 'icon' => 'loader', 'label' => 'قيد الانتظار والمعالجة'],
                                'approved' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-200', 'icon' => 'check-circle-2', 'label' => 'تمت الموافقة بنجاح'],
                                'documented' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'border' => 'border-emerald-200', 'icon' => 'shield-check', 'label' => 'تم التوثيق والأرشفة'],
                                'rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-200', 'icon' => 'x-circle', 'label' => 'تم رفض الطلب'],
                                'cancelled' => ['bg' => 'bg-gray-200', 'text' => 'text-gray-800', 'border' => 'border-gray-300', 'icon' => 'slash', 'label' => 'تم إلغاء الطلب'],
                            ];
                            $status = $statusConfig[$request->status] ?? $statusConfig['cancelled'];
                        @endphp

                        <div class="{{ $status['bg'] }} {{ $status['text'] }} {{ $status['border'] }} p-6 rounded-2xl border flex flex-col items-center justify-center text-center gap-3 shadow-sm mb-6 min-h-[160px]">
                            <div class="bg-white p-3 rounded-full shadow-sm animate-pulse">
                                <i data-lucide="{{ $status['icon'] }}" class="w-8 h-8"></i>
                            </div>
                            <div class="font-bold text-xl">{{ $status['label'] }}</div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="space-y-3">
                            @if($request->status === 'documented' && $request->stamped_image)
                                <button type="button" class="w-full bg-white border border-emerald-200 text-emerald-700 hover:bg-emerald-50 py-3.5 rounded-xl font-bold transition-all shadow-sm flex items-center justify-center gap-2 group" data-bs-toggle="modal" data-bs-target="#imageModal">
                                    <i data-lucide="image" class="w-5 h-5 text-emerald-500"></i>
                                    عــرض الفاتورة
                                </button>
                            @endif

                            @if($request->status !== 'pending')
                                <a href="{{ route('marketer.requests.pdf', $request) }}" target="_blank" class="w-full bg-gray-900 text-white hover:bg-gray-800 py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-gray-200 flex items-center justify-center gap-2 group">
                                    <i data-lucide="printer" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                                    طباعة PDF
                                </a>
                            @endif

                            @if(in_array($request->status, ['pending', 'approved']))
                                <div x-data="{ showCancel: false }" class="mt-4">
                                    {{-- Initial Cancel Button --}}
                                    <button 
                                        type="button" 
                                        x-show="!showCancel"
                                        @click="showCancel = true"
                                        class="w-full bg-white border-2 border-red-50 text-red-500 hover:bg-red-50 hover:border-red-100 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 group shadow-sm">
                                        <i data-lucide="x-circle" class="w-5 h-5 group-hover:rotate-90 transition-transform"></i>
                                        إلغاء الطلب
                                    </button>

                                    {{-- Inline Cancel Form (Slide Down Animation) --}}
                                    <div 
                                        x-show="showCancel" 
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 -translate-y-2"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        class="bg-red-50 rounded-2xl p-4 border border-red-100"
                                        style="display: none;">
                                        
                                        <form action="{{ route('marketer.requests.cancel', $request) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            
                                            <label class="block text-xs font-bold text-red-800 mb-2 mr-1">سبب الإلغاء:</label>
                                            <textarea 
                                                name="notes" 
                                                rows="2" 
                                                class="w-full bg-white border border-red-200 rounded-xl p-3 text-sm focus:outline-none focus:border-red-400 focus:ring-2 focus:ring-red-200 transition-all placeholder:text-red-300 mb-3" 
                                                placeholder="اكتب السبب هنا..." 
                                                required></textarea>
                                            
                                            <div class="flex gap-2">
                                                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2.5 rounded-xl text-sm transition-colors shadow-sm">
                                                    تأكيد الإلغاء
                                                </button>
                                                <button 
                                                    type="button" 
                                                    @click="showCancel = false"
                                                    class="px-4 py-2.5 bg-white border border-red-200 text-red-600 font-bold rounded-xl text-sm hover:bg-red-50 transition-colors">
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

                {{-- Timeline Card (Dynamic History) --}}
                <div class="bg-[#f8fafc] rounded-[1.5rem] border border-slate-200 p-6 shadow-inner">
                    <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2 text-sm uppercase tracking-wider">
                        <i data-lucide="history" class="w-4 h-4 text-primary-500"></i>
                        سجل العمليات
                    </h3>

                    <div class="relative space-y-8 pl-2">
                        {{-- Continuous Line --}}
                        <div class="absolute right-[19px] top-4 bottom-4 w-[2px] bg-slate-200 rounded-full"></div>

                        {{-- 1. Created (Always) --}}
                        <div class="relative flex items-start gap-4 group">
                            <div class="z-10 bg-white border-2 border-primary-500 w-10 h-10 rounded-full flex items-center justify-center text-primary-600 shadow-sm shrink-0">
                                <i data-lucide="plus" class="w-5 h-5"></i>
                            </div>
                            <div class="pt-1"> 
                                <div class="font-bold text-gray-900">إنشاء الطلب</div>
                                <div class="text-xs text-gray-500 mt-1 font-medium bg-white px-2 py-1 rounded border border-gray-100 inline-block">
                                    {{ $request->created_at->format('Y-m-d h:i A') }}
                                </div>
                                <div class="text-xs text-primary-600 mt-1">بواسطتك</div>
                            </div>
                        </div>

                        {{-- 2. Approved (If Happened) --}}
                        @if($request->approved_at)
                        <div class="relative flex items-start gap-4 animate-slide-up" style="animation-delay: 0.1s">
                            <div class="z-10 bg-white border-2 border-blue-500 w-10 h-10 rounded-full flex items-center justify-center text-blue-600 shadow-sm shrink-0">
                                <i data-lucide="check" class="w-5 h-5"></i>
                            </div>
                            <div class="pt-1">
                                <div class="font-bold text-gray-900">الموافقة الإدارية</div>
                                <div class="text-xs text-gray-500 mt-1 font-medium bg-white px-2 py-1 rounded border border-gray-100 inline-block">
                                    {{ $request->approved_at->format('Y-m-d h:i A') }}
                                </div>
                                <div class="text-xs text-blue-600 mt-1 font-bold">{{ $request->approver->full_name }}</div>
                            </div>
                        </div>
                        @endif

                        {{-- 3. Rejected (If Happened) --}}
                        @if($request->rejected_at)
                        <div class="relative flex items-start gap-4 animate-slide-up" style="animation-delay: 0.1s">
                            <div class="z-10 bg-white border-2 border-red-500 w-10 h-10 rounded-full flex items-center justify-center text-red-600 shadow-sm shrink-0">
                                <i data-lucide="x" class="w-5 h-5"></i>
                            </div>
                            <div class="pt-1">
                                <div class="font-bold text-gray-900">تم رفض الطلب</div>
                                <div class="text-xs text-gray-500 mt-1 font-medium bg-white px-2 py-1 rounded border border-gray-100 inline-block">
                                    {{ $request->rejected_at->format('Y-m-d h:i A') }}
                                </div>
                                @if($request->rejected_by)
                                    <div class="text-xs text-red-600 mt-1 font-bold">{{ $request->rejecter->full_name }}</div>
                                @endif
                            </div>
                        </div>
                        @endif

                        {{-- 4. Documented (If Happened) --}}
                        @if($request->documented_at)
                        <div class="relative flex items-start gap-4 animate-slide-up" style="animation-delay: 0.2s">
                            <div class="z-10 bg-white border-2 border-emerald-500 w-10 h-10 rounded-full flex items-center justify-center text-emerald-600 shadow-sm shrink-0">
                                <i data-lucide="file-check" class="w-5 h-5"></i>
                            </div>
                            <div class="pt-1">
                                <div class="font-bold text-gray-900">التوثيق والأرشفة</div>
                                <div class="text-xs text-gray-500 mt-1 font-medium bg-white px-2 py-1 rounded border border-gray-100 inline-block">
                                    {{ $request->documented_at->format('Y-m-d h:i A') }}
                                </div>
                                <div class="text-xs text-emerald-600 mt-1 font-bold">{{ $request->documenter->full_name }}</div>
                            </div>
                        </div>
                        @endif

                        {{-- 5. Cancelled (If Cancelled) --}}
                        @if($request->status === 'cancelled')
                        <div class="relative flex items-start gap-4 animate-slide-up" style="animation-delay: 0.1s">
                            <div class="z-10 bg-white border-2 border-gray-400 w-10 h-10 rounded-full flex items-center justify-center text-gray-500 shadow-sm shrink-0">
                                <i data-lucide="ban" class="w-5 h-5"></i>
                            </div>
                            <div class="pt-1">
                                <div class="font-bold text-gray-900">تم إلغاء الطلب</div>
                                <div class="text-xs text-gray-500 mt-1 font-medium bg-white px-2 py-1 rounded border border-gray-100 inline-block">
                                    {{ $request->updated_at->format('Y-m-d h:i A') }}
                                </div>
                            </div>
                        </div>
                        @endif

                        {{-- Pending State Indicator (If still pending) --}}
                        @if($request->status === 'pending')
                        <div class="relative flex items-start gap-4 opacity-50">
                            <div class="z-10 bg-slate-100 border-2 border-slate-300 w-10 h-10 rounded-full flex items-center justify-center text-slate-400 shrink-0">
                                <i data-lucide="loader" class="w-5 h-5 animate-spin"></i>
                            </div>
                            <div class="pt-1">
                                <div class="font-bold text-gray-500">بانتظار الإجراء...</div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- Image Modal (Modern) --}}
@if($request->stamped_image)
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 bg-transparent shadow-none">
            <div class="relative bg-white/90 backdrop-blur-xl rounded-[2rem] overflow-hidden shadow-2xl border border-white/50">
                <div class="p-6 flex justify-between items-center border-b border-gray-100">
                    <div>
                        <h5 class="font-black text-xl text-gray-900">صورة التوثيق</h5>
                        <p class="text-xs text-gray-500 mt-1">نسخة من الفاتورة الموقعة والمختومة</p>
                    </div>
                    <button type="button" class="w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-colors" data-bs-dismiss="modal">
                        <i data-lucide="x" class="w-5 h-5 text-gray-600"></i>
                    </button>
                </div>
                <div class="p-8 bg-gray-50/50 flex justify-center">
                    <img src="{{ asset('storage/' . $request->stamped_image) }}" class="max-h-[60vh] w-auto object-contain rounded-lg shadow-sm" alt="صورة التوثيق">
                </div>
                <div class="p-6 border-t border-gray-100 flex justify-end">
                    <a href="{{ asset('storage/' . $request->stamped_image) }}" target="_blank" class="bg-gray-900 text-white px-6 py-3 rounded-xl font-bold text-sm hover:bg-gray-800 transition-colors flex items-center gap-2">
                        <i data-lucide="external-link" class="w-4 h-4"></i>
                        فتح بجودة كاملة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif





@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush

@endsection
