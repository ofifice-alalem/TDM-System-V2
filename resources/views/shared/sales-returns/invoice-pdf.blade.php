<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>إرجاع بضاعة</title>
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
        .info-box { background-color: #f8f9fa; padding: 4px 8px; border-radius: 3px; margin-bottom: 5px; border: 1px solid #333; text-align: right; }
        .info-row { display: inline-block; width: 48%; margin-bottom: 2px; font-size: 11px; text-align: right; font-weight: bold; }
        .label { font-weight: bold; color: #333; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-top: 3px; }
        th { background-color: #333; color: white; padding: 4px; text-align: center; font-weight: bold; font-size: 12px; }
        td { border: 1px solid #333; padding: 3px; background-color: #ffffff; font-size: 11px; text-align: center; }
        td.product-name { text-align: right; padding-right: 6px; }
        td.quantity { font-family: 'DejaVu Sans', sans-serif; direction: ltr; }
        tr:nth-child(even) td { background-color: #f5f5f5; }
        .totals-box { background-color: #f8f9fa; padding: 8px; border-radius: 3px; margin-top: 10px; border: 1px solid #333; text-align: right; direction: rtl; }
        .total-row { display: block; margin-bottom: 4px; font-size: 11px; font-weight: bold; direction: rtl; }
        .total-row.final { font-size: 14px; color: #000; background-color: #e9ecef; padding: 4px; border-radius: 2px; direction: rtl; }
        .signatures { position: fixed; bottom: 10px; left: 10px; right: 10px; }
        .signature-box { display: inline-block; width: 30%; text-align: center; border-top: 1px solid #000; padding-top: 10px; margin: 0 1.5%; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            <h2>#{{ $returnNumber }}</h2>
        </div>
        <div class="header-right">
            <h1>{{ $title }}</h1>
        </div>
    </div>

    <div class="info-box">
        <div class="info-row">
            {{ $marketerName }} :<span class="label">{{ $labels['marketer'] }}</span>
        </div>
        <div class="info-row">
            {{ $storeName }} :<span class="label">{{ $labels['store'] }}</span>
        </div>
        <div class="info-row">
            {{ $date }} :<span class="label">{{ $labels['date'] }}</span>
        </div>
        <div class="info-row">
            {{ $status }} :<span class="label">{{ $labels['status'] }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ $labels['total'] }}</th>
                <th>{{ $labels['price'] }}</th>
                <th>{{ $labels['quantity'] }}</th>
                <th>{{ $labels['product'] }}</th>
                <th>#</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td class="quantity">{{ $item->total_price }}</td>
                <td class="quantity">{{ $item->unit_price }}</td>
                <td class="quantity">{{ $item->quantity }}</td>
                <td class="product-name">{{ $item->name }}</td>
                <td class="quantity">{{ $index + 1 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-box">
        <div class="total-row final">
            {{ $labels['currency'] }} {{ $totalAmount }} :<span class="label">{{ $labels['finalTotal'] }}</span>
        </div>
    </div>

    <div class="signatures">
        <div class="signature-box">{{ $labels['store'] }}</div>
        <div class="signature-box">{{ $labels['marketer'] }}</div>
        <div class="signature-box">{{ $labels['keeper'] }}</div>
    </div>
</body>
</html>
