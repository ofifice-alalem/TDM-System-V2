@extends('layouts.app')

@section('title', 'تفاصيل الطلب #' . $request->invoice_number)

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto space-y-8">
        
        {{-- Header & Quick Actions --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in-down">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                        طلب بضاعة
                    </span>
                    <span class="text-gray-400 dark:text-dark-muted text-xs font-mono tracking-wider">
                        {{ $request->created_at->format('Y-m-d h:i A') }}
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                    طلب #{{ $request->invoice_number }}
                </h1>
            </div>

            <div class="flex gap-3 w-full md:w-auto">
                <a href="{{ route('marketer.requests.index') }}" class="px-6 py-3.5 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-600 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors shadow-sm flex items-center justify-center gap-2 flex-1 md:flex-auto">
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    عودة
                </a>
            </div>
        </div>

        {{-- Main Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            {{-- Right Column (Main Content - Table) - ORDER 1 --}}
            <div class="lg:col-span-8 space-y-6 animate-slide-up">
                
                {{-- Products Table Container --}}
                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-[0_2px_12px_-4px_rgba(0,0,0,0.05)] dark:shadow-none border border-gray-100 dark:border-dark-border relative overflow-hidden">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="font-bold text-xl text-gray-900 dark:text-white flex items-center gap-3">
                                <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400 shadow-sm border border-primary-100 dark:border-primary-600/30">
                                    <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                                </span>
                                المنتجات المطلوبة
                            </h2>
                            <p class="text-sm text-gray-400 dark:text-dark-muted mt-2 mr-14 font-medium">قائمة الأصناف المطلوب تجهيزها من المستودع</p>
                        </div>
                        <span class="bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border text-gray-600 dark:text-gray-300 px-4 py-2 rounded-xl text-xs font-black shadow-sm">
                            {{ $request->items->count() }} أصناف
                        </span>
                    </div>

                    <div class="overflow-x-auto negative-margin-x pb-2">
                        <table class="w-full border-separate border-spacing-y-3">
                            <thead>
                                <tr class="text-xs text-gray-400 dark:text-dark-muted font-bold uppercase tracking-wider">
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
                                    <td class="px-6 py-5 bg-gray-50/50 dark:bg-dark-bg/60 rounded-r-2xl border border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-white dark:bg-dark-card flex items-center justify-center text-gray-400 dark:text-gray-500 border border-gray-100 dark:border-dark-border shadow-sm group-hover:text-primary-600 dark:group-hover:text-accent-400 transition-colors shrink-0">
                                                <i data-lucide="package" class="w-6 h-6"></i>
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 dark:text-gray-100 text-lg">{{ $item->product->name }}</div>
                                                <div class="text-xs text-gray-400 dark:text-dark-muted mt-1 font-mono flex items-center gap-2">
                                                    <span>{{ $item->product->barcode ?? '---' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Quantity --}}
                                    <td class="px-6 py-5 bg-gray-50/50 dark:bg-dark-bg/60 rounded-l-2xl border border-gray-100 dark:border-dark-border group-hover:bg-white dark:group-hover:bg-dark-card group-hover:shadow-md group-hover:border-primary-100 dark:group-hover:border-accent-500/30 transition-all text-center">
                                        <span class="inline-flex items-center justify-center bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-900 dark:text-gray-100 font-black px-8 py-2 rounded-xl text-xl shadow-sm group-hover:border-primary-200 dark:group-hover:border-accent-500/50 group-hover:text-primary-700 dark:group-hover:text-accent-400 transition-all">
                                            {{ $item->quantity }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Total Banner (Quantity Focused) --}}
                    <div class="mt-8 bg-gray-900 dark:bg-black/40 rounded-3xl p-8 shadow-xl shadow-gray-200 dark:shadow-none text-white flex flex-col md:flex-row justify-between items-center gap-6 relative overflow-hidden group">
                        {{-- Background Decoration --}}
                        <div class="absolute top-0 left-0 w-64 h-64 bg-emerald-500 dark:bg-accent-500 rounded-full mix-blend-overlay dark:mix-blend-screen filter blur-[60px] opacity-20 dark:opacity-10 -translate-x-1/2 -translate-y-1/2 transition-transform duration-700 group-hover:scale-125"></div>
                        
                        <div class="flex items-center gap-5 relative z-10">
                            <div class="w-16 h-16 bg-white/5 rounded-2xl flex items-center justify-center text-emerald-400 dark:text-accent-400 border border-white/10 shadow-inner">
                                <i data-lucide="layers" class="w-8 h-8"></i>
                            </div>
                            <div>
                                <div class="text-base text-gray-100 dark:text-gray-200 font-bold uppercase tracking-wider mb-1">إجمالي القطع</div>
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
                {{-- Notes (Moved Here) --}}
                @if($request->notes)
                    @php
                        $isRejected = $request->status === 'rejected';
                        $noteTitle = $isRejected ? 'ملاحظات أمين المخزن (سبب الرفض)' : 'ملاحظات المندوب';
                        $bgClass = $isRejected ? 'bg-red-50/50 dark:bg-red-900/10 border-red-100 dark:border-red-900/30' : 'bg-white dark:bg-dark-card border-gray-100 dark:border-dark-border';
                        $titleColor = $isRejected ? 'text-red-800 dark:text-red-400' : 'text-gray-800 dark:text-white';
                        $textColor = $isRejected ? 'text-red-700 dark:text-red-300' : 'text-gray-600 dark:text-gray-300';
                        $iconBg = $isRejected ? 'bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400' : 'bg-primary-50 dark:bg-primary-900/20 text-primary-600 dark:text-primary-400';
                        $icon = $isRejected ? 'shield-alert' : 'sticky-note';
                    @endphp

                    <div class="{{ $bgClass }} rounded-[1.5rem] shadow-sm border p-8 relative overflow-hidden group hover:shadow-md transition-all duration-300">
                        {{-- Decorative Blur --}}
                        <div class="absolute top-0 right-0 w-32 h-32 {{ $isRejected ? 'bg-red-100 dark:bg-red-900/20' : 'bg-primary-50 dark:bg-primary-900/20' }} rounded-full mix-blend-multiply dark:mix-blend-overlay filter blur-3xl opacity-40 -translate-y-1/2 translate-x-1/2 transition-transform group-hover:scale-110"></div>

                        <div class="relative z-10">
                            <h3 class="{{ $titleColor }} font-bold text-2xl mb-6 flex items-center gap-4">
                                <span class="p-3 rounded-xl {{ $iconBg }} shadow-sm">
                                    <i data-lucide="{{ $icon }}" class="w-7 h-7"></i>
                                </span>
                                {{ $noteTitle }}
                            </h3>
                            
                            <div class="{{ $isRejected ? 'bg-white/60 dark:bg-dark-bg/50 border-red-100 dark:border-red-900/30' : 'bg-gray-50/50 dark:bg-dark-bg/50 border-gray-100 dark:border-dark-border' }} border backdrop-blur-sm rounded-2xl p-6 {{ $textColor }} text-lg font-medium leading-loose">
                                {{ $request->notes }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Left Column (Status & Actions) - ORDER 2 --}}
            <div class="lg:col-span-4 space-y-6 animate-slide-up" style="animation-delay: 0.1s">
                
                {{-- Current Status Card --}}
                <div class="bg-gray-50 dark:bg-dark-card/50 rounded-[1.5rem] border border-gray-200 dark:border-dark-border p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-gray-200 dark:bg-dark-bg rounded-full mix-blend-multiply dark:mix-blend-overlay filter blur-3xl opacity-40 -translate-y-1/2 translate-x-1/2"></div>

                    <h3 class="text-gray-800 dark:text-gray-200 font-bold text-lg mb-6 flex items-center gap-2 relative z-10">
                        <i data-lucide="activity" class="w-5 h-5 text-gray-400 dark:text-dark-muted"></i>
                        حالة الطلب الحالية
                    </h3>

                    <div class="relative z-10 text-center py-4">
                        @php
                            $statusConfig = [
                                'pending' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-600 dark:text-amber-400', 'icon' => 'clock', 'label' => 'قيد المراجعة'],
                                'approved' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-600 dark:text-emerald-400', 'icon' => 'check-circle', 'label' => 'تمت الموافقة'],
                                'rejected' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-600 dark:text-red-400', 'icon' => 'x-circle', 'label' => 'مرفوض'],
                                'documented' => ['bg' => 'bg-blue-100 dark:bg-accent-900/30', 'text' => 'text-blue-600 dark:text-accent-400', 'icon' => 'file-check', 'label' => 'مؤرشف '],
                                'cancelled' => ['bg' => 'bg-gray-100 dark:bg-gray-800', 'text' => 'text-gray-500', 'icon' => 'slash', 'label' => 'ملغي'],
                            ][$request->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'icon' => 'help-circle', 'label' => $request->status];
                        @endphp
                        
                        <div class="inline-flex items-center justify-center p-4 rounded-full {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} mb-4 shadow-inner ring-4 ring-white dark:ring-dark-card">
                            <i data-lucide="{{ $statusConfig['icon'] }}" class="w-8 h-8"></i>
                        </div>
                        <h2 class="text-2xl font-black {{ $statusConfig['text'] }}">{{ $statusConfig['label'] }}</h2>
                        <p class="text-xs text-gray-400 dark:text-dark-muted mt-2 font-medium">آخر تحديث: {{ $request->updated_at->diffForHumans() }}</p>
                    </div>

                    {{-- Actions Area --}}
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-dark-border z-10 relative">
                         @if($request->status !== 'pending')
                            <a href="{{ route('marketer.requests.pdf', $request) }}" target="_blank" class="w-full bg-gray-900 dark:bg-dark-bg text-white hover:bg-gray-800 dark:hover:bg-dark-card border border-transparent dark:border-dark-border py-3.5 rounded-xl font-bold transition-all shadow-lg shadow-gray-200 dark:shadow-none flex items-center justify-center gap-2 group">
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
                                    class="w-full bg-white dark:bg-dark-card border-2 border-red-50 dark:border-red-900/30 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:border-red-100 dark:hover:border-red-800 py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2 group shadow-sm">
                                    <i data-lucide="x-circle" class="w-5 h-5 group-hover:rotate-90 transition-transform"></i>
                                    إلغاء الطلب
                                </button>

                                {{-- Inline Cancel Form (Slide Down Animation) --}}
                                <div 
                                    x-show="showCancel" 
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    class="bg-red-50 dark:bg-red-900/10 rounded-2xl p-4 border border-red-100 dark:border-red-900/30"
                                    style="display: none;">
                                    
                                    <form action="{{ route('marketer.requests.cancel', $request) }}" method="POST">
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

                {{-- Activity Timeline --}}
                <div class="bg-white dark:bg-dark-card rounded-[1.5rem] border border-gray-100 dark:border-dark-border p-6 shadow-sm">
                    <h3 class="font-bold text-gray-800 dark:text-white mb-6 flex items-center gap-2">
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
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">تم إنشاء الطلب</h4>
                                <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">بواسطة: {{ $request->user?->full_name ?? 'غير معروف' }}</p>
                                <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $request->created_at->format('Y-m-d h:i A') }}</span>
                            </div>
                        </div>

                        {{-- Step 2: Approved --}}
                        @if($request->approved_at)
                        <div class="relative flex items-start gap-4 animate-slide-up">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-accent-900/30 text-blue-600 dark:text-accent-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">تمت موافقة المخزن</h4>
                                <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">بواسطة: {{ $request->approvedBy?->full_name ?? 'النظام' }}</p>
                                <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $request->approved_at->format('Y-m-d h:i A') }}</span>
                            </div>
                        </div>
                        @elseif($request->status == 'rejected')
                             <div class="relative flex items-start gap-4 animate-slide-up">
                                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                    <i data-lucide="x" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-red-800 dark:text-red-400 text-sm">تم رفض الطلب</h4>
                                    <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">بواسطة: {{ $request->approvedBy ? $request->approvedBy->full_name : 'المشرف' }}</p>
                                     <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $request->updated_at->format('Y-m-d h:i A') }}</span>
                                </div>
                            </div>
                        @else
                          <div class="relative flex items-start gap-4 opacity-50 grayscale">
                             <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-dark-bg text-gray-400 dark:text-gray-600 flex items-center justify-center shrink-0 z-10 border-2 border-white dark:border-dark-card relative overflow-hidden">
                                <div class="absolute inset-0 bg-gray-200/50 dark:bg-gray-800/50 animate-pulse"></div>
                                <i data-lucide="clock" class="w-4 h-4"></i>
                            </div>
                             <div>
                                <h4 class="font-bold text-gray-400 dark:text-gray-600 text-sm">موافقة المخزن</h4>
                                <div class="text-xs text-gray-400 dark:text-gray-600 mt-1">في الانتظار...</div>
                            </div>
                        </div>
                        @endif

                        {{-- Step 3: Documented --}}
                         @if($request->documented_at)
                        <div class="relative flex items-start gap-4 animate-slide-up">
                            <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="file-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white text-sm">التوثيق والأرشفة</h4>
                                <p class="text-xs text-gray-500 dark:text-dark-muted mt-1">بواسطة: {{ $request->documentedBy?->full_name ?? 'النظام' }}</p>
                                <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $request->documented_at->format('Y-m-d h:i A') }}</span>
                            </div>
                        </div>
                         @else
                         <div class="relative flex items-start gap-4 opacity-50 grayscale">
                             <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-dark-bg text-gray-400 dark:text-gray-600 flex items-center justify-center shrink-0 z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                            </div>
                             <div>
                                <h4 class="font-bold text-gray-400 dark:text-gray-600 text-sm">التوثيق والأرشفة</h4>
                            </div>
                        </div>
                        @endif

                        {{-- Step 4: Cancelled --}}
                        @if($request->status == 'cancelled')
                         <div class="relative flex items-start gap-4 animate-slide-up">
                            <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-600 dark:text-gray-300 flex items-center justify-center shrink-0 shadow-sm z-10 border-2 border-white dark:border-dark-card">
                                <i data-lucide="slash" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-600 dark:text-gray-300 text-sm">تم إلغاء الطلب</h4>
                                 <span class="text-[10px] bg-gray-100 dark:bg-dark-bg px-2 py-0.5 rounded text-gray-500 dark:text-dark-muted mt-2 inline-block font-mono">{{ $request->updated_at->format('Y-m-d h:i A') }}</span>
                            </div>
                        </div>
                        @endif
                        
                    </div>
                </div>



            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
