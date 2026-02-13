@extends('layouts.app')

@section('title', 'الإحصائيات')

@section('content')

<div class="min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4">
        
        <div class="mb-8">
            <h1 class="text-3xl font-black text-gray-900 dark:text-white">الإحصائيات</h1>
            <p class="text-gray-500 dark:text-dark-muted mt-1">تقارير وإحصائيات مفصلة</p>
        </div>

        {{-- Filters --}}
        <div class="bg-white dark:bg-dark-card rounded-2xl p-6 border border-gray-200 dark:border-dark-border shadow-lg mb-6">
            <form method="GET" action="{{ route('admin.statistics.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="md:col-span-2 lg:col-span-3">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">نوع الإحصاء</label>
                        <select name="stat_type" id="stat_type" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">اختر النوع...</option>
                            <option value="stores" {{ request('stat_type') == 'stores' ? 'selected' : '' }}>المتاجر</option>
                            <option value="marketers" {{ request('stat_type') == 'marketers' ? 'selected' : '' }}>المسوقين</option>
                        </select>
                    </div>
                </div>

                <div id="filters_container" style="display: {{ request('stat_type') ? 'block' : 'none' }}">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div id="store_field" style="display: {{ request('stat_type') == 'stores' ? 'block' : 'none' }}">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">اسم المتجر</label>
                            <select name="store_id" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">اختر المتجر...</option>
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="marketer_field" style="display: {{ request('stat_type') == 'marketers' ? 'block' : 'none' }}">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">اسم المسوق</label>
                            <select name="marketer_id" id="marketer_id" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">اختر المسوق...</option>
                                @foreach($marketers as $marketer)
                                    <option value="{{ $marketer->id }}" {{ request('marketer_id') == $marketer->id ? 'selected' : '' }}>{{ $marketer->full_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div id="marketer_store_field" style="display: none">
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">المتجر (اختياري)</label>
                            <select name="marketer_store_id" id="marketer_store_id" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">الكل</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">العملية</label>
                            <select name="operation" id="operation" required class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="">اختر العملية...</option>
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

                    <div>
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 mb-1.5">الحالة</label>
                        <select name="status" id="status" class="w-full bg-gray-50 dark:bg-dark-bg border border-gray-200 dark:border-dark-border rounded-xl px-4 py-2.5 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <option value="">الكل</option>
                        </select>
                    </div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                        عرض الإحصائيات
                    </button>
                    @if(request()->hasAny(['stat_type', 'store_id', 'operation', 'from_date', 'to_date', 'status']))
                        <button type="submit" name="export" value="1" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            تصدير Excel
                        </button>
                        <a href="{{ route('admin.statistics.index') }}" class="px-6 py-2.5 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-bold transition-all text-sm flex items-center gap-2">
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
                        <h2 class="text-xl font-black text-gray-900 dark:text-white">النتائج</h2>
                        <div class="text-left">
                            <p class="text-xs text-gray-500 dark:text-gray-400">الإجمالي</p>
                            <p class="text-2xl font-black text-primary-600 dark:text-primary-400">{{ number_format($results['total'], 2) }} دينار</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 dark:bg-dark-bg border-b border-gray-200 dark:border-dark-border">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">
                                    @if($results['operation'] == 'sales')
                                        رقم الفاتورة
                                    @elseif($results['operation'] == 'payments')
                                        رقم الإيصال
                                    @elseif($results['operation'] == 'returns')
                                        رقم الإرجاع
                                    @elseif($results['operation'] == 'requests')
                                        رقم الطلب
                                    @elseif($results['operation'] == 'withdrawals')
                                        رقم السحب
                                    @endif
                                </th>
                                @if(request('stat_type') == 'stores')
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المسوق</th>
                                @elseif(request('stat_type') == 'marketers' && in_array($results['operation'], ['sales', 'payments']))
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المتجر</th>
                                @endif
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">التاريخ</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-600 dark:text-gray-400">المبلغ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-dark-border">
                            @forelse($results['data'] as $item)
                                @php
                                    $statusConfig = [
                                        'pending' => ['bg' => 'bg-amber-100 dark:bg-amber-900/30', 'text' => 'text-amber-700 dark:text-amber-400', 'label' => 'معلق'],
                                        'approved' => ['bg' => 'bg-blue-100 dark:bg-blue-900/30', 'text' => 'text-blue-700 dark:text-blue-400', 'label' => 'موافق عليه'],
                                        'documented' => ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-400', 'label' => 'موثق'],
                                        'cancelled' => ['bg' => 'bg-gray-100 dark:bg-gray-800/50', 'text' => 'text-gray-700 dark:text-gray-400', 'label' => 'ملغي'],
                                        'rejected' => ['bg' => 'bg-red-100 dark:bg-red-900/30', 'text' => 'text-red-700 dark:text-red-400', 'label' => 'مرفوض'],
                                    ][$item->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'label' => $item->status];
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-dark-bg transition-colors">
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                        @if($results['operation'] == 'sales')
                                            {{ $item->invoice_number }}
                                        @elseif($results['operation'] == 'payments')
                                            {{ $item->payment_number }}
                                        @elseif($results['operation'] == 'returns')
                                            {{ $item->return_number }}
                                        @elseif($results['operation'] == 'requests')
                                            {{ $item->invoice_number }}
                                        @elseif($results['operation'] == 'withdrawals')
                                            WD-{{ $item->id }}
                                        @endif
                                    </td>
                                    @if(request('stat_type') == 'stores')
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->marketer->full_name ?? '-' }}</td>
                                    @elseif(request('stat_type') == 'marketers' && in_array($results['operation'], ['sales', 'payments']))
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->store->name ?? '-' }}</td>
                                    @endif
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $item->created_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-2 py-1 rounded text-xs font-bold">
                                            {{ $statusConfig['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                        @if($results['operation'] == 'sales')
                                            {{ number_format($item->total_amount, 2) }}
                                        @elseif($results['operation'] == 'payments')
                                            {{ number_format($item->amount, 2) }}
                                        @elseif($results['operation'] == 'returns')
                                            {{ number_format($item->total_amount, 2) }}
                                        @elseif($results['operation'] == 'requests')
                                            -
                                        @elseif($results['operation'] == 'withdrawals')
                                            {{ number_format($item->requested_amount, 2) }}
                                        @endif
                                        @if($results['operation'] != 'requests')
                                            دينار
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ (request('stat_type') == 'stores') || (request('stat_type') == 'marketers' && in_array($results['operation'], ['sales', 'payments'])) ? '5' : '4' }}" class="px-6 py-12 text-center">
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
            </div>
        @endif

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        
        const statType = document.getElementById('stat_type');
        const filtersContainer = document.getElementById('filters_container');
        const storeField = document.getElementById('store_field');
        const marketerField = document.getElementById('marketer_field');
        const marketerStoreField = document.getElementById('marketer_store_field');
        const marketerSelect = document.getElementById('marketer_id');
        const marketerStoreSelect = document.getElementById('marketer_store_id');
        const operation = document.getElementById('operation');
        const statusSelect = document.getElementById('status');
        
        const storeOperations = [
            {value: 'sales', text: 'فواتير البيع'},
            {value: 'payments', text: 'إيصالات القبض'},
            {value: 'returns', text: 'إرجاعات البضاعة'}
        ];
        
        const marketerOperations = [
            {value: 'requests', text: 'طلبات البضاعة'},
            {value: 'returns', text: 'إرجاعات البضاعة'},
            {value: 'sales', text: 'فواتير البيع'},
            {value: 'payments', text: 'إيصالات القبض'},
            {value: 'withdrawals', text: 'طلبات سحب الأرباح'}
        ];
        
        // Status options based on operation type
        const statusOptions = {
            // For requests and returns from marketer
            'requests': [
                {value: 'pending', text: 'معلق'},
                {value: 'approved', text: 'موافق عليه'},
                {value: 'documented', text: 'موثق'},
                {value: 'cancelled', text: 'ملغي'},
                {value: 'rejected', text: 'مرفوض'}
            ],
            'returns': [
                {value: 'pending', text: 'معلق'},
                {value: 'approved', text: 'موافق عليه'},
                {value: 'documented', text: 'موثق'},
                {value: 'cancelled', text: 'ملغي'},
                {value: 'rejected', text: 'مرفوض'}
            ],
            // For sales and payments
            'sales': [
                {value: 'pending', text: 'معلق'},
                {value: 'approved', text: 'موثق'},
                {value: 'cancelled', text: 'ملغي'},
                {value: 'rejected', text: 'مرفوض'}
            ],
            'payments': [
                {value: 'pending', text: 'معلق'},
                {value: 'approved', text: 'موثق'},
                {value: 'cancelled', text: 'ملغي'},
                {value: 'rejected', text: 'مرفوض'}
            ],
            'withdrawals': [
                {value: 'pending', text: 'معلق'},
                {value: 'approved', text: 'موثق'},
                {value: 'cancelled', text: 'ملغي'},
                {value: 'rejected', text: 'مرفوض'}
            ]
        };
        
        const selectedOperation = '{{ request('operation') }}';
        
        // Load marketer stores when marketer is selected
        marketerSelect.addEventListener('change', function() {
            const marketerId = this.value;
            if (marketerId) {
                fetch(`/admin/statistics/marketer-stores/${marketerId}`)
                    .then(response => response.json())
                    .then(stores => {
                        marketerStoreSelect.innerHTML = '<option value="">الكل</option>';
                        stores.forEach(store => {
                            const option = document.createElement('option');
                            option.value = store.id;
                            option.text = store.name;
                            if ('{{ request('marketer_store_id') }}' == store.id) {
                                option.selected = true;
                            }
                            marketerStoreSelect.appendChild(option);
                        });
                    });
            } else {
                marketerStoreSelect.innerHTML = '<option value="">الكل</option>';
            }
        });
        
        statType.addEventListener('change', function() {
            if (this.value === 'stores') {
                filtersContainer.style.display = 'block';
                storeField.style.display = 'block';
                marketerField.style.display = 'none';
                marketerStoreField.style.display = 'none';
                updateOperations(storeOperations, selectedOperation);
            } else if (this.value === 'marketers') {
                filtersContainer.style.display = 'block';
                storeField.style.display = 'none';
                marketerField.style.display = 'block';
                updateOperations(marketerOperations, selectedOperation);
            } else {
                filtersContainer.style.display = 'none';
                storeField.style.display = 'none';
                marketerField.style.display = 'none';
                marketerStoreField.style.display = 'none';
                operation.innerHTML = '<option value="">اختر العملية...</option>';
            }
        });
        
        function updateOperations(ops, selected) {
            operation.innerHTML = '<option value="">اختر العملية...</option>';
            ops.forEach(op => {
                const option = document.createElement('option');
                option.value = op.value;
                option.text = op.text;
                if (selected && op.value === selected) {
                    option.selected = true;
                }
                operation.appendChild(option);
            });
            
            // Update status options when operation changes
            if (selected) {
                updateStatusOptions(selected);
            }
            
            // Show/hide marketer store field based on operation
            if (statType.value === 'marketers' && ['sales', 'payments'].includes(operation.value)) {
                marketerStoreField.style.display = 'block';
            } else {
                marketerStoreField.style.display = 'none';
            }
        }
        
        function updateStatusOptions(operationType) {
            const selectedStatus = '{{ request('status') }}';
            const options = statusOptions[operationType] || [];
            
            statusSelect.innerHTML = '<option value="">الكل</option>';
            options.forEach(opt => {
                const option = document.createElement('option');
                option.value = opt.value;
                option.text = opt.text;
                if (selectedStatus && opt.value === selectedStatus) {
                    option.selected = true;
                }
                statusSelect.appendChild(option);
            });
        }
        
        // Listen to operation changes
        operation.addEventListener('change', function() {
            updateStatusOptions(this.value);
            
            if (statType.value === 'marketers' && ['sales', 'payments'].includes(this.value)) {
                marketerStoreField.style.display = 'block';
            } else {
                marketerStoreField.style.display = 'none';
            }
        });
        
        // Initialize on load
        if (statType.value) {
            statType.dispatchEvent(new Event('change'));
        }
        
        // Load marketer stores on page load if marketer is selected
        if (marketerSelect.value && statType.value === 'marketers') {
            marketerSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endpush
@endsection
