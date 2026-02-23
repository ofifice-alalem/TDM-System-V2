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
        @page { margin: 0; size: 80mm auto; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', 'DejaVu Sans', sans-serif; page-break-inside: avoid; }
        body { font-family: 'Cairo', 'DejaVu Sans', sans-serif; font-size: 12px; color: #000; direction: rtl; background: white; padding: 2mm 2mm 30mm 2mm; width: 76mm; max-width: 76mm; margin: 0 auto; page-break-inside: avoid; }
        .header, .info-section, .items-table, .totals, .footer { page-break-inside: avoid; }
        table { page-break-inside: avoid; }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 2mm; margin-bottom: 2mm; }
        .logo { max-height: 30mm; width: auto; margin-bottom: 2mm; display: none; }
        .company-name { font-size: 14px; font-weight: bold; margin-bottom: 1mm; }
        .invoice-title { font-size: 13px; font-weight: bold; margin: 1mm 0; }
        .invoice-number { font-size: 12px; font-weight: bold; }
        .info-section { margin-bottom: 2mm; font-size: 11px; line-height: 1.3; }
        .info-row { display: table; width: 100%; margin-bottom: 0.5mm; }
        .info-row > div { display: table-cell; width: 50%; }
        .info-label { font-weight: bold; }
        .items-table { width: 100%; border-collapse: collapse; margin: 2mm 0; font-size: 10px; }
        .items-table th { background: #f0f0f0; padding: 1.5mm; text-align: right; font-weight: bold; border-bottom: 1px solid #000; font-size: 10px; }
        .items-table td { padding: 1.5mm 1mm; text-align: right; border-bottom: 1px dotted #ccc; }
        .items-table .item-name { font-weight: bold; }
        .totals { margin-top: 2mm; padding-top: 1.5mm; border-top: 2px dashed #000; font-size: 11px; }
        .totals-row { display: table; width: 100%; margin-bottom: 0.5mm; direction: rtl; }
        .totals-row > div { display: table-cell; }
        .totals-row .label { text-align: right; font-weight: bold; width: 50%; }
        .totals-row .value { text-align: left; font-weight: bold; width: 50%; }
        .totals-row.final { font-size: 14px; margin-top: 1.5mm; padding-top: 1.5mm; border-top: 2px solid #000; }
        .footer { text-align: center; margin-top: 3mm; padding-top: 2mm; border-top: 2px dashed #000; font-size: 11px; }
        .invalid-stamp { text-align: center; font-size: 16px; font-weight: bold; color: #dc3545; margin: 2mm 0; padding: 1.5mm; border: 2px solid #dc3545; }
    </style>
</head>
<body>
    <div class="header">
        @if($logoBase64)
        <img src="data:image/png;base64,{{ $logoBase64 }}" class="logo" alt="Logo">
        @endif
        <div class="company-name">{!! $companyName !!}</div>
        <div class="invoice-title">{!! $title !!}</div>
        <div class="invoice-number">#{{ $invoiceNumber }}</div>
    </div>

    @if($isInvalid)
    <div class="invalid-stamp">{!! $labels['invalidInvoice'] !!}</div>
    @endif

    <div class="info-section">
        <div class="info-row">
            <div>{!! $storeName !!} :<span class="info-label">{!! $labels['store'] !!}</span></div>
        </div>
        <div class="info-row">
            <div>{{ $customerPhone }} :<span class="info-label">{!! $labels['phone'] !!}</span></div>
        </div>
        <div class="info-row">
            <div>{!! $marketerName !!} :<span class="info-label">{!! $labels['marketer'] !!}</span></div>
        </div>
        <div class="info-row">
            <div>{{ $date }} :<span class="info-label">{!! $labels['date'] !!}</span></div>
        </div>
        <div class="info-row">
            <div>{!! $status !!} :<span class="info-label">{!! $labels['status'] !!}</span></div>
        </div>
        @if($statusValue === 'approved' && $keeperName && $confirmedDate)
        <div class="info-row">
            <div>{!! $keeperName !!} ({{ $confirmedDate }}) :<span class="info-label">{!! $labels['approvedBy'] !!}</span></div>
        </div>
        @elseif($statusValue === 'rejected' && $rejectedByName && $rejectedDate)
        <div class="info-row">
            <div>{!! $rejectedByName !!} ({{ $rejectedDate }}) :<span class="info-label">{!! $labels['rejectedBy'] !!}</span></div>
        </div>
        @endif
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 20%; text-align: right;">{!! $labels['total'] !!}</th>
                <th style="width: 15%; text-align: center;">{!! $labels['free'] !!}</th>
                <th style="width: 15%; text-align: center;">{!! $labels['quantity'] !!}</th>
                <th style="width: 50%; text-align: right;">{!! $labels['product'] !!}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td style="text-align: right;">{{ $item->totalPrice }}</td>
                <td style="text-align: center;">{{ $item->freeQuantity > 0 ? $item->freeQuantity : '---' }}</td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td class="item-name" style="text-align: right;">{{ $item->name }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="totals-row">
            <div class="value">{{ $subtotal }} {!! $labels['currency'] !!}</div>
            <div class="label">:{!! $labels['subtotal'] !!}</div>
        </div>
        @if($productDiscount > 0)
        <div class="totals-row">
            <div class="value">{{ $productDiscount }} {!! $labels['currency'] !!} -</div>
            <div class="label">:{!! $labels['productDiscount'] !!}</div>
        </div>
        @endif
        @if($invoiceDiscount > 0)
        <div class="totals-row">
            <div class="value">{{ $invoiceDiscount }} {!! $labels['currency'] !!} -</div>
            <div class="label">:{!! $labels['invoiceDiscount'] !!}</div>
        </div>
        @endif
        <div class="totals-row final">
            <div class="value">{{ $totalAmount }} {!! $labels['currency'] !!}</div>
            <div class="label">:{!! $labels['finalTotal'] !!}</div>
        </div>
    </div>

    <div class="footer">
        <div style="margin-bottom: 2mm;">{!! $thankYou !!}</div>
        <div style="font-size: 14px;">{{ $date }}</div>
    </div>
</body>
</html>
