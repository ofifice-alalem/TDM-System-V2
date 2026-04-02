<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>فواتير مجمعة</title>
    <style>
        @font-face { font-family: 'Cairo'; src: url('{{ public_path("fonts/Cairo-Regular.ttf") }}') format('truetype'); font-weight: normal; }
        @font-face { font-family: 'Cairo'; src: url('{{ public_path("fonts/Cairo-Bold.ttf") }}') format('truetype'); font-weight: bold; }
        @font-face { font-family: 'Cairo'; src: url('{{ public_path("fonts/Cairo-ExtraBold.ttf") }}') format('truetype'); font-weight: 900; }
        @page { size: A4; margin: 5px; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', 'DejaVu Sans', sans-serif; }
        body { font-size: 13px; color: #000; direction: rtl; background: #fff; }
        .page-break { page-break-after: always; }

        .inv-container { padding: 5px 15px; background: white; position: relative; }
        .inv-header { border-bottom: 3px solid #000; padding: 10px 0; margin-bottom: 10px; display: table; width: 100%; }
        .inv-header-r { display: table-cell; text-align: right; width: 70%; vertical-align: top; }
        .inv-header-l { display: table-cell; text-align: left; width: 30%; vertical-align: top; }
        .inv-logo { max-height: 100px; width: auto; }
        .inv-company { font-size: 18px; font-weight: bold; margin-bottom: 3px; }
        .inv-title { font-size: 16px; font-weight: 600; margin-top: 3px; }
        .inv-number { margin-top: 5px; font-size: 13px; padding: 5px 10px; border: 2px solid #000; border-radius: 6px; display: inline-block; }
        .inv-info { margin-bottom: 10px; background: #f5f5f5; padding: 8px; border-radius: 10px; border-right: 4px solid #000; display: table; width: 100%; }
        .inv-info-row { display: table-row; }
        .inv-info-row > div { display: table-cell; width: 50%; padding: 3px 6px; font-size: 12px; }
        .inv-info-label { font-weight: bold; }
        .inv-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 10px; direction: rtl; border-radius: 10px; overflow: hidden; border: 1px solid #000; table-layout: fixed; }
        .inv-table th { background: #eee; text-align: right; padding: 5px 4px; font-size: 12px; font-weight: bold; border-bottom: 2px solid #000; }
        .inv-table td { border-bottom: 1px solid #ddd; padding: 4px; text-align: right; font-size: 11px; background: white; font-weight: bold; }
        .inv-table tbody tr:nth-child(even) td { background: #f5f5f5; }
        .inv-totals { float: right; width: 45%; padding: 5px; border-radius: 10px; border: 1px solid #ddd; margin-top: 10px; }
        .inv-totals-row { padding: 3px 0; font-size: 12px; text-align: right; }
        .inv-totals-final { background: #eee; margin-top: 5px; padding: 5px; border-radius: 10px; font-weight: bold; font-size: 13px; }
        .inv-invalid { position: absolute; top: 40%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 60px; color: rgba(220,53,69,0.2); font-weight: bold; border: 6px solid rgba(220,53,69,0.2); padding: 20px 40px; border-radius: 20px; white-space: nowrap; z-index: 1000; }

        .rcpt-container { border: 4px double #000; position: relative; }
        .rcpt-company-header { text-align: center; padding: 12px; border-bottom: 2px solid #e0e0e0; }
        .rcpt-company-header img { max-height: 100px; margin-bottom: 6px; }
        .rcpt-company-name { font-size: 16px; font-weight: bold; color: #333; }
        .rcpt-header { background: #eee; padding: 12px 20px; text-align: center; border-bottom: 3px solid #000; }
        .rcpt-title { font-size: 26px; font-weight: 900; margin: 0 0 5px 0; }
        .rcpt-number { font-size: 15px; font-weight: bold; }
        .rcpt-content { padding: 15px; }
        .rcpt-info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .rcpt-info-table td { padding: 8px 12px; font-size: 13px; border-bottom: 1px solid #e0e0e0; }
        .rcpt-info-table td.lbl { font-weight: 900; text-align: right; width: 35%; background: #f8f8f8; border-left: 4px solid #000; }
        .rcpt-info-table td.val { text-align: right; font-weight: bold; width: 65%; }
        .rcpt-amount { text-align: center; padding: 16px; background: #f8f8f8; border: 3px solid #000; margin: 15px 0; }
        .rcpt-amount .lbl { font-size: 13px; font-weight: 900; margin-bottom: 8px; }
        .rcpt-amount .val { font-size: 30px; font-weight: 900; }
    </style>
</head>
<body>

@php
$L = [
    'store'          => $g('المتجر'),
    'date'           => $g('التاريخ'),
    'phone'          => $g('الهاتف'),
    'marketer'       => $g('المسوق'),
    'status'         => $g('الحالة'),
    'approvedBy'     => $g('وثق بواسطة'),
    'rejectedBy'     => $g('رفض بواسطة'),
    'cancelledBy'    => $g('ألغى بواسطة'),
    'origInvoice'    => $g('الفاتورة الأصلية'),
    'product'        => $g('المنتج'),
    'qty'            => $g('الكمية'),
    'gift'           => $g('هدية'),
    'unitPrice'      => $g('سعر الوحدة'),
    'price'          => $g('السعر'),
    'total'          => $g('الإجمالي'),
    'totalProducts'  => $g('عدد المنتجات'),
    'subtotal'       => $g('المجموع الفرعي'),
    'prodDiscount'   => $g('خصم المنتجات'),
    'invDiscount'    => $g('خصم الفاتورة'),
    'grandTotal'     => $g('المجموع النهائي'),
    'payMethod'      => $g('طريقة الدفع'),
    'amount'         => $g('المبلغ المسدد'),
    'reqAmount'      => $g('المبلغ المطلوب'),
    'currency'       => $g('دينار'),
    'invalid'        => $g('لا يعتد بها'),
    'notes'          => $g('ملاحظات'),
    'goodsTotal'     => $g('إجمالي البضاعة'),
    'approvedAt'     => $g('تاريخ الموافقة'),
    'rejectedAt'     => $g('تاريخ الرفض'),
    'approvedByUser' => $g('تمت الموافقة بواسطة'),
    'rejectedByUser' => $g('تم الرفض بواسطة'),
];
@endphp

@foreach($invoices as $idx => $inv)
@php $isLast = $idx === count($invoices) - 1; @endphp

{{-- ===== فاتورة بيع ===== --}}
@if($inv['operation'] === 'sales')
<div class="inv-container">
    @if($inv['isInvalid'])<div class="inv-invalid">{!! $L['invalid'] !!}</div>@endif
    <div class="inv-header">
        <div class="inv-header-l">
            @if($inv['logoBase64'])<img src="data:image/png;base64,{{ $inv['logoBase64'] }}" class="inv-logo">@endif
        </div>
        <div class="inv-header-r">
            <div class="inv-company"><strong>{!! $inv['companyName'] !!}</strong></div>
            <div class="inv-title"><strong>{!! $inv['title'] !!}</strong></div>
            <div class="inv-number">#{{ $inv['invoiceNumber'] }}</div>
        </div>
    </div>
    <div class="inv-info">
        <div class="inv-info-row">
            <div>{!! $inv['storeName'] !!} :<span class="inv-info-label">{!! $L['store'] !!}</span></div>
            <div>{{ $inv['date'] }} :<span class="inv-info-label">{!! $L['date'] !!}</span></div>
        </div>
        <div class="inv-info-row">
            <div>{{ $inv['storePhone'] }} :<span class="inv-info-label">{!! $L['phone'] !!}</span></div>
            <div>{!! $inv['marketerName'] !!} :<span class="inv-info-label">{!! $L['marketer'] !!}</span></div>
        </div>
        <div class="inv-info-row">
            <div>{!! $inv['status'] !!} :<span class="inv-info-label">{!! $L['status'] !!}</span></div>
            <div>
                @if($inv['statusValue'] === 'approved' && $inv['keeperName'])
                    {!! $inv['keeperName'] !!}@if($inv['confirmedDate']) ({{ $inv['confirmedDate'] }})@endif :<span class="inv-info-label">{!! $L['approvedBy'] !!}</span>
                @elseif($inv['statusValue'] === 'rejected' && $inv['rejectedByName'])
                    {!! $inv['rejectedByName'] !!}@if($inv['rejectedDate']) ({{ $inv['rejectedDate'] }})@endif :<span class="inv-info-label">{!! $L['rejectedBy'] !!}</span>
                @endif
            </div>
        </div>
    </div>
    <table class="inv-table">
        <thead><tr>
            <th style="width:22%">{!! $L['total'] !!}</th>
            <th style="width:16%">{!! $L['unitPrice'] !!}</th>
            <th style="width:10%">{!! $L['gift'] !!}</th>
            <th style="width:10%">{!! $L['qty'] !!}</th>
            <th style="width:37%">{!! $L['product'] !!}</th>
            <th style="width:5%">#</th>
        </tr></thead>
        <tbody>
            @foreach($inv['items'] as $i => $item)
            <tr>
                <td>{!! $L['currency'] !!} {{ $item->totalPrice }}</td>
                <td>{!! $L['currency'] !!} {{ $item->unitPrice }}</td>
                <td>{{ $item->freeQuantity > 0 ? $item->freeQuantity : '---' }}</td>
                <td>{{ $item->totalQuantity }}</td>
                <td>{!! $item->name !!}</td>
                <td>{{ $i + 1 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="inv-totals">
        <div class="inv-totals-row">{{ $inv['totalProducts'] }} : <strong>{!! $L['totalProducts'] !!}</strong></div>
        <div class="inv-totals-row">{!! $L['currency'] !!} {{ $inv['subtotal'] }} : <strong>{!! $L['subtotal'] !!}</strong></div>
        @if($inv['productDiscount'] > 0)<div class="inv-totals-row">{!! $L['currency'] !!} {{ $inv['productDiscount'] }} : <strong>{!! $L['prodDiscount'] !!}</strong></div>@endif
        @if($inv['invoiceDiscount'] > 0)<div class="inv-totals-row">{!! $L['currency'] !!} {{ $inv['invoiceDiscount'] }} : <strong>{!! $L['invDiscount'] !!}</strong></div>@endif
        <div class="inv-totals-row inv-totals-final">{!! $L['currency'] !!} {{ $inv['totalAmount'] }} : <strong>{!! $L['grandTotal'] !!}</strong></div>
    </div>
</div>

{{-- ===== إيصال قبض ===== --}}
@elseif($inv['operation'] === 'payments')
<div class="rcpt-container">
    @if($inv['isInvalid'])<div class="inv-invalid">{!! $L['invalid'] !!}</div>@endif
    <div class="rcpt-company-header">
        @if($inv['logoBase64'])<img src="data:image/png;base64,{{ $inv['logoBase64'] }}" alt="">@endif
        <div class="rcpt-company-name">{!! $inv['companyName'] !!}</div>
    </div>
    <div class="rcpt-header">
        <div class="rcpt-title">{!! $inv['title'] !!}</div>
        <div class="rcpt-number">#{{ $inv['paymentNumber'] }}</div>
    </div>
    <div class="rcpt-content">
        <table class="rcpt-info-table">
            <tr><td class="val">{!! $inv['storeName'] !!}</td><td class="lbl">{!! $L['store'] !!}</td></tr>
            <tr><td class="val">{!! $inv['marketerName'] !!}</td><td class="lbl">{!! $L['marketer'] !!}</td></tr>
            <tr><td class="val">{{ $inv['date'] }}</td><td class="lbl">{!! $L['date'] !!}</td></tr>
            <tr><td class="val">{!! $inv['paymentMethod'] !!}</td><td class="lbl">{!! $L['payMethod'] !!}</td></tr>
            <tr><td class="val">{!! $inv['status'] !!}</td><td class="lbl">{!! $L['status'] !!}</td></tr>
            @if($inv['keeperName'])<tr><td class="val">{!! $inv['keeperName'] !!}@if($inv['confirmedDate']) ({{ $inv['confirmedDate'] }})@endif</td><td class="lbl">{!! $L['approvedBy'] !!}</td></tr>@endif
        </table>
        <div class="rcpt-amount">
            <div class="lbl">{!! $L['amount'] !!}</div>
            <div class="val">{!! $L['currency'] !!} {{ $inv['amount'] }}</div>
        </div>
    </div>
</div>

{{-- ===== إرجاع متجر ===== --}}
@elseif($inv['operation'] === 'sales_returns')
<div class="inv-container">
    @if($inv['isInvalid'])<div class="inv-invalid">{!! $L['invalid'] !!}</div>@endif
    <div class="inv-header">
        <div class="inv-header-l">
            @if($inv['logoBase64'])<img src="data:image/png;base64,{{ $inv['logoBase64'] }}" class="inv-logo">@endif
        </div>
        <div class="inv-header-r">
            <div class="inv-company"><strong>{!! $inv['companyName'] !!}</strong></div>
            <div class="inv-title"><strong>{!! $inv['title'] !!}</strong></div>
            <div class="inv-number">#{{ $inv['returnNumber'] }}</div>
        </div>
    </div>
    <div class="inv-info">
        <div class="inv-info-row">
            <div>{!! $inv['storeName'] !!} :<span class="inv-info-label">{!! $L['store'] !!}</span></div>
            <div>{{ $inv['date'] }} :<span class="inv-info-label">{!! $L['date'] !!}</span></div>
        </div>
        <div class="inv-info-row">
            <div>{!! $inv['marketerName'] !!} :<span class="inv-info-label">{!! $L['marketer'] !!}</span></div>
            <div>#{{ $inv['invoiceNumber'] }} :<span class="inv-info-label">{!! $L['origInvoice'] !!}</span></div>
        </div>
        <div class="inv-info-row">
            <div>{!! $inv['status'] !!} :<span class="inv-info-label">{!! $L['status'] !!}</span></div>
            <div>@if($inv['keeperName']){!! $inv['keeperName'] !!}@if($inv['confirmedDate']) ({{ $inv['confirmedDate'] }})@endif :<span class="inv-info-label">{!! $L['approvedBy'] !!}</span>@endif</div>
        </div>
    </div>
    <table class="inv-table">
        <thead><tr>
            <th style="width:22%">{!! $L['total'] !!}</th>
            <th style="width:16%">{!! $L['price'] !!}</th>
            <th style="width:12%">{!! $L['qty'] !!}</th>
            <th style="width:45%">{!! $L['product'] !!}</th>
            <th style="width:5%">#</th>
        </tr></thead>
        <tbody>
            @foreach($inv['items'] as $i => $item)
            <tr>
                <td>{!! $L['currency'] !!} {{ $item->total_price }}</td>
                <td>{!! $L['currency'] !!} {{ $item->unit_price }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{!! $item->name !!}</td>
                <td>{{ $i + 1 }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="inv-totals">
        <div class="inv-totals-row inv-totals-final">{!! $L['currency'] !!} {{ $inv['totalAmount'] }} : <strong>{!! $L['total'] !!}</strong></div>
    </div>
</div>

{{-- ===== طلب بضاعة / إرجاع بضاعة ===== --}}
@elseif(in_array($inv['operation'], ['requests', 'returns']))
<div class="inv-container">
    @if($inv['isInvalid'])<div class="inv-invalid">{!! $L['invalid'] !!}</div>@endif
    <div class="inv-header">
        <div class="inv-header-l">
            @if($inv['logoBase64'])<img src="data:image/png;base64,{{ $inv['logoBase64'] }}" class="inv-logo">@endif
        </div>
        <div class="inv-header-r">
            <div class="inv-company"><strong>{!! $inv['companyName'] !!}</strong></div>
            <div class="inv-title"><strong>{!! $inv['title'] !!}</strong></div>
            <div class="inv-number">#{{ $inv['invoiceNumber'] }}</div>
        </div>
    </div>
    <div class="inv-info">
        <div class="inv-info-row">
            <div>{!! $inv['marketerName'] !!} :<span class="inv-info-label">{!! $L['marketer'] !!}</span></div>
            <div>{{ $inv['date'] }} :<span class="inv-info-label">{!! $L['date'] !!}</span></div>
        </div>
        <div class="inv-info-row">
            <div>{!! $inv['status'] !!} :<span class="inv-info-label">{!! $L['status'] !!}</span></div>
            <div>
                @if($inv['rejectedBy'] ?? null){!! $inv['rejectedBy'] !!} :<span class="inv-info-label">{!! $L['rejectedBy'] !!}</span>
                @elseif($inv['approvedBy'] ?? null){!! $inv['approvedBy'] !!} :<span class="inv-info-label">{!! $L['approvedBy'] !!}</span>@endif
            </div>
        </div>
    </div>
    <table class="inv-table">
        <thead><tr>
            <th style="width:20%">{!! $L['qty'] !!}</th>
            <th style="width:75%">{!! $L['product'] !!}</th>
            <th style="width:5%">#</th>
        </tr></thead>
        <tbody>
            @foreach($inv['items'] as $i => $item)
            <tr>
                <td>{{ $item->quantity }}</td>
                <td>{!! $item->name !!}</td>
                <td>{{ $i + 1 }}</td>
            </tr>
            @endforeach
            <tr style="background:#eee !important;">
                <td style="font-weight:bold;font-size:13px;">{{ $inv['operation'] === 'returns' ? ($inv['totalQuantity'] ?? 0) : $inv['items']->sum('quantity') }}</td>
                <td colspan="2" style="font-weight:bold;font-size:13px;">{!! $L['goodsTotal'] !!}</td>
            </tr>
        </tbody>
    </table>
</div>

{{-- ===== سحب أرباح ===== --}}
@elseif($inv['operation'] === 'withdrawals')
<div class="rcpt-container">
    @if($inv['isInvalid'])<div class="inv-invalid">{!! $L['invalid'] !!}</div>@endif
    <div class="rcpt-company-header">
        @if($inv['logoBase64'])<img src="data:image/png;base64,{{ $inv['logoBase64'] }}" alt="">@endif
        <div class="rcpt-company-name">{!! $inv['companyName'] !!}</div>
    </div>
    <div class="rcpt-header">
        <div class="rcpt-title">{!! $inv['title'] !!}</div>
        <div class="rcpt-number">#WD-{{ $inv['withdrawalNumber'] }}</div>
    </div>
    <div class="rcpt-content">
        <table class="rcpt-info-table">
            <tr><td class="val">{!! $inv['marketerName'] !!}</td><td class="lbl">{!! $L['marketer'] !!}</td></tr>
            <tr><td class="val">{{ $inv['date'] }}</td><td class="lbl">{!! $L['date'] !!}</td></tr>
            <tr><td class="val">{!! $inv['status'] !!}</td><td class="lbl">{!! $L['status'] !!}</td></tr>
            @if($inv['approvedBy'])<tr><td class="val">{!! $inv['approvedBy'] !!}@if($inv['approvedAt']) ({{ $inv['approvedAt'] }})@endif</td><td class="lbl">{!! $L['approvedByUser'] !!}</td></tr>@endif
            @if($inv['rejectedBy'])<tr><td class="val">{!! $inv['rejectedBy'] !!}@if($inv['rejectedAt']) ({{ $inv['rejectedAt'] }})@endif</td><td class="lbl">{!! $L['rejectedByUser'] !!}</td></tr>@endif
            @if($inv['notes'])<tr><td class="val">{!! $inv['notes'] !!}</td><td class="lbl">{!! $L['notes'] !!}</td></tr>@endif
        </table>
        <div class="rcpt-amount">
            <div class="lbl">{!! $L['reqAmount'] !!}</div>
            <div class="val">{!! $L['currency'] !!} {{ $inv['amount'] }}</div>
        </div>
    </div>
</div>
@endif

@if(!$isLast)<div class="page-break"></div>@endif

@endforeach

</body>
</html>
