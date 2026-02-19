@extends('layouts.app')

@section('title', 'الإحصائيات')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4">
        
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white">الإحصائيات</h1>
                    <p class="text-gray-500 dark:text-dark-muted mt-1">تقارير وإحصائيات العملاء</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="openQuickModal('invoices')" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold transition-all flex items-center gap-2 shadow-md">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                        الفواتير
                    </button>
                    <button onclick="openQuickModal('payments')" class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold transition-all flex items-center gap-2 shadow-md">
                        <i data-lucide="banknote" class="w-4 h-4"></i>
                        المدفوعات
                    </button>
                    <button onclick="openQuickModal('returns')" class="px-5 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-bold transition-all flex items-center gap-2 shadow-md">
                        <i data-lucide="package-x" class="w-4 h-4"></i>
                        المرتجعات
                    </button>
                    <button onclick="openQuickModal('summary')" class="px-5 py-2.5 bg-purple-600 hover:bg-purple-700 text-white rounded-xl font-bold transition-all flex items-center gap-2 shadow-md">
                        <i data-lucide="wallet" class="w-4 h-4"></i>
                        الملخص المالي
                    </button>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl p-6 border border-gray-200 dark:border-dark-border shadow-lg mb-6">
            <form method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">اسم العميل</label>
                        <select name="customer_id" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">اختر العميل...</option>
                            <option value="all" {{ request('customer_id') == 'all' ? 'selected' : '' }}>الكل</option>
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
                    @if($results['operation'] == 'payments' && $results['paymentMethodTotals'])
                    <div class="mt-4 grid grid-cols-3 gap-3">
                        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-500/30 rounded-xl p-3">
                            <p class="text-xs text-green-700 dark:text-green-400 font-bold">إجمالي النقدي</p>
                            <p class="text-lg font-black text-green-600 dark:text-green-400">{{ number_format($results['paymentMethodTotals']['cash'], 2) }} دينار</p>
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-500/30 rounded-xl p-3">
                            <p class="text-xs text-blue-700 dark:text-blue-400 font-bold">إجمالي التحويل</p>
                            <p class="text-lg font-black text-blue-600 dark:text-blue-400">{{ number_format($results['paymentMethodTotals']['transfer'], 2) }} دينار</p>
                        </div>
                        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-500/30 rounded-xl p-3">
                            <p class="text-xs text-amber-700 dark:text-amber-400 font-bold">إجمالي الشيك</p>
                            <p class="text-lg font-black text-amber-600 dark:text-amber-400">{{ number_format($results['paymentMethodTotals']['check'], 2) }} دينار</p>
                        </div>
                    </div>
                    @endif
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
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">العميل</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">الموظف</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">التاريخ</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">الحالة</th>
                                @if($results['operation'] == 'payments')
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">طريقة الدفع</th>
                                @endif
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المبلغ</th>
                                @if($results['operation'] == 'invoices')
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المرتجعات</th>
                                @endif
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
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->customer->name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->salesUser->full_name ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-2 py-1 rounded text-xs font-bold">
                                            {{ $statusConfig['label'] }}
                                        </span>
                                    </td>
                                    @if($results['operation'] == 'payments')
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $item->payment_method === 'cash' ? 'نقدي' : ($item->payment_method === 'transfer' ? 'تحويل' : 'شيك') }}
                                    </td>
                                    @endif
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
                                    @if($results['operation'] == 'invoices')
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        @if($item->returns->count() > 0)
                                            @foreach($item->returns as $return)
                                                <span class="inline-block px-2 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 rounded text-xs font-bold mr-1 mb-1">{{ $return->return_number }}</span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $results['operation'] == 'invoices' ? '7' : ($results['operation'] == 'payments' ? '7' : '6') }}" class="px-6 py-12 text-center">
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

{{-- Quick Date Modal --}}
<div id="quickDateModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-dark-card rounded-2xl shadow-2xl max-w-md w-full p-6 border border-gray-200 dark:border-dark-border">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-black text-gray-900 dark:text-white">تحديد الفترة الزمنية</h3>
            <button onclick="closeQuickModal()" class="w-8 h-8 bg-gray-100 dark:bg-dark-bg rounded-lg flex items-center justify-center hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                <i data-lucide="x" class="w-5 h-5 text-gray-600 dark:text-gray-400"></i>
            </button>
        </div>
        <form id="quickDateForm" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">من تاريخ</label>
                <input type="date" id="quickFromDate" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all dark:[color-scheme:dark]">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">إلى تاريخ</label>
                <input type="date" id="quickToDate" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl text-gray-900 dark:text-white focus:border-primary-500 focus:ring-2 focus:ring-primary-100 dark:focus:ring-primary-500/20 transition-all dark:[color-scheme:dark]">
            </div>
            <button type="submit" class="w-full px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all flex items-center justify-center gap-2">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
                عرض النتائج
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let currentType = '';
    
    function openQuickModal(type) {
        currentType = type;
        const today = new Date().toISOString().split('T')[0];
        const firstDay = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().split('T')[0];
        
        document.getElementById('quickFromDate').value = firstDay;
        document.getElementById('quickToDate').value = today;
        document.getElementById('quickDateModal').classList.remove('hidden');
        lucide.createIcons();
    }
    
    function closeQuickModal() {
        document.getElementById('quickDateModal').classList.add('hidden');
    }
    
    document.getElementById('quickDateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const fromDate = document.getElementById('quickFromDate').value;
        const toDate = document.getElementById('quickToDate').value;
        
        const routes = {
            'invoices': '{{ route('sales.statistics.quick-invoices') }}',
            'payments': '{{ route('sales.statistics.quick-payments') }}',
            'returns': '{{ route('sales.statistics.quick-returns') }}',
            'summary': '{{ route('sales.statistics.quick-summary') }}'
        };
        
        window.location.href = routes[currentType] + '?from_date=' + fromDate + '&to_date=' + toDate;
    });
    
    document.getElementById('quickDateModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeQuickModal();
        }
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
    });
</script>
@endpush
@endsection
