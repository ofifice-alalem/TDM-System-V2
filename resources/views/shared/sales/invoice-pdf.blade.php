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
        @page { margin: 5px; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', 'DejaVu Sans', sans-serif; }
        body { font-family: 'Cairo', 'DejaVu Sans', sans-serif; font-size: 13px; color: #000; direction: rtl; background: white; }
        .container { padding: 5px 15px; background: white; max-width: 800px; margin: 0 auto; }
        .header { border-bottom: 3px solid #000; padding: 10px 0; margin-bottom: 10px; display: table; width: 100%; }
        .header-right { display: table-cell; text-align: right; width: 70%; vertical-align: top; }
        .header-left { display: table-cell; text-align: left; width: 30%; vertical-align: top; }
        .logo { max-height: 190px; width: auto; }
        .company-name { font-size: 20px; font-weight: bold; margin-bottom: 3px; color: #000; }
        .invoice-title { font-size: 16px; font-weight: 600; margin-top: 3px; color: #000; }
        .invoice-number { margin-top: 5px; font-size: 13px; padding: 5px 10px; border: 2px solid #000; border-radius: 6px; display: inline-block; background: white; }
        .info-section { margin-bottom: 10px; background: #f5f5f5; padding: 8px; border-radius: 10px; border-right: 4px solid #000; display: table; width: 100%; }
        .info-row { display: table-row; }
        .info-row > div { display: table-cell; width: 50%; padding: 3px 6px; font-size: 13px; }
        .info-label { font-weight: bold; color: #000; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 10px; direction: rtl; border-radius: 10px; overflow: hidden; border: 1px solid #000; table-layout: fixed; }
        th { background: #eee; color: #000; text-align: right; padding: 5px 4px; font-size: 13px; font-weight: bold; border-bottom: 2px solid #000; }
        td { border-bottom: 1px solid #ddd; padding: 4px 4px; text-align: right; font-size: 12px; background: white; color: #000; font-weight: bold; }
        th:first-child { border-radius: 10px 0 0 0; }
        th:last-child { border-radius: 0 10px 0 0; }
        th:nth-child(1), td:nth-child(1) { width: 23%; }
        th:nth-child(2), td:nth-child(2) { width: 17%; }
        th:nth-child(3), td:nth-child(3) { width: 10%; }
        th:nth-child(4), td:nth-child(4) { width: 10%; }
        th:nth-child(5), td:nth-child(5) { width: 35%; }
        th:nth-child(6), td:nth-child(6) { width: 5%; }
        td:first-child { text-align: right; }
        tbody tr:nth-child(even) td { background-color: #f5f5f5; }
        tbody tr:last-child td:first-child { border-radius: 0 0 0 10px; }
        tbody tr:last-child td:last-child { border-radius: 0 0 10px 0; }
        .totals { margin-top: 10px; float: right; width: 45%; direction: rtl; padding: 5px; border-radius: 10px; border: 1px solid #ddd; }
        .totals-row { padding: 3px 0; direction: rtl; font-size: 13px; text-align: right; }
        .totals-row:last-child { border-bottom: none; }
        .totals-row.final { background: #eee; color: #000; margin-top: 5px; padding: 5px; border-radius: 10px; font-weight: bold; font-size: 14px; }
        .signatures { position: fixed; bottom: 55px; left: 15px; right: 15px; display: table; width: calc(100% - 30px); }
        .signature { display: table-cell; width: 50%; text-align: center; padding: 0 20px; font-weight: bold; }
        .signature span { display: inline-block; padding-top: 10px; border-top: 2px dotted #000; min-width: 150px; }
        .invalid-stamp { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 80px; color: rgba(220, 53, 69, 0.2); font-weight: bold; border: 8px solid rgba(220, 53, 69, 0.2); padding: 30px 50px; z-index: 1000; border-radius: 20px; white-space: nowrap; }
    </style>
</head>
<body>
    @if($isInvalid)
    <div class="invalid-stamp">{{ $labels['invalidInvoice'] }}</div>
    @endif

    <div class="container">
        <div class="header">
            <div class="header-left">
                @if($logoBase64)
                <img src="data:image/png;base64,{{ $logoBase64 }}" class="logo" alt="Logo">
                @endif
            </div>
            <div class="header-right">
                <div class="company-name"><strong>{!! $companyName !!}</strong></div>
                <div class="title" style="font-size: 18px;"><strong>{{ $title }}</strong></div>
                <div class="invoice-number">#{{ $invoiceNumber }}</div>
            </div>
        </div>

        <div class="info-section">
            <div class="info-row">
                <div>{{ $customerName }} :<span class="info-label">{!! $labels['store'] !!}</span></div>
                <div>{{ $date }} :<span class="info-label">{!! $labels['date'] !!}</span></div>
            </div>
            <div class="info-row">
                <div>{{ $customerPhone }} :<span class="info-label">{!! $labels['phone'] !!}</span></div>
                <div>{{ $employeeName }} :<span class="info-label">{!! $labels['employee'] !!}</span></div>
            </div>
            <div class="info-row">
                <div>{{ $status }} :<span class="info-label">{!! $labels['status'] !!}</span></div>
                @if($statusValue === 'approved' && $keeperName && $confirmedDate)
                <div>{{ $keeperName }} ({{ $confirmedDate }}) :<span class="info-label">{!! $labels['approvedBy'] !!}</span></div>
                @elseif($statusValue === 'rejected' && $rejectedByName && $rejectedDate)
                <div>{{ $rejectedByName }} ({{ $rejectedDate }}) :<span class="info-label">{!! $labels['rejectedBy'] !!}</span></div>
                @elseif($statusValue === 'cancelled' && $keeperName && $confirmedDate)
                <div>{{ $keeperName }} ({{ $confirmedDate }}) :<span class="info-label">{!! $labels['cancelledBy'] !!}</span></div>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>{!! $labels['total'] !!}</th>
                    <th>{!! $labels['unitPrice'] !!}</th>
                    <th>{!! $labels['free'] !!}</th>
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
                    <td>{{ $item->freeQuantity > 0 ? $item->freeQuantity : '---' }}</td>
                    <td>{{ $item->totalQuantity }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $index + 1 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <div class="totals-row">
                {{ $totalProducts }} : <strong>{!! $labels['totalProducts'] !!}</strong>
            </div>
            <div class="totals-row">
                {!! $currency !!} {{ $subtotal }} : <strong>{!! $labels['subtotal'] !!}</strong>
            </div>
            @if($productDiscount > 0)
            <div class="totals-row">
                {!! $currency !!} {{ $productDiscount }} : <strong>{!! $labels['productDiscount'] !!}</strong>
            </div>
            @endif
            @if($invoiceDiscount > 0)
            <div class="totals-row">
                {!! $currency !!} {{ $invoiceDiscount }} : <strong>{!! $labels['invoiceDiscount'] !!}</strong>
            </div>
            @endif
            <div class="totals-row final">
                {!! $currency !!} {{ $totalAmount }} : <strong>{!! $labels['grandTotal'] !!}</strong>
            </div>
        </div>

        <div class="signatures">
            <div class="signature"><span>{!! $labels['marketer'] !!}</span></div>
            <div class="signature"><span>{!! $labels['store'] !!}</span></div>
        </div>
    </div>
</body>
</html>
