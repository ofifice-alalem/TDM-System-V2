<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>فاتورة مصنع - {{ $invoice->invoice_number }}</title>
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('{{ storage_path('fonts/cairo_normal_2d9c4d6e617fe3285e59b1da45dbe1bb.ttf') }}') format('truetype');
            font-weight: normal;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('{{ storage_path('fonts/cairo_bold_e98ffece7049214f295baa3838c174b1.ttf') }}') format('truetype');
            font-weight: bold;
        }
        body { font-family: 'Cairo', sans-serif; direction: rtl; text-align: right; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #f59e0b; padding-bottom: 20px; }
        .header h1 { color: #f59e0b; font-size: 28px; margin: 0; }
        .header p { color: #666; margin: 5px 0; }
        .info-box { background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .info-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .info-label { font-weight: bold; color: #374151; }
        .info-value { color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f59e0b; color: white; padding: 12px; text-align: right; font-weight: bold; }
        td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        tr:nth-child(even) { background: #f9fafb; }
        .footer { margin-top: 40px; text-align: center; color: #9ca3af; font-size: 12px; border-top: 1px solid #e5e7eb; padding-top: 20px; }
        .status-badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 14px; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-documented { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <div class="header">
        <h1>⚡ تقنية للتوزيع</h1>
        <p>فاتورة تعبئة مخزن من المصنع</p>
        <p style="font-size: 12px; color: #9ca3af;">{{ now()->format('Y-m-d H:i') }}</p>
    </div>

    <div class="info-box">
        <div class="info-row">
            <div>
                <span class="info-label">رقم الفاتورة:</span>
                <span class="info-value">{{ $invoice->invoice_number }}</span>
            </div>
            <div>
                <span class="info-label">الحالة:</span>
                <span class="status-badge status-{{ $invoice->status }}">
                    {{ $invoice->status === 'pending' ? 'قيد الانتظار' : 'موثق' }}
                </span>
            </div>
        </div>
        <div class="info-row">
            <div>
                <span class="info-label">أمين المخزن:</span>
                <span class="info-value">{{ $invoice->keeper->full_name }}</span>
            </div>
            <div>
                <span class="info-label">تاريخ الإنشاء:</span>
                <span class="info-value">{{ $invoice->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>
        @if($invoice->status === 'documented')
        <div class="info-row">
            <div>
                <span class="info-label">وثق بواسطة:</span>
                <span class="info-value">{{ $invoice->documenter->full_name }}</span>
            </div>
            <div>
                <span class="info-label">تاريخ التوثيق:</span>
                <span class="info-value">{{ $invoice->documented_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>
        @endif
    </div>

    @if($invoice->notes)
    <div class="info-box">
        <div class="info-label">ملاحظات:</div>
        <div style="margin-top: 5px; color: #6b7280;">{{ $invoice->notes }}</div>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th style="width: 10%;">#</th>
                <th style="width: 60%;">المنتج</th>
                <th style="width: 30%; text-align: center;">الكمية</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $item->product->name }}</strong></td>
                <td style="text-align: center;"><strong>{{ number_format($item->quantity) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>هذه الفاتورة صادرة إلكترونياً من نظام تقنية للتوزيع</p>
        <p>تم الطباعة بتاريخ: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
