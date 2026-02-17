<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>فاتورة مبيعات</title>
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path("fonts/Cairo-Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path("fonts/Cairo-Bold.ttf") }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path("fonts/Cairo-ExtraBold.ttf") }}') format('truetype');
            font-weight: 900;
            font-style: normal;
        }
        @page { margin: 10px; }
        * { font-family: 'Cairo', 'DejaVu Sans', sans-serif; }
        body { font-family: 'Cairo', 'DejaVu Sans', sans-serif; color: #333; font-size: 11px; margin: 0; position: relative; }
        @if($isInvalid)
        body::before {
            content: "{{ $labels['invalidInvoice'] }}";
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 80px;
            font-weight: 900;
            color: rgba(220, 53, 69, 0.15);
            z-index: 1000;
            pointer-events: none;
            white-space: nowrap;
        }
        @endif
        .header { margin-bottom: 5px; background-color: #333; color: white; padding: 5px; border-radius: 3px; display: table; width: 100%; }
        .header-right { display: table-cell; text-align: right; width: 50%; vertical-align: middle; }
        .header-left { display: table-cell; text-align: left; width: 50%; vertical-align: middle; }
        .header h1 { margin: 0; font-size: 16px; font-weight: bold; }
        .header h2 { margin: 0; font-size: 18px; font-weight: 900; color: white; letter-spacing: 0.5px; }
        .company-header { display: table; width: 100%; margin-bottom: 10px; }
        .company-name { display: table-cell; text-align: right; width: 70%; vertical-align: top; font-size: 16px; font-weight: bold; color: #333; }
        .company-logo { display: table-cell; text-align: left; width: 30%; vertical-align: top; }
        .company-logo img { max-height: 145px; max-width: 100%; }
        .info-box { background-color: #f8f9fa; padding: 4px 8px; border-radius: 3px; margin-bottom: 5px; border: 1px solid #333; text-align: right; margin-top: 10px; }
        .info-row { display: inline-block; width: 48%; margin-bottom: 2px; font-size: 11px; text-align: right; font-weight: bold; }
        .label { font-weight: bold; color: #333; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 3px; }
        th { background-color: #333; color: white; padding: 4px; text-align: center; font-weight: bold; font-size: 12px; }
        td { border: 1px solid #333; padding: 3px; background-color: #ffffff; font-size: 11px; text-align: center; }
        td.product-name { text-align: right; padding-right: 6px; }
        td.quantity { font-family: 'DejaVu Sans', sans-serif; direction: ltr; }
        tr:nth-child(even) td { background-color: #f5f5f5; }
        .totals { margin-top: 10px; width: 50%; margin-right: 0; border: 2px solid #333; padding: 10px; border-radius: 5px; background-color: #f8f9fa; }
        .total-row { padding: 3px 0; font-size: 12px; display: table; width: 100%; direction: rtl; }
        .total-row .label { display: table-cell; text-align: right; width: 60%; font-weight: bold; }
        .total-row .value { display: table-cell; text-align: left; width: 40%; direction: ltr; }
        .total-row.grand { font-size: 14px; font-weight: 900; border-top: 2px solid #333; padding-top: 5px; margin-top: 5px; }
        .notes-box { margin-top: 10px; border: 1px solid #333; padding: 10px; border-radius: 5px; background-color: #fff; }
        .notes-box .notes-label { font-weight: bold; font-size: 12px; margin-bottom: 5px; }
        .notes-box .notes-content { font-size: 11px; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h2>#{{ $invoiceNumber }}</h2>
        </div>
        <div class="header-right">
            <h1>{{ $title }}</h1>
        </div>
    </div>

    <div class="company-header">
        <div class="company-logo">
            @if($logoBase64)
            <img src="data:image/png;base64,{{ $logoBase64 }}" alt="شعار الشركة">
            @endif
        </div>
        <div class="company-name">
            {{ $companyName }}
            <div class="info-box">
                <div class="info-row">
                    {{ $customerName }} :<span class="label">{{ $labels['customer'] }}</span>
                </div>
                <div class="info-row">
                    {{ $customerPhone }} :<span class="label">{{ $labels['phone'] }}</span>
                </div>
                <div class="info-row">
                    {{ $date }} :<span class="label">{{ $labels['date'] }}</span>
                </div>
                <div class="info-row">
                    {{ $paymentType }} :<span class="label">{{ $labels['paymentType'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ $labels['total'] }}</th>
                <th>{{ $labels['unitPrice'] }}</th>
                <th>{{ $labels['quantity'] }}</th>
                <th>{{ $labels['product'] }}</th>
                <th>#</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td class="quantity">{{ $item->totalPrice }}</td>
                <td class="quantity">{{ $item->unitPrice }}</td>
                <td class="quantity">{{ $item->quantity }}</td>
                <td class="product-name">{{ $item->name }}</td>
                <td class="quantity">{{ $index + 1 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-row">
            <span class="value">{{ $subtotal }} {{ $currency }}</span>
            <span class="label">:{{ $labels['subtotal'] }}</span>
        </div>
        @if($discountAmount > 0)
        <div class="total-row">
            <span class="value">{{ $discountAmount }} {{ $currency }}</span>
            <span class="label">:{{ $labels['discount'] }}</span>
        </div>
        @endif
        <div class="total-row grand">
            <span class="value">{{ $totalAmount }} {{ $currency }}</span>
            <span class="label">:{{ $labels['grandTotal'] }}</span>
        </div>
    </div>
</body>
</html>
