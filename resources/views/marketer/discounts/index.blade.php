@extends('layouts.app')

@section('title', 'خصومات الفواتير')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="animate-fade-in-down">
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                    العروض المتاحة
                </span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                خصومات الفواتير
            </h1>
            <p class="text-gray-500 dark:text-dark-muted mt-2">الخصومات التلقائية المتاحة على فواتير البيع</p>
        </div>

        {{-- Main Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Discounts List --}}
            <div class="lg:col-span-8">
                {{-- Type Tabs --}}
                <div class="bg-white dark:bg-dark-card rounded-2xl p-2 shadow-sm border border-gray-200 dark:border-dark-border mb-6">
                    <div class="flex gap-2">
                        <a href="{{ route('marketer.discounts.index', ['type' => 'percentage']) }}" 
                            class="{{ (!request('type') || request('type') === 'percentage') ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-2 border-emerald-200 dark:border-emerald-800' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-4 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-2 flex-1 justify-center">
                            <i data-lucide="percent" class="w-4 h-4"></i>
                            نسبة مئوية
                        </a>
                        <a href="{{ route('marketer.discounts.index', ['type' => 'fixed']) }}" 
                            class="{{ request('type') === 'fixed' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border-2 border-blue-200 dark:border-blue-800' : 'bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border' }} px-4 py-2.5 rounded-xl font-bold transition-all text-sm flex items-center gap-2 flex-1 justify-center">
                            <i data-lucide="coins" class="w-4 h-4"></i>
                            مبلغ ثابت
                        </a>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-sm border border-gray-200 dark:border-dark-border animate-slide-up">
            
            @forelse($discounts as $discount)
                @php
                    $borderClass = 'border-green-400 dark:border-green-700';
                    $shadowClass = 'shadow-[inset_0_1px_15px_rgba(34,197,94,0.15)]';
                    $bgClass = 'from-green-50/30 to-white dark:from-green-950/10 dark:to-dark-card';
                @endphp
                <div class="bg-gradient-to-br {{ $bgClass }} rounded-2xl border-2 {{ $borderClass }} {{ $shadowClass }} mb-4 p-6 transition-all duration-300 group hover:shadow-[inset_0_1px_20px_rgba(34,197,94,0.2)]">
                    <div class="flex flex-col gap-4">
                        {{-- Header Row --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 px-3 py-1.5 rounded-lg text-xs font-black border border-amber-300 dark:border-amber-500">
                                    #{{ $discount->id }}
                                </span>
                                
                                <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                    نشط
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                <span>{{ $discount->creator->full_name }}</span>
                            </div>
                        </div>

                        {{-- Main Content --}}
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 flex-1">
                                <div class="bg-white dark:bg-dark-card border-2 border-gray-300 dark:border-dark-border px-4 sm:px-6 py-3 rounded-xl shadow-sm w-full sm:w-auto">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1 font-medium">الحد الأدنى</div>
                                    <div class="text-lg sm:text-xl font-black text-gray-900 dark:text-white">{{ number_format($discount->min_amount, 2) }}</div>
                                    <div class="text-xs text-gray-400 dark:text-gray-500 font-bold">دينار</div>
                                </div>
                                
                                <div class="hidden sm:flex items-center">
                                    <i data-lucide="arrow-left" class="w-6 h-6 text-gray-300 dark:text-gray-600"></i>
                                </div>
                                
                                @if($discount->discount_type === 'percentage')
                                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-900/30 border-2 border-emerald-200 dark:border-emerald-800 px-4 sm:px-6 py-3 rounded-xl shadow-sm w-full sm:w-auto">
                                        <div class="text-xs text-emerald-600 dark:text-emerald-400 mb-1 font-bold">نسبة الخصم</div>
                                        <div class="text-2xl sm:text-3xl font-black text-emerald-700 dark:text-emerald-400 flex items-baseline gap-1">
                                            {{ $discount->discount_percentage }}
                                            <span class="text-base sm:text-lg">%</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-900/30 border-2 border-blue-200 dark:border-blue-800 px-4 sm:px-6 py-3 rounded-xl shadow-sm w-full sm:w-auto">
                                        <div class="text-xs text-blue-600 dark:text-blue-400 mb-1 font-bold">مبلغ الخصم</div>
                                        <div class="text-xl sm:text-2xl font-black text-blue-700 dark:text-blue-400">{{ number_format($discount->discount_amount, 2) }}</div>
                                        <div class="text-xs text-blue-500 dark:text-blue-400 font-bold">دينار</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Footer Row --}}
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-3 border-t border-gray-200 dark:border-dark-border">
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-bold">فترة الصلاحية:</div>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                                <div class="flex items-center gap-2 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/30 px-3 sm:px-4 py-2 rounded-xl border-2 border-green-200 dark:border-green-800 shadow-sm">
                                    <i data-lucide="calendar-check" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                                    <span class="text-xs sm:text-sm font-black text-green-700 dark:text-green-300">{{ $discount->start_date->format('Y-m-d') }}</span>
                                </div>
                                <i data-lucide="arrow-left" class="w-5 h-5 text-gray-400 hidden sm:block"></i>
                                <i data-lucide="arrow-down" class="w-5 h-5 text-gray-400 sm:hidden mx-auto"></i>
                                <div class="flex items-center gap-2 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-900/30 px-3 sm:px-4 py-2 rounded-xl border-2 border-red-200 dark:border-red-800 shadow-sm">
                                    <i data-lucide="calendar-x" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
                                    <span class="text-xs sm:text-sm font-black text-red-700 dark:text-red-300">{{ $discount->end_date->format('Y-m-d') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="percent" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد خصومات متاحة</h3>
                    <p class="text-gray-500 dark:text-dark-muted">لا توجد خصومات نشطة حالياً</p>
                </div>
            @endforelse

            @if($discounts->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
                    {{ $discounts->links() }}
                </div>
            @endif
                </div>
            </div>

            {{-- Info Guide --}}
            <div class="lg:col-span-4">
                <div class="bg-white dark:bg-dark-card rounded-[1.5rem] border border-gray-200 dark:border-dark-border p-8 shadow-sm lg:sticky lg:top-[150px]">
                    <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-8 flex items-center gap-3">
                        <i data-lucide="info" class="w-6 h-6 text-primary-500"></i>
                        كيف تعمل الخصومات؟
                    </h3>
                    
                    <div class="space-y-6">
                        
                        <div class="relative">
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-sm">
                                    <i data-lucide="zap" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">تلقائي</h4>
                                    <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">يُطبق الخصم تلقائياً عند إنشاء فاتورة بيع</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm">
                                    <i data-lucide="calculator" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">حسب القيمة</h4>
                                    <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">إذا كانت قيمة الفاتورة >= الحد الأدنى</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 dark:bg-green-900/10 border border-green-200 dark:border-green-900/30 rounded-xl p-4 mt-6">
                            <div class="flex gap-3">
                                <i data-lucide="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400 shrink-0 mt-0.5"></i>
                                <div class="text-sm text-green-800 dark:text-green-300">
                                    <p class="font-bold mb-1">فائدة:</p>
                                    <p>كلما زادت قيمة الفاتورة، حصلت على خصم أكبر!</p>
                                </div>
                            </div>
                        </div>
                        
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
