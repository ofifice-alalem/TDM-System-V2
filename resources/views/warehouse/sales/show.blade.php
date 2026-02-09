@extends('layouts.app')

@section('title', 'تفاصيل الفاتورة #' . $invoice->invoice_number)

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto space-y-8 px-4 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 animate-fade-in-down">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="bg-primary-100 dark:bg-primary-600/20 text-primary-600 dark:text-primary-400 px-3 py-1 rounded-lg text-xs font-bold">
                        فاتورة بيع
                    </span>
                    <span class="text-gray-400 dark:text-dark-muted text-xs font-mono tracking-wider">
                        {{ $invoice->created_at->format('Y-m-d h:i A') }}
                    </span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white">
                    فاتورة #{{ $invoice->invoice_number }}
                </h1>
            </div>

            <a href="{{ route('warehouse.sales.index') }}" class="px-12 py-4 bg-white dark:bg-dark-card border border-gray-200 dark:border-dark-border text-gray-700 dark:text-gray-300 rounded-xl font-bold hover:bg-gray-50 transition-colors shadow-lg flex items-center gap-2">
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
                عودة
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-8 space-y-6 animate-slide-up order-2 lg:order-1">
                
                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-xl border border-gray-200 dark:border-dark-border">
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white flex items-center gap-3 mb-6">
                        <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400">
                            <i data-lucide="info" class="w-5 h-5"></i>
                        </span>
                        معلومات الفاتورة
                    </h2>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">المسوق</p>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $invoice->marketer->full_name }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-dark-bg rounded-xl p-4">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">المتجر</p>
                            <p class="font-bold text-gray-900 dark:text-white">{{ $invoice->store->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-dark-card rounded-[2rem] p-8 shadow-xl border border-gray-200 dark:border-dark-border">
                    <h2 class="font-bold text-xl text-gray-900 dark:text-white flex items-center gap-3 mb-6">
                        <span class="bg-primary-50 dark:bg-primary-900/20 p-2.5 rounded-xl text-primary-600 dark:text-primary-400">
                            <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        </span>
                        المنتجات
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full border-separate border-spacing-y-3">
                            <thead>
                                <tr class="text-xs text-gray-400 font-bold uppercase">
                                    <th class="px-4 py-2 text-right">المنتج</th>
                                    <th class="px-4 py-2 text-center">الكمية</th>
                                    <th class="px-4 py-2 text-center">مجاني</th>
                                    <th class="px-4 py-2 text-center">السعر</th>
                                    <th class="px-4 py-2 text-center">الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoice->items as $item)
                                <tr>
                                    <td class="px-4 py-4 bg-gray-50 dark:bg-dark-bg rounded-r-2xl border border-gray-100 dark:border-dark-border">
                                        <div class="font-bold text-gray-900 dark:text-gray-100">{{ $item->product->name }}</div>
                                    </td>
                                    <td class="px-4 py-4 bg-gray-50 dark:bg-dark-bg border-y border-gray-100 dark:border-dark-border text-center">
                                        <span class="font-black">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="px-4 py-4 bg-gray-50 dark:bg-dark-bg border-y border-gray-100 dark:border-dark-border text-center">
                                        <span class="font-black text-emerald-600">{{ $item->free_quantity }}</span>
                                    </td>
                                    <td class="px-4 py-4 bg-gray-50 dark:bg-dark-bg border-y border-gray-100 dark:border-dark-border text-center">
                                        {{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="px-4 py-4 bg-gray-50 dark:bg-dark-bg rounded-l-2xl border border-gray-100 dark:border-dark-border text-center">
                                        <span class="font-black">{{ number_format($item->total_price, 2) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-8 bg-gray-900 dark:bg-black/40 rounded-3xl p-8 text-white">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span>المجموع الفرعي:</span>
                                <span class="font-bold">{{ number_format($invoice->subtotal, 2) }} ر.س</span>
                            </div>
                            @if($invoice->invoice_discount_amount > 0)
                            <div class="flex justify-between text-blue-400">
                                <span>خصم الفاتورة:</span>
                                <span class="font-bold">- {{ number_format($invoice->invoice_discount_amount, 2) }} ر.س</span>
                            </div>
                            @endif
                            <div class="pt-3 border-t border-white/10 flex justify-between text-2xl">
                                <span class="font-bold">الإجمالي:</span>
                                <span class="font-black">{{ number_format($invoice->total_amount, 2) }} ر.س</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-4 space-y-6">
                
                <div class="bg-gray-50 dark:bg-dark-card/50 rounded-[1.5rem] border-2 border-dashed border-gray-200 dark:border-dark-border p-6">
                    <h3 class="text-gray-800 dark:text-gray-200 font-bold text-lg mb-6">حالة الفاتورة</h3>

                    <div class="text-center py-4">
                        @php
                            $statusConfig = [
                                'pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'icon' => 'clock', 'label' => 'قيد الانتظار'],
                                'approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'icon' => 'check-circle', 'label' => 'موثق'],
                            ][$invoice->status];
                        @endphp
                        
                        <div class="inline-flex items-center justify-center p-4 rounded-full {{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} mb-4">
                            <i data-lucide="{{ $statusConfig['icon'] }}" class="w-8 h-8"></i>
                        </div>
                        <h2 class="text-2xl font-black {{ $statusConfig['text'] }}">{{ $statusConfig['label'] }}</h2>
                    </div>

                    @if($invoice->status === 'pending')
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-dark-border">
                        <form action="{{ route('warehouse.sales.approve', $invoice) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">صورة الفاتورة المختومة</label>
                                <input type="file" name="stamped_invoice_image" accept="image/*" required class="w-full bg-white dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            </div>
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                                <i data-lucide="check" class="w-5 h-5"></i>
                                توثيق الفاتورة
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-dark-border">
                        <a href="{{ route('marketer.sales.pdf', $invoice) }}" target="_blank" class="w-full bg-gray-900 dark:bg-dark-bg text-white hover:bg-gray-800 py-3.5 rounded-xl font-bold transition-all shadow-lg flex items-center justify-center gap-2">
                            <i data-lucide="printer" class="w-5 h-5"></i>
                            طباعة PDF
                        </a>
                        @if($invoice->stamped_invoice_image)
                        <a href="{{ route('warehouse.sales.documentation', $invoice) }}" target="_blank" class="w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white py-3.5 rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                            <i data-lucide="image" class="w-5 h-5"></i>
                            عرض صورة التوثيق
                        </a>
                        @endif
                    </div>
                    @endif
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
