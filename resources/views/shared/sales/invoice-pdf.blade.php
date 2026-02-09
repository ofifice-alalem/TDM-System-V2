<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة بيع #{{ $invoice->invoice_number }}</title>
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('{{ storage_path('fonts/Cairo-Regular.ttf') }}') format('truetype');
            font-weight: normal;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('{{ storage_path('fonts/Cairo-Bold.ttf') }}') format('truetype');
            font-weight: bold;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Cairo', sans-serif; font-size: 12px; line-height: 1.6; color: #333; }
        .container { padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #f59e0b; padding-bottom: 20px; }
        .header h1 { font-size: 28px; color: #f59e0b; margin-bottom: 5px; }
        .header p { font-size: 11px; color: #666; }
        .info-section { display: table; width: 100%; margin-bottom: 20px; }
        .info-box { display: table-cell; width: 50%; padding: 15px; background: #f9fafb; border-radius: 8px; }
        .info-box:first-child { margin-left: 10px; }
        .info-box h3 { font-size: 14px; color: #f59e0b; margin-bottom: 10px; border-bottom: 2px solid #f59e0b; padding-bottom: 5px; }
        .info-row { margin-bottom: 8px; }
        .info-label { font-weight: bold; color: #666; display: inline-block; width: 100px; }
        .info-value { color: #333; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        thead { background: #f59e0b; color: white; }
        th { padding: 12px; text-align: center; font-weight: bold; font-size: 11px; }
        td { padding: 10px; text-align: center; border-bottom: 1px solid #e5e7eb; }
        tbody tr:nth-child(even) { background: #f9fafb; }
        .totals { margin-top: 30px; float: left; width: 50%; }
        .totals table { margin: 0; }
        .totals td { border: none; padding: 8px; }
        .totals .label { text-align: right; font-weight: bold; color: #666; }
        .totals .value { text-align: left; font-weight: bold; }
        .total-row { background: #1f2937 !important; color: white !important; font-size: 16px; }
        .footer { margin-top: 80px; text-align: center; padding-top: 20px; border-top: 2px solid #e5e7eb; font-size: 10px; color: #666; }
        .status-badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-size: 10px; font-weight: bold; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚡ تقنية للتوزيع</h1>
            <p>نظام إدارة التوزيع والمبيعات</p>
            <p style="margin-top: 10px;">
                <span class="status-badge status-{{ $invoice->status }}">
                    {{ $invoice->status === 'pending' ? 'قيد الانتظار' : 'موثق' }}
                </span>
            </p>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            <h2 style="font-size: 20px; color: #1f2937;">فاتورة بيع #{{ $invoice->invoice_number }}</h2>
            <p style="color: #666; font-size: 11px;">تاريخ الإصدار: {{ $invoice->created_at->format('Y-m-d h:i A') }}</p>
        </div>

        <div class="info-section">
            <div class="info-box">
                <h3>معلومات المسوق</h3>
                <div class="info-row">
                    <span class="info-label">الاسم:</span>
                    <span class="info-value">{{ $invoice->marketer->full_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">الهاتف:</span>
                    <span class="info-value">{{ $invoice->marketer->phone ?? 'غير متوفر' }}</span>
                </div>
            </div>
            <div class="info-box">
                <h3>معلومات المتجر</h3>
                <div class="info-row">
                    <span class="info-label">اسم المتجر:</span>
                    <span class="info-value">{{ $invoice->store->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">صاحب المتجر:</span>
                    <span class="info-value">{{ $invoice->store->owner_name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">الهاتف:</span>
                    <span class="info-value">{{ $invoice->store->phone ?? 'غير متوفر' }}</span>
                </div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 35%; text-align: right;">المنتج</th>
                    <th style="width: 12%;">الكمية</th>
                    <th style="width: 12%;">مجاني</th>
                    <th style="width: 15%;">سعر الوحدة</th>
                    <th style="width: 15%;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: right; font-weight: bold;">{{ $item->product->name }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td style="color: #059669; font-weight: bold;">{{ $item->free_quantity }}</td>
                    <td>{{ number_format($item->unit_price, 2) }} ر.س</td>
                    <td style="font-weight: bold;">{{ number_format($item->total_price, 2) }} ر.س</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td class="label">المجموع الفرعي:</td>
                    <td class="value">{{ number_format($invoice->subtotal, 2) }} ر.س</td>
                </tr>
                @if($invoice->product_discount > 0)
                <tr>
                    <td class="label" style="color: #059669;">خصم المنتجات (هدايا):</td>
                    <td class="value" style="color: #059669;">- {{ number_format($invoice->product_discount, 2) }} ر.س</td>
                </tr>
                @endif
                @if($invoice->invoice_discount_amount > 0)
                <tr>
                    <td class="label" style="color: #2563eb;">خصم الفاتورة:</td>
                    <td class="value" style="color: #2563eb;">- {{ number_format($invoice->invoice_discount_amount, 2) }} ر.س</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td class="label">الإجمالي النهائي:</td>
                    <td class="value">{{ number_format($invoice->total_amount, 2) }} ر.س</td>
                </tr>
            </table>
        </div>

        <div style="clear: both;"></div>

        @if($invoice->notes)
        <div style="margin-top: 30px; padding: 15px; background: #fef3c7; border-right: 4px solid #f59e0b; border-radius: 8px;">
            <h4 style="color: #92400e; margin-bottom: 8px;">ملاحظات:</h4>
            <p style="color: #78350f;">{{ $invoice->notes }}</p>
        </div>
        @endif

        <div class="footer">
            <p>هذه الفاتورة صادرة إلكترونياً من نظام تقنية للتوزيع</p>
            <p>تاريخ الطباعة: {{ now()->format('Y-m-d h:i A') }}</p>
        </div>
    </div>
</body>
</html>
