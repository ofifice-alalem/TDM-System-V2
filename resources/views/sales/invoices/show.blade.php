@extends('layouts.app')

@section('title', 'تفاصيل الفاتورة')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-6xl mx-auto px-4">
        
        {{-- Header --}}
        <div class="animate-fade-in-down mb-8">
            <a href="{{ route('sales.invoices.index') }}" class="inline-flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 mb-4 transition-colors">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                <span class="font-bold">العودة للفواتير</span>
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold border border-primary-100 dark:border-primary-600/30">
                            فاتورة #{{ $invoice->invoice_number }}
                        </span>
                        @if($invoice->status === 'completed')
                        <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full text-xs font-bold">مكتمل</span>
                        @else
                        <span class="px-3 py-1 bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400 rounded-full text-xs font-bold">ملغي</span>
                        @endif
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 dark:text-white">تفاصيل الفاتورة</h1>
                </div>
                <button onclick="window.location.href='{{ route('sales.invoices.pdf', $invoice) }}'" class="px-6 py-3 bg-gray-100 dark:bg-dark-bg hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-bold transition-all flex items-center gap-2">
                    <i data-lucide="printer" class="w-5 h-5"></i>
                    طباعة
                </button>
            </div>
        </div>

        {{-- Customer & Invoice Info --}}
        <div class="bg-white dark:bg-dark-card rounded-3xl shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border overflow-hidden animate-slide-up">
            
            {{-- Header Section --}}
            <div class="bg-white dark:bg-dark-card p-8 border-b border-gray-200 dark:border-dark-border">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-primary-100 dark:bg-primary-500/10 rounded-xl flex items-center justify-center">
                            <i data-lucide="file-text" class="w-7 h-7 text-primary-600 dark:text-primary-400"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">فاتورة</p>
                                    <h2 class="text-2xl font-black text-gray-900 dark:text-white">#{{ $invoice->invoice_number }}</h2>
                                </div>
                                @if($invoice->status === 'completed')
                                <span class="px-3 py-1.5 bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-lg text-xs font-bold border border-emerald-200 dark:border-emerald-500/30 flex items-center gap-1.5">
                                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                                    مكتمل
                                </span>
                                @else
                                <span class="px-3 py-1.5 bg-red-100 dark:bg-red-500/10 text-red-700 dark:text-red-400 rounded-lg text-xs font-bold border border-red-200 dark:border-red-500/30 flex items-center gap-1.5">
                                    <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                    ملغي
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-left">
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">إجمالي الفاتورة</p>
                        <p class="text-3xl font-black text-gray-900 dark:text-white">{{ number_format($invoice->total_amount, 0) }} دينار</p>
                    </div>
                </div>
            </div>

            @if($invoice->status === 'cancelled')
            <div class="bg-red-50 dark:bg-red-500/10 border-t-4 border-red-500 dark:border-red-600 p-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-14 h-14 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center">
                            <i data-lucide="x-octagon" class="w-7 h-7 text-red-600 dark:text-red-400"></i>
                        </div>
                        <h3 class="text-xl font-black text-red-900 dark:text-red-300">تم إلغاء الفاتورة</h3>
                    </div>
                    <div class="flex items-center gap-6">
                        <div class="flex items-center gap-2 text-red-700 dark:text-red-400">
                            <i data-lucide="calendar" class="w-5 h-5"></i>
                            <span class="font-bold">بتاريخ:</span>
                            <span class="text-base">{{ $invoice->updated_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="w-px h-6 bg-red-300 dark:bg-red-700"></div>
                        <div class="flex items-center gap-2 text-red-700 dark:text-red-400">
                            <i data-lucide="clock" class="w-5 h-5"></i>
                            <span class="font-bold">الساعة:</span>
                            <span class="text-base">{{ $invoice->updated_at->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
                @if($invoice->cancel_notes)
                <div class="mt-6 bg-white dark:bg-dark-bg rounded-xl p-4 border border-red-200 dark:border-red-700/30">
                    <p class="text-xs text-red-600 dark:text-red-400 font-bold mb-2">سبب الإلغاء:</p>
                    <p class="text-sm text-red-800 dark:text-red-300">{{ $invoice->cancel_notes }}</p>
                </div>
                @endif
            </div>
            @endif

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
                        <p class="text-lg font-black text-gray-900 dark:text-white mb-1">{{ $invoice->customer->name }}</p>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <i data-lucide="phone" class="w-4 h-4"></i>
                            <span>{{ $invoice->customer->phone }}</span>
                        </div>
                        @if($invoice->customer->address)
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mt-2">
                            <i data-lucide="map-pin" class="w-4 h-4"></i>
                            <span>{{ $invoice->customer->address }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 dark:bg-dark-bg rounded-2xl p-5">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 bg-amber-100 dark:bg-amber-500/10 rounded-xl flex items-center justify-center">
                                <i data-lucide="calendar" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 font-bold uppercase">معلومات الفاتورة</p>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">التاريخ:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $invoice->created_at->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">نوع الدفع:</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold 
                                    {{ $invoice->payment_type === 'cash' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                    {{ $invoice->payment_type === 'credit' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                    {{ $invoice->payment_type === 'partial' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}">
                                    {{ $invoice->payment_type === 'cash' ? 'نقدي' : ($invoice->payment_type === 'credit' ? 'آجل' : 'جزئي') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">الموظف:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $invoice->salesUser->full_name ?? 'غير متوفر' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="bg-white dark:bg-dark-card rounded-3xl shadow-lg shadow-gray-200/60 dark:shadow-none border border-gray-200 dark:border-dark-border overflow-hidden mb-6">
                    <div class="p-6 border-b border-gray-200 dark:border-dark-border">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-primary-50 dark:bg-primary-500/10 rounded-xl flex items-center justify-center">
                                <i data-lucide="package" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <h3 class="text-xl font-black text-gray-900 dark:text-white">المنتجات</h3>
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-dark-bg">
                                <tr>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">المنتج</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">الكمية</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">سعر الوحدة</th>
                                    <th class="px-6 py-4 text-center text-xs font-bold text-gray-600 dark:text-gray-400 uppercase">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                                @foreach($invoice->items as $item)
                                <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900 dark:text-white">{{ $item->product->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-white">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-center text-gray-600 dark:text-gray-400">{{ number_format($item->unit_price, 0) }} دينار</td>
                                    <td class="px-6 py-4 text-center font-bold text-gray-900 dark:text-white">{{ number_format($item->total_price, 0) }} دينار</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="p-6 bg-gray-50 dark:bg-dark-bg border-t border-gray-200 dark:border-dark-border">
                        <div class="max-w-md mr-auto space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">المجموع الفرعي:</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ number_format($invoice->subtotal, 0) }} دينار</span>
                            </div>
                            @if($invoice->discount_amount > 0)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">الخصم:</span>
                                    <span class="font-bold text-red-600 dark:text-red-400">- {{ number_format($invoice->discount_amount, 0) }} دينار</span>
                                </div>
                            @endif
                            <div class="flex justify-between text-xl">
                                <span class="font-black text-gray-900 dark:text-white">الإجمالي:</span>
                                <span class="font-black text-primary-600 dark:text-primary-400">{{ number_format($invoice->total_amount, 0) }} دينار</span>
                            </div>
                        </div>
                    </div>
                </div>

                @if($invoice->notes)
                    <div class="bg-amber-50 dark:bg-amber-500/10 rounded-2xl p-5 border border-amber-200 dark:border-amber-500/30">
                        <div class="flex items-center gap-2 mb-2">
                            <i data-lucide="message-square" class="w-5 h-5 text-amber-600 dark:text-amber-400"></i>
                            <h3 class="text-sm font-bold text-amber-900 dark:text-amber-300">ملاحظات</h3>
                        </div>
                        <p class="text-sm text-amber-800 dark:text-amber-200 bg-white dark:bg-dark-bg rounded-xl p-4">{{ $invoice->notes }}</p>
                    </div>
                @endif

                @if($invoice->status === 'completed' && $invoice->returns()->where('status', '!=', 'cancelled')->count() === 0)
                    <div class="bg-red-50 dark:bg-red-500/10 rounded-2xl p-5 border border-red-200 dark:border-red-500/30 mt-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                                <div>
                                    <h3 class="text-sm font-bold text-red-900 dark:text-red-300">إلغاء الفاتورة</h3>
                                    <p class="text-xs text-red-700 dark:text-red-400 mt-0.5">سيتم إرجاع الكميات للمخزون وإلغاء الدين</p>
                                </div>
                            </div>
                            <button type="button" onclick="document.getElementById('cancelForm').classList.toggle('hidden')" class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all flex items-center gap-2 shadow-lg shadow-red-500/30">
                                <i data-lucide="x-circle" class="w-4 h-4"></i>
                                إلغاء الفاتورة
                            </button>
                        </div>
                        <form id="cancelForm" action="{{ route('sales.invoices.cancel', $invoice) }}" method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                            <div class="space-y-3">
                                <textarea name="cancel_notes" rows="3" class="w-full px-4 py-3 bg-white dark:bg-dark-bg border border-red-300 dark:border-red-500/30 rounded-xl text-gray-900 dark:text-white focus:ring-2 focus:ring-red-500 focus:border-transparent" placeholder="سبب الإلغاء (اختياري)"></textarea>
                                <button type="submit" onclick="return confirm('هل أنت متأكد من إلغاء هذه الفاتورة؟')" class="w-full px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4"></i>
                                    تأكيد الإلغاء
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                @if($invoice->returns->count() > 0)
                    <div class="bg-orange-50 dark:bg-orange-500/10 rounded-2xl p-5 border border-orange-200 dark:border-orange-500/30 mt-6">
                        <div class="flex items-center gap-2 mb-3">
                            <i data-lucide="undo-2" class="w-5 h-5 text-orange-600 dark:text-orange-400"></i>
                            <h3 class="text-sm font-bold text-orange-900 dark:text-orange-300">مرتجعات هذه الفاتورة</h3>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($invoice->returns as $return)
                                <a href="{{ route('sales.returns.show', $return->id) }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-2 bg-white dark:bg-dark-bg text-orange-600 dark:text-orange-400 rounded-lg text-sm font-bold hover:bg-orange-100 dark:hover:bg-orange-900/20 transition-colors border border-orange-200 dark:border-orange-500/30">
                                    <i data-lucide="external-link" class="w-4 h-4"></i>
                                    {{ $return->return_number }}
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ number_format($return->total_amount, 0) }} دينار</span>
                                </a>
                            @endforeach
                        </div>
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
