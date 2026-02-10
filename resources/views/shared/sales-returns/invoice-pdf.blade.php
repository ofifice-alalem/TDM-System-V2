<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>فاتورة إرجاع #{{ $salesReturn->return_number }}</title>
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('{{ storage_path('fonts/Cairo-Bold.ttf') }}') format('truetype');
            font-weight: bold;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('{{ storage_path('fonts/Cairo-Regular.ttf') }}') format('truetype');
            font-weight: normal;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; font-size: 14px; line-height: 1.6; color: #1f2937; direction: rtl; }
        .container { max-width: 800px; margin: 0 auto; padding: 40px; }
        .header { text-align: center; margin-bottom: 40px; border-bottom: 3px solid #f59e0b; padding-bottom: 20px; }
        .header h1 { font-size: 32px; color: #f59e0b; margin-bottom: 10px; font-weight: bold; }
        .header p { font-size: 16px; color: #6b7280; }
        .info-grid { display: table; width: 100%; margin-bottom: 30px; }
        .info-row { display: table-row; }
        .info-cell { display: table-cell; padding: 10px; border: 1px solid #e5e7eb; background: #f9fafb; }
        .info-label { font-weight: bold; color: #374151; width: 30%; }
        .info-value { color: #1f2937; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 8px; font-weight: bold; font-size: 14px; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #f3f4f6; color: #374151; }
        table { width: 100%; border-collapse: collapse; margin: 30px 0; }
        thead { background: #f59e0b; color: white; }
        th, td { padding: 12px; text-align: center; border: 1px solid #e5e7eb; }
        th { font-weight: bold; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        .total-row { background: #fef3c7 !important; font-weight: bold; font-size: 16px; }
        .footer { margin-top: 40px; padding-top: 20px; border-top: 2px solid #e5e7eb; text-align: center; color: #6b7280; font-size: 12px; }
        .notes { background: #fef3c7; border-right: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 8px; }
        .notes-title { font-weight: bold; color: #92400e; margin-bottom: 8px; }
        .notes-content { color: #78350f; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>فاتورة إرجاع بضاعة</h1>
            <p>نظام إدارة التوزيع والمبيعات - تقنية</p>
        </div>

        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell info-label">رقم الإرجاع</div>
                <div class="info-cell info-value">#{{ $salesReturn->return_number }}</div>
                <div class="info-cell info-label">التاريخ</div>
                <div class="info-cell info-value">{{ $salesReturn->created_at->format('Y-m-d') }}</div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">الفاتورة الأصلية</div>
                <div class="info-cell info-value">#{{ $salesReturn->salesInvoice->invoice_number }}</div>
                <div class="info-cell info-label">الحالة</div>
                <div class="info-cell info-value">
                    <span class="status-badge status-{{ $salesReturn->status }}">
                        @if($salesReturn->status === 'pending') قيد الانتظار
                        @elseif($salesReturn->status === 'approved') موثق
                        @elseif($salesReturn->status === 'rejected') مرفوض
                        @elseif($salesReturn->status === 'cancelled') ملغي
                        @endif
                    </span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell info-label">المسوق</div>
                <div class="info-cell info-value">{{ $salesReturn->marketer->full_name }}</div>
                <div class="info-cell info-label">المتجر</div>
                <div class="info-cell info-value">{{ $salesReturn->store->name }}</div>
            </div>
            @if($salesReturn->keeper)
            <div class="info-row">
                <div class="info-cell info-label">أمين المخزن</div>
                <div class="info-cell info-value">{{ $salesReturn->keeper->full_name }}</div>
                <div class="info-cell info-label">تاريخ التوثيق</div>
                <div class="info-cell info-value">{{ $salesReturn->confirmed_at ? $salesReturn->confirmed_at->format('Y-m-d H:i') : '-' }}</div>
            </div>
            @endif
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 10%;">#</th>
                    <th style="width: 40%;">المنتج</th>
                    <th style="width: 15%;">الكمية</th>
                    <th style="width: 15%;">سعر الوحدة</th>
                    <th style="width: 20%;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($salesReturn->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: right;">{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="4" style="text-align: left;">المجموع الإجمالي</td>
                    <td>{{ number_format($salesReturn->total_amount, 2) }} دينار</td>
                </tr>
            </tbody>
        </table>

        @if($salesReturn->notes)
        <div class="notes">
            <div class="notes-title">ملاحظات:</div>
            <div class="notes-content">{{ $salesReturn->notes }}</div>
        </div>
        @endif

        <div class="footer">
            <p>تم إنشاء هذه الفاتورة إلكترونياً بواسطة نظام تقنية لإدارة التوزيع والمبيعات</p>
            <p>{{ now()->format('Y-m-d H:i:s') }}</p>
        </div>
    </div>
</body>
</html>
