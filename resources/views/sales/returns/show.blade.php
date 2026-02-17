@extends('layouts.app')

@section('title', 'تفاصيل المرتجع')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-6xl mx-auto px-4">
        
        {{-- Header --}}
        <div class="animate-fade-in-down mb-8">
            <a href="{{ route('sales.returns.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-4 transition-colors">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                <span class="font-bold">العودة للمرتجعات</span>
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-orange-100 dark:bg-orange-600/20 text-orange-600 dark:text-orange-400 px-3 py-1 rounded-lg text-xs font-bold border border-orange-100 dark:border-orange-600/30">
                            مرتجع #{{ $return->return_number }}
                        </span>
                        @if($return->status === 'completed')
                        <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-bold">مكتمل</span>
                        @else
                        <span class="px-3 py-1 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-full text-xs font-bold">ملغي</span>
                        @endif
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white">تفاصيل المرتجع</h1>
                </div>
                <button onclick="window.location.href='{{ route('sales.returns.pdf', $return) }}'" class="px-6 py-3 bg-gray-100 dark:bg-dark-bg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all flex items-center gap-2">
                    <i data-lucide="printer" class="w-5 h-5"></i>
                    طباعة
                </button>
            </div>
        </div>

        {{-- Return Card --}}
        <div class="bg-white dark:bg-dark-card rounded-3xl shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border overflow-hidden animate-slide-up">
            
            {{-- Header Section --}}
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-8 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <i data-lucide="package-x" class="w-7 h-7"></i>
                            </div>
                            <div>
                                <p class="text-sm opacity-90">مرتجع</p>
                                <h2 class="text-2xl font-black">#{{ $return->return_number }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="text-left">
                        <p class="text-sm opacity-90 mb-1">إجمالي المرتجع</p>
                        <p class="text-3xl font-black">{{ number_format($return->total_amount, 0) }} دينار</p>
                    </div>
                </div>
            </div>

            {{-- Details Section --}}
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-500/10 rounded-xl flex items-center justify-center">
                                <i data-lucide="user" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase">العميل</p>
                        </div>
                        <p class="text-lg font-black text-gray-900 dark:text-white mb-1">{{ $return->customer->name }}</p>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                            <span>{{ $return->customer->phone }}</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-500/10 rounded-xl flex items-center justify-center">
                                <i data-lucide="calendar" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase">معلومات المرتجع</p>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">التاريخ:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $return->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">الفاتورة الأصلية:</span>
                                <a href="{{ route('sales.invoices.show', $return->invoice) }}" class="text-sm font-bold text-primary-600 dark:text-primary-400 hover:underline">#{{ $return->invoice->invoice_number }}</a>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">الموظف:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $return->salesUser->full_name ?? 'غير متوفر' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Products Table --}}
                <div class="mb-6">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center">
                            <i data-lucide="package" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                        </div>
                        <h3 class="text-xl font-black text-gray-900 dark:text-white">منتجات المرتجع</h3>
                    </div>
                    <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl overflow-hidden">
                        <table class="w-full">
                            <thead class="bg-gray-100 dark:bg-dark-card">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المنتج</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-400">الكمية</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-400">سعر الوحدة</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 dark:text-gray-400">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                                @foreach($return->items as $item)
                                <tr>
                                    <td class="px-4 py-3 text-gray-900 dark:text-white font-bold">{{ $item->product->name }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-400">{{ number_format($item->unit_price, 0) }} دينار</td>
                                    <td class="px-4 py-3 text-center font-bold text-gray-900 dark:text-white">{{ number_format($item->total_price, 0) }} دينار</td>
                                </tr>
                                @endforeach
                                <tr class="bg-orange-50 dark:bg-orange-500/10 border-t-2 border-orange-200 dark:border-orange-500/30">
                                    <td colspan="3" class="px-4 py-4 text-right font-black text-gray-900 dark:text-white">الإجمالي:</td>
                                    <td class="px-4 py-4 text-center font-black text-orange-600 dark:text-orange-400 text-lg">{{ number_format($return->total_amount, 0) }} دينار</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($return->notes)
                <div class="bg-amber-50 dark:bg-amber-500/10 rounded-2xl p-5 border border-amber-200 dark:border-amber-500/30">
                    <div class="flex items-center gap-2 mb-2">
                        <i data-lucide="message-square" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                        <h3 class="text-sm font-bold text-amber-900 dark:text-amber-300">ملاحظات</h3>
                    </div>
                    <p class="text-sm text-amber-800 dark:text-amber-200">{{ $return->notes }}</p>
                </div>
                @endif
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
