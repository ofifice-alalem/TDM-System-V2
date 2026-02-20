<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>فاتورة {{ $invoiceNumber }}</title>
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path("fonts/Cairo-Regular.ttf") }}') format('truetype');
            font-weight: normal;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path("fonts/Cairo-Bold.ttf") }}') format('truetype');
            font-weight: bold;
        }
        @page { margin: 15px; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', 'DejaVu Sans', sans-serif; }
        body { font-family: 'Cairo', 'DejaVu Sans', sans-serif; font-size: 11px; color: #000; direction: rtl; background: white; }
        .container { padding: 20px; background: white; max-width: 800px; margin: 0 auto; }
        .header { border-bottom: 3px solid #000; padding: 20px 0; margin-bottom: 20px; text-align: center; }
        .logo { width: 120px; height: auto; margin-bottom: 10px; }
        .company-name { font-size: 20px; font-weight: bold; margin-bottom: 5px; color: #000; }
        .invoice-title { font-size: 16px; font-weight: 600; margin-top: 5px; color: #000; }
        .invoice-number { margin-top: 10px; font-size: 13px; padding: 8px 15px; border: 2px solid #000; border-radius: 6px; display: inline-block; background: white; }
        .info-section { margin-bottom: 20px; background: #f5f5f5; padding: 15px; border-radius: 10px; border-right: 4px solid #000; display: table; width: 100%; }
        .info-row { display: table-row; }
        .info-row > div { display: table-cell; width: 50%; padding: 8px 10px; font-size: 11px; }
        .info-label { font-weight: bold; color: #000; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 20px; direction: rtl; border-radius: 10px; overflow: hidden; border: 1px solid #000; }
        th { background: #000; color: white; font-weight: bold; text-align: center; padding: 12px 8px; font-size: 11px; }
        td { border-bottom: 1px solid #ddd; padding: 10px 8px; text-align: right; font-size: 10px; background: white; }
        th:first-child { border-radius: 10px 0 0 0; }
        th:last-child { border-radius: 0 10px 0 0; }
        td:first-child { text-align: center; }
        tbody tr:nth-child(even) td { background-color: #f5f5f5; }
        tbody tr:last-child td:first-child { border-radius: 0 0 0 10px; }
        tbody tr:last-child td:last-child { border-radius: 0 0 10px 0; }
        .totals { margin-top: 20px; float: right; width: 45%; direction: rtl; background: #f5f5f5; padding: 15px; border-radius: 10px; border: 1px solid #ddd; }
        .totals-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #ddd; direction: rtl; font-size: 11px; }
        .totals-row:last-child { border-bottom: none; }
        .totals-row.final { background: #000; color: white; margin: 10px -15px -15px -15px; padding: 15px; border-radius: 0 0 10px 10px; font-weight: bold; font-size: 14px; border: none; }
        .notes { clear: both; margin-top: 20px; padding: 15px; border-right: 4px solid #000; background: #f5f5f5; border-radius: 8px; font-size: 10px; }
        .invalid-stamp { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 100px; color: rgba(220, 53, 69, 0.15); font-weight: bold; border: 10px solid rgba(220, 53, 69, 0.15); padding: 20px; z-index: 1000; border-radius: 20px; }
    </style>
</head>
<body>
    @if($isInvalid)
    <div class="invalid-stamp">{{ $labels['invalidInvoice'] }}</div>
    @endif

    <div class="container">
        <div class="header">
            @if($logoBase64)
            <img src="data:image/png;base64,{{ $logoBase64 }}" class="logo" alt="Logo">
            @endif
            <div class="company-name">{!! $companyName !!}</div>
            <div class="invoice-title">{!! $title !!}</div>
            <div class="invoice-number">{{ $invoiceNumber }} :<strong>{!! $labels['invoiceNumber'] !!}</strong></div>
        </div>

        <div class="info-section">
            <div class="info-row">
                <div>{{ $customerName }} :<span class="info-label">{!! $labels['customer'] !!}</span></div>
                <div>{{ $date }} :<span class="info-label">{!! $labels['date'] !!}</span></div>
            </div>
            <div class="info-row">
                <div>{{ $customerPhone }} :<span class="info-label">{!! $labels['phone'] !!}</span></div>
                <div>{{ $employeeName }} :<span class="info-label">{!! $labels['employee'] !!}</span></div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>{!! $labels['total'] !!}</th>
                    <th>{!! $labels['unitPrice'] !!}</th>
                    <th>{!! $labels['quantity'] !!}</th>
                    <th>{!! $labels['product'] !!}</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td>{!! $currency !!} {{ $item->totalPrice }}</td>
                    <td>{!! $currency !!} {{ $item->unitPrice }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $index + 1 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                <span>{!! $currency !!} {{ $subtotal }}</span>
                <span><strong>{!! $labels['subtotal'] !!}:</strong></span>
            </div>
            @if($discountAmount > 0)
            <div class="totals-row">
                <span>{!! $currency !!} {{ $discountAmount }}</span>
                <span><strong>{!! $labels['discount'] !!}:</strong></span>
            </div>
            @endif
            <div class="totals-row final">
                <span>{!! $currency !!} {{ $totalAmount }}</span>
                <span><strong>{!! $labels['grandTotal'] !!}:</strong></span>
            </div>
        </div>

        @if($notes)
        <div class="notes">
            <strong>{!! $labels['notes'] !!}:</strong><br>
            {{ $notes }}
        </div>
        @endif
    </div>
</body>
</html>
