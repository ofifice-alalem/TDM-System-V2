@extends('layouts.app')

@section('title', 'الإحصائيات')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4">
        
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 dark:text-white">الإحصائيات</h1>
            <p class="text-gray-500 dark:text-dark-muted mt-1">تقارير وإحصائيات العملاء</p>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl p-6 border border-gray-200 dark:border-dark-border shadow-lg mb-6">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">اسم العميل</label>
                        <select name="customer_id" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">اختر العميل...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">العملية</label>
                        <select name="operation" id="operation" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">اختر العملية...</option>
                            <option value="invoices" {{ request('operation') == 'invoices' ? 'selected' : '' }}>الفواتير</option>
                            <option value="payments" {{ request('operation') == 'payments' ? 'selected' : '' }}>المدفوعات</option>
                            <option value="returns" {{ request('operation') == 'returns' ? 'selected' : '' }}>المرتجعات</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">الحالة</label>
                        <select name="status" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">الكل</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">من تاريخ</label>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:[color-scheme:dark]">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">إلى تاريخ</label>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 dark:[color-scheme:dark]">
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                        عرض الإحصائيات
                    </button>
                    @if(request()->hasAny(['customer_id', 'operation', 'from_date', 'to_date']))
                        <button type="submit" name="export" value="1" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            تصدير Excel
                        </button>
                        <a href="{{ route('sales.statistics.index') }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            إعادة تعيين
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Results --}}
        @if($results)
            <div class="bg-white dark:bg-dark-card rounded-2xl border border-gray-200 dark:border-dark-border shadow-lg overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-dark-border">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-black text-gray-900 dark:text-white">النتائج</h2>
                            @if(!request('status'))
                            <div class="flex items-center gap-2 mt-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-500/30 rounded-lg">
                                <i data-lucide="info" class="w-4 h-4 text-blue-600 dark:text-blue-400"></i>
                                <p class="text-xs font-bold text-blue-700 dark:text-blue-400">يتم احتساب العمليات المكتملة فقط</p>
                            </div>
                            @endif
                        </div>
                        <div class="text-left">
                            <p class="text-xs text-gray-500 dark:text-gray-400">الإجمالي</p>
                            <p class="text-2xl font-black text-primary-600 dark:text-primary-400">{{ number_format($results['total'], 2) }} دينار</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">
                                    @if($results['operation'] == 'invoices')
                                        رقم الفاتورة
                                    @elseif($results['operation'] == 'payments')
                                        رقم الإيصال
                                    @elseif($results['operation'] == 'returns')
                                        رقم الإرجاع
                                    @endif
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">الموظف</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">التاريخ</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المبلغ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                            @forelse($results['data'] as $item)
                                @php
                                    $statusConfig = match($item->status) {
                                        'completed' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'مكتمل'],
                                        'cancelled' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'ملغي'],
                                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => $item->status]
                                    };
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                        @if($results['operation'] == 'invoices')
                                            {{ $item->invoice_number }}
                                        @elseif($results['operation'] == 'payments')
                                            {{ $item->payment_number }}
                                        @elseif($results['operation'] == 'returns')
                                            {{ $item->return_number }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->salesUser->full_name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-2 py-1 rounded text-xs font-bold">
                                            {{ $statusConfig['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                        @if($results['operation'] == 'invoices')
                                            {{ number_format($item->total_amount, 2) }}
                                        @elseif($results['operation'] == 'payments')
                                            {{ number_format($item->amount, 2) }}
                                        @elseif($results['operation'] == 'returns')
                                            {{ number_format($item->total_amount, 2) }}
                                        @endif
                                        دينار
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <i data-lucide="inbox" class="w-12 h-12 text-gray-400 dark:text-gray-600 mb-3"></i>
                                            <p class="text-gray-500 dark:text-gray-400">لا توجد نتائج</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($results['data']->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-dark-border">
                        {{ $results['data']->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        @endif

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
