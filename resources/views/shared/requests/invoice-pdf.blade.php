<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>طلب بضاعة</title>
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
        .signatures { position: fixed; bottom: 10px; left: 10px; right: 10px; }
        .signature-box { display: inline-block; width: 45%; text-align: center; border-top: 1px solid #000; padding-top: 10px; margin: 0 2%; font-size: 10px; }
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
                    {{ $marketerName }} :<span class="label">{{ $labels['marketer'] }}</span>
                </div>
                <div class="info-row">
                    {{ $date }} :<span class="label">{{ $labels['date'] }}</span>
                </div>
                <div class="info-row">
                    {{ $status }} :<span class="label">{{ $labels['status'] }}</span>
                </div>
                @if(isset($approvedBy))
                <div class="info-row">
                    {{ $approvedBy }} :<span class="label">{{ $labels['approvedBy'] }}</span>
                </div>
                @endif
                @if(isset($rejectedBy))
                <div class="info-row">
                    {{ $rejectedBy }} :<span class="label">{{ $labels['rejectedBy'] }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>{{ $labels['quantity'] }}</th>
                <th>{{ $labels['product'] }}</th>
                <th>#</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
            <tr>
                <td class="quantity">{{ $item->quantity }}</td>
                <td class="product-name">{{ $item->name }}</td>
                <td class="quantity">{{ $index + 1 }}</td>
            </tr>
            @endforeach
            <tr style="background-color: #f0f0f0; color: #000; font-weight: bold;">
                <td class="quantity">{{ array_sum(array_column($items->toArray(), 'quantity')) }}</td>
                <td class="product-name">{{ $labels['total'] }}</td>
                <td>-</td>
            </tr>
        </tbody>
    </table>

    <div class="signatures">
        <div class="signature-box">{{ $labels['marketer'] }}</div>
        <div class="signature-box">{{ $labels['keeper'] }}</div>
    </div>
</body>
</html>
