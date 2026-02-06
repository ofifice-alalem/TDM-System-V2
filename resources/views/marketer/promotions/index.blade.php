@extends('layouts.app')

@section('title', 'العروض الترويجية')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="animate-fade-in-down">
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-purple-100 dark:bg-purple-600/20 text-purple-600 dark:text-purple-400 px-3 py-1 rounded-lg text-xs font-bold border border-purple-100 dark:border-purple-600/30">
                    العروض المتاحة
                </span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                العروض الترويجية
            </h1>
            <p class="text-gray-500 dark:text-dark-muted mt-2">اشتري X واحصل على Y مجاناً</p>
        </div>

        {{-- Main Layout Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Promotions List --}}
            <div class="lg:col-span-8">
                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border animate-slide-up">
            
            @forelse($promotions as $promotion)
                @php
                    $borderClass = 'border-green-300 dark:border-green-800 shadow-green-200/30 dark:shadow-green-900/20';
                    $bgClass = 'from-gray-50 to-white dark:from-dark-bg dark:to-dark-card';
                @endphp
                <div class="bg-gradient-to-br {{ $bgClass }} rounded-2xl border-2 {{ $borderClass }} mb-4 p-6 hover:shadow-lg transition-all group">
                    <div class="flex flex-col gap-4">
                        {{-- Header Row --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <span class="bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400 px-3 py-1.5 rounded-lg text-xs font-black border border-amber-300 dark:border-amber-500">
                                    #{{ $promotion->id }}
                                </span>
                                
                                <span class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-3 py-1.5 rounded-lg text-xs font-bold flex items-center gap-1.5">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                    نشط
                                </span>
                            </div>

                            <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400">
                                <i data-lucide="user" class="w-3.5 h-3.5"></i>
                                <span>{{ $promotion->creator->full_name }}</span>
                            </div>
                        </div>

                        {{-- Main Content --}}
                        <div class="flex flex-col gap-4">
                            {{-- Product Info - Full Width --}}
                            <div class="bg-gradient-to-r from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-900/30 border-2 border-purple-200 dark:border-purple-800 px-5 py-3 rounded-xl shadow-sm">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-purple-200 dark:bg-purple-800 rounded-lg flex items-center justify-center shrink-0">
                                        <i data-lucide="package" class="w-4 h-4 text-purple-700 dark:text-purple-300"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-xs text-purple-600 dark:text-purple-400 font-medium mb-0.5">المنتج</div>
                                        <div class="text-sm font-black text-purple-900 dark:text-purple-200">{{ $promotion->product->name }}</div>
                                    </div>
                                </div>
                            </div>

                            {{-- Quantities Row --}}
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                                <div class="flex-1 bg-white dark:bg-dark-card border-2 border-gray-300 dark:border-dark-border px-5 py-4 rounded-xl shadow-sm">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-2 font-medium flex items-center gap-1.5">
                                        <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                                        اشتري
                                    </div>
                                    <div class="flex items-baseline gap-2">
                                        <div class="text-3xl sm:text-4xl font-black text-gray-900 dark:text-white">{{ $promotion->min_quantity }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400 font-bold">قطعة</div>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-center">
                                    <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-dark-bg flex items-center justify-center">
                                        <i data-lucide="arrow-left" class="w-5 h-5 text-gray-400 dark:text-gray-600 hidden sm:block"></i>
                                        <i data-lucide="arrow-down" class="w-5 h-5 text-gray-400 dark:text-gray-600 sm:hidden"></i>
                                    </div>
                                </div>
                                
                                <div class="flex-1 bg-gradient-to-br from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/30 border-2 border-green-200 dark:border-green-800 px-5 py-4 rounded-xl shadow-sm">
                                    <div class="text-xs text-green-600 dark:text-green-400 mb-2 font-bold flex items-center gap-1.5">
                                        <i data-lucide="gift" class="w-3.5 h-3.5"></i>
                                        واحصل على
                                    </div>
                                    <div class="flex items-baseline gap-2">
                                        <div class="text-3xl sm:text-4xl font-black text-green-700 dark:text-green-400">{{ $promotion->free_quantity }}</div>
                                        <div class="text-sm text-green-600 dark:text-green-400 font-bold">مجاناً</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Row --}}
                        <div class="flex flex-col sm:flex-row sm:items-center gap-3 pt-3 border-t border-gray-200 dark:border-dark-border">
                            <div class="text-xs text-gray-500 dark:text-gray-400 font-bold">فترة الصلاحية:</div>
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
                                <div class="flex items-center gap-2 bg-gradient-to-r from-green-50 to-green-100 dark:from-green-900/20 dark:to-green-900/30 px-3 sm:px-4 py-2 rounded-xl border-2 border-green-200 dark:border-green-800 shadow-sm">
                                    <i data-lucide="calendar-check" class="w-4 h-4 text-green-600 dark:text-green-400"></i>
                                    <span class="text-xs sm:text-sm font-black text-green-700 dark:text-green-300">{{ $promotion->start_date->format('Y-m-d') }}</span>
                                </div>
                                <i data-lucide="arrow-left" class="w-5 h-5 text-gray-400 hidden sm:block"></i>
                                <i data-lucide="arrow-down" class="w-5 h-5 text-gray-400 sm:hidden mx-auto"></i>
                                <div class="flex items-center gap-2 bg-gradient-to-r from-red-50 to-red-100 dark:from-red-900/20 dark:to-red-900/30 px-3 sm:px-4 py-2 rounded-xl border-2 border-red-200 dark:border-red-800 shadow-sm">
                                    <i data-lucide="calendar-x" class="w-4 h-4 text-red-600 dark:text-red-400"></i>
                                    <span class="text-xs sm:text-sm font-black text-red-700 dark:text-red-300">{{ $promotion->end_date->format('Y-m-d') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="gift" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا توجد عروض متاحة</h3>
                    <p class="text-gray-500 dark:text-dark-muted">لا توجد عروض ترويجية نشطة حالياً</p>
                </div>
            @endforelse

            @if($promotions->hasPages())
                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border">
                    {{ $promotions->links() }}
                </div>
            @endif
                </div>
            </div>

            {{-- Info Guide --}}
            <div class="lg:col-span-4">
                <div class="bg-white dark:bg-dark-card rounded-[1.5rem] border border-gray-200 dark:border-dark-border p-8 shadow-lg shadow-gray-200/50 dark:shadow-sm lg:sticky lg:top-[150px]">
                    <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-8 flex items-center gap-3">
                        <i data-lucide="info" class="w-6 h-6 text-purple-500"></i>
                        كيف تعمل العروض؟
                    </h3>
                    
                    <div class="space-y-6">
                        
                        <div class="relative">
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center shrink-0 shadow-sm">
                                    <i data-lucide="zap" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">تلقائي</h4>
                                    <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">يُطبق العرض تلقائياً عند إنشاء فاتورة بيع</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 flex items-center justify-center shrink-0 shadow-sm">
                                    <i data-lucide="gift" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">كمية مجانية</h4>
                                    <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">تحصل على كمية مجانية عند تحقيق الشرط</p>
                                </div>
                            </div>
                        </div>

                        <div class="relative">
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0 shadow-sm">
                                    <i data-lucide="calculator" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 dark:text-white text-base mb-1">حسب الكمية</h4>
                                    <p class="text-sm text-gray-500 dark:text-dark-muted leading-relaxed">كلما زادت الكمية، زادت الهدايا المجانية</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-900/30 rounded-xl p-4 mt-6">
                            <div class="flex gap-3">
                                <i data-lucide="info" class="w-5 h-5 text-purple-600 dark:text-purple-400 shrink-0 mt-0.5"></i>
                                <div class="text-sm text-purple-800 dark:text-purple-300">
                                    <p class="font-bold mb-1">مثال:</p>
                                    <p>اشتري 10 قطع واحصل على 2 مجاناً. عند شراء 25 قطعة = 4 مجانية (25÷10=2، 2×2=4)</p>
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
