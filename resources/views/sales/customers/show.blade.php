<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">تفاصيل العميل: {{ $customer->name }}</h2>
                <div class="flex gap-2">
                    <a href="{{ route('sales.customers.edit', $customer->id) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg">
                        تعديل
                    </a>
                    <a href="{{ route('sales.customers.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                        رجوع
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">معلومات العميل</h3>
                    <div class="space-y-2">
                        <p><span class="font-semibold">الاسم:</span> {{ $customer->name }}</p>
                        <p><span class="font-semibold">الهاتف:</span> {{ $customer->phone }}</p>
                        <p><span class="font-semibold">العنوان:</span> {{ $customer->address ?? 'غير محدد' }}</p>
                        <p><span class="font-semibold">رقم الهوية:</span> {{ $customer->id_number ?? 'غير محدد' }}</p>
                        <p><span class="font-semibold">الحالة:</span> 
                            <span class="px-2 py-1 text-xs rounded-full {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $customer->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">الدين الحالي</h3>
                    <p class="text-3xl font-bold {{ $totalDebt > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ number_format($totalDebt, 2) }} د.ع
                    </p>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">إجراءات سريعة</h3>
                    <div class="space-y-2">
                        <a href="{{ route('sales.invoices.create', ['customer_id' => $customer->id]) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center px-4 py-2 rounded-lg">
                            فاتورة جديدة
                        </a>
                        @if($totalDebt > 0)
                            <a href="{{ route('sales.payments.create', ['customer_id' => $customer->id]) }}" class="block w-full bg-green-600 hover:bg-green-700 text-white text-center px-4 py-2 rounded-lg">
                                تسديد دفعة
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-700">آخر الفواتير</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">رقم الفاتورة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">نوع الدفع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($customer->invoices as $invoice)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('sales.invoices.show', $invoice->id) }}" class="text-blue-600 hover:text-blue-900">
                                            {{ $invoice->invoice_number }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ number_format($invoice->total_amount, 2) }} د.ع</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($invoice->payment_type === 'cash') نقدي
                                        @elseif($invoice->payment_type === 'credit') آجل
                                        @else جزئي
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $invoice->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $invoice->status === 'completed' ? 'مكتملة' : 'ملغاة' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $invoice->created_at->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">لا توجد فواتير</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-700">حركات الحساب</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">النوع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($customer->debtLedger as $entry)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($entry->entry_type === 'sale') بيع
                                        @elseif($entry->entry_type === 'payment') دفعة
                                        @else إرجاع
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="{{ $entry->amount > 0 ? 'text-red-600' : 'text-green-600' }} font-semibold">
                                            {{ number_format($entry->amount, 2) }} د.ع
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $entry->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">لا توجد حركات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
