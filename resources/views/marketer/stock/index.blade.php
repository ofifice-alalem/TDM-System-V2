@extends('layouts.app')

@section('title', 'مخزوني')

@section('content')
<div class="min-h-screen py-8">
    <div class="max-w-[1600px] mx-auto space-y-8 px-2">
        
        {{-- Header --}}
        <div class="animate-fade-in-down">
            <div class="flex items-center gap-3 mb-2">
                <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                    إدارة المخزون
                </span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white tracking-tight leading-tight">
                مخزوني
            </h1>
        </div>

        {{-- Main Grid Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Right Side: Tabs & Table --}}
            <div class="lg:col-span-8 space-y-6 animate-slide-up order-2 lg:order-1">
                {{-- Tabs --}}
                <div class="bg-white dark:bg-dark-card rounded-2xl p-2 shadow-lg shadow-gray-200/50 dark:shadow-none border border-gray-200 dark:border-dark-border">
                    <div class="flex gap-2">
                        <button onclick="switchTab('actual')" id="tab-actual" class="flex-1 px-6 py-3 rounded-xl font-bold transition-all text-sm flex items-center gap-2 justify-center bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border-2 border-blue-200 dark:border-blue-800">
                            <i data-lucide="package" class="w-4 h-4"></i>
                            المخزون الفعلي
                        </button>
                        <button onclick="switchTab('reserved')" id="tab-reserved" class="flex-1 px-6 py-3 rounded-xl font-bold transition-all text-sm flex items-center gap-2 justify-center bg-gray-100 dark:bg-dark-bg text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-dark-border">
                            <i data-lucide="clock" class="w-4 h-4"></i>
                            المخزون المحجوز
                        </button>
                    </div>
                </div>

                {{-- Tables --}}
                <div>
                    {{-- Actual Stock Table --}}
                    <div id="content-actual" class="bg-white dark:bg-dark-card rounded-[2rem] p-4 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
                        @if($actualStock->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full border-separate border-spacing-y-3">
                                    <thead>
                                        <tr class="text-xs text-gray-400 dark:text-dark-muted font-bold uppercase tracking-wider">
                                            <th class="px-4 md:px-6 py-2 text-center w-[10%]">#</th>
                                            <th class="px-4 md:px-6 py-2 text-right w-[70%]">المنتج</th>
                                            <th class="px-4 md:px-6 py-2 text-center w-[20%]">الكمية</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($actualStock as $index => $item)
                                        <tr class="group hover:-translate-y-0.5 transition-transform duration-300">
                                            <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-100 dark:bg-dark-bg/60 rounded-r-2xl border-2 border-gray-200 dark:border-dark-border group-hover:bg-blue-50 dark:group-hover:bg-dark-card group-hover:shadow-lg group-hover:border-blue-200 dark:group-hover:border-accent-500/30 transition-all text-center">
                                                <span class="text-gray-700 dark:text-gray-400 font-black text-base">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-100 dark:bg-dark-bg/60 border-y-2 border-gray-200 dark:border-dark-border group-hover:bg-blue-50 dark:group-hover:bg-dark-card group-hover:shadow-lg group-hover:border-blue-200 dark:group-hover:border-accent-500/30 transition-all">
                                                <div class="font-black text-gray-900 dark:text-gray-100 text-xs md:text-base">{{ $item->name }}</div>
                                            </td>
                                            <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-100 dark:bg-dark-bg/60 rounded-l-2xl border-2 border-gray-200 dark:border-dark-border group-hover:bg-blue-50 dark:group-hover:bg-dark-card group-hover:shadow-lg group-hover:border-blue-200 dark:group-hover:border-accent-500/30 transition-all text-center">
                                                <span class="inline-flex items-center justify-center bg-gray-600 dark:bg-blue-900/30 border-2 border-gray-700 dark:border-blue-800 text-white dark:text-blue-400 font-black px-3 md:px-6 py-1 md:py-2 rounded-xl text-sm md:text-lg shadow-md">
                                                    {{ $item->quantity }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 font-bold">الإجمالي:</span>
                                <span class="text-3xl font-black text-gray-900 dark:text-white">{{ $totalActual }}</span>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="inbox" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا يوجد مخزون</h3>
                                <p class="text-gray-500 dark:text-dark-muted">لا يوجد مخزون فعلي حالياً</p>
                            </div>
                        @endif
                    </div>

                    {{-- Reserved Stock Table --}}
                    <div id="content-reserved" class="hidden bg-white dark:bg-dark-card rounded-[2rem] p-4 md:p-8 shadow-xl shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border">
                        @if($reservedStock->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full border-separate border-spacing-y-3">
                                    <thead>
                                        <tr class="text-xs text-gray-400 dark:text-dark-muted font-bold uppercase tracking-wider">
                                            <th class="px-4 md:px-6 py-2 text-center w-[10%]">#</th>
                                            <th class="px-4 md:px-6 py-2 text-right w-[70%]">المنتج</th>
                                            <th class="px-4 md:px-6 py-2 text-center w-[20%]">الكمية</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reservedStock as $index => $item)
                                        <tr class="group hover:-translate-y-0.5 transition-transform duration-300">
                                            <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-100 dark:bg-dark-bg/60 rounded-r-2xl border-2 border-gray-200 dark:border-dark-border group-hover:bg-amber-50 dark:group-hover:bg-dark-card group-hover:shadow-lg group-hover:border-amber-200 dark:group-hover:border-accent-500/30 transition-all text-center">
                                                <span class="text-gray-700 dark:text-gray-400 font-black text-base">{{ $index + 1 }}</span>
                                            </td>
                                            <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-100 dark:bg-dark-bg/60 border-y-2 border-gray-200 dark:border-dark-border group-hover:bg-amber-50 dark:group-hover:bg-dark-card group-hover:shadow-lg group-hover:border-amber-200 dark:group-hover:border-accent-500/30 transition-all">
                                                <div class="font-black text-gray-900 dark:text-gray-100 text-xs md:text-base">{{ $item->name }}</div>
                                            </td>
                                            <td class="px-4 md:px-6 py-4 md:py-5 bg-gray-100 dark:bg-dark-bg/60 rounded-l-2xl border-2 border-gray-200 dark:border-dark-border group-hover:bg-amber-50 dark:group-hover:bg-dark-card group-hover:shadow-lg group-hover:border-amber-200 dark:group-hover:border-accent-500/30 transition-all text-center">
                                                <span class="inline-flex items-center justify-center bg-amber-500 dark:bg-amber-900/30 border-2 border-amber-600 dark:border-amber-800 text-white dark:text-amber-400 font-black px-3 md:px-6 py-1 md:py-2 rounded-xl text-sm md:text-lg shadow-md">
                                                    {{ $item->quantity }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-dark-border flex justify-between items-center">
                                <span class="text-gray-600 dark:text-gray-400 font-bold">الإجمالي:</span>
                                <span class="text-3xl font-black text-gray-900 dark:text-white">{{ $totalReserved }}</span>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="w-20 h-20 bg-gray-100 dark:bg-dark-bg rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i data-lucide="inbox" class="w-10 h-10 text-gray-400 dark:text-gray-600"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">لا يوجد مخزون محجوز</h3>
                                <p class="text-gray-500 dark:text-dark-muted">لا يوجد مخزون محجوز حالياً</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Left Side: Stats Cards --}}
            <div class="lg:col-span-4 space-y-6 animate-slide-up order-1 lg:order-2 lg:sticky lg:top-[150px]">
                {{-- Actual Stock Card --}}
                <div class="bg-gradient-to-br from-blue-50 to-white dark:from-blue-900/20 dark:to-dark-card rounded-[2rem] p-6 md:p-8 shadow-xl shadow-blue-100/50 dark:shadow-none border-2 border-blue-100 dark:border-blue-800/50 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-200 dark:bg-blue-900/20 rounded-full mix-blend-multiply dark:mix-blend-overlay filter blur-3xl opacity-60 -translate-y-1/2 translate-x-1/2 transition-transform group-hover:scale-125"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-xl bg-blue-500 dark:bg-blue-600 text-white shadow-lg">
                                <i data-lucide="package" class="w-8 h-8"></i>
                            </div>
                        </div>
                        <h3 class="text-blue-600 dark:text-blue-400 text-sm font-bold mb-2">المخزون الفعلي</h3>
                        <div class="text-5xl font-black text-blue-700 dark:text-blue-300">{{ $totalActual }}</div>
                        <p class="text-xs text-blue-500 dark:text-blue-400/70 mt-2 font-semibold">إجمالي القطع المتاحة</p>
                    </div>
                </div>

                {{-- Reserved Stock Card --}}
                <div class="bg-gradient-to-br from-amber-50 to-white dark:from-amber-900/20 dark:to-dark-card rounded-[2rem] p-6 md:p-8 shadow-xl shadow-amber-100/50 dark:shadow-none border-2 border-amber-100 dark:border-amber-800/50 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-amber-200 dark:bg-amber-900/20 rounded-full mix-blend-multiply dark:mix-blend-overlay filter blur-3xl opacity-60 -translate-y-1/2 translate-x-1/2 transition-transform group-hover:scale-125"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 rounded-xl bg-amber-500 dark:bg-amber-600 text-white shadow-lg">
                                <i data-lucide="clock" class="w-8 h-8"></i>
                            </div>
                        </div>
                        <h3 class="text-amber-600 dark:text-amber-400 text-sm font-bold mb-2">المخزون المحجوز</h3>
                        <div class="text-5xl font-black text-amber-700 dark:text-amber-300">{{ $totalReserved }}</div>
                        <p class="text-xs text-amber-500 dark:text-amber-400/70 mt-2 font-semibold">القطع المحجوزة للطلبات</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function switchTab(tab) {
    const tabs = ['actual', 'reserved'];
    tabs.forEach(t => {
        const tabBtn = document.getElementById('tab-' + t);
        const content = document.getElementById('content-' + t);
        
        if (t === tab) {
            tabBtn.classList.remove('bg-gray-100', 'dark:bg-dark-bg', 'text-gray-700', 'dark:text-gray-300');
            tabBtn.classList.add(t === 'actual' ? 'bg-blue-100' : 'bg-amber-100', t === 'actual' ? 'dark:bg-blue-900/30' : 'dark:bg-amber-900/30', t === 'actual' ? 'text-blue-700' : 'text-amber-700', t === 'actual' ? 'dark:text-blue-400' : 'dark:text-amber-400', 'border-2', t === 'actual' ? 'border-blue-200' : 'border-amber-200', t === 'actual' ? 'dark:border-blue-800' : 'dark:border-amber-800');
            content.classList.remove('hidden');
        } else {
            tabBtn.classList.add('bg-gray-100', 'dark:bg-dark-bg', 'text-gray-700', 'dark:text-gray-300');
            tabBtn.classList.remove('bg-blue-100', 'bg-amber-100', 'dark:bg-blue-900/30', 'dark:bg-amber-900/30', 'text-blue-700', 'text-amber-700', 'dark:text-blue-400', 'dark:text-amber-400', 'border-2', 'border-blue-200', 'border-amber-200', 'dark:border-blue-800', 'dark:border-amber-800');
            content.classList.add('hidden');
        }
    });
    
    lucide.createIcons();
}

document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endpush
@endsection
