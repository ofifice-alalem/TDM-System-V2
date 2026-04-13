<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Client Invoices</title>
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
        @page { size: A4; margin: 20px 32px; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', 'DejaVu Sans', sans-serif; }
        body { font-size: 10px; color: #1e293b; background: #fff; }

        #page-header { position: fixed; top: -20px; left: 0; right: 0; background: #fff; border-bottom: 2px solid #0f172a; padding: 8px 32px; }
        #page-header .ph-tbl { display: table; width: 100%; }
        #page-header .ph-r { display: table-cell; vertical-align: middle; text-align: right; }
        #page-header .ph-c { display: table-cell; vertical-align: middle; text-align: center; width: 80px; }
        #page-header .ph-l { display: table-cell; vertical-align: middle; text-align: left; width: 120px; }
        #page-header .ph-title { font-size: 14px; font-weight: bold; color: #0f172a; }
        #page-header .ph-pagenum { font-size: 12px; border-bottom: 2px solid #0f172a; color: #0f172a; font-weight: bold; padding: 2px 10px; display: inline-block; }
        .page-num:before { content: counter(page); }
        @page { counter-increment: page; }
        body { margin-top: 80px; }

        table { width: 100%; border-collapse: collapse; table-layout: fixed; direction: ltr; }
        th { text-align: right; padding: 5px 6px; font-size: 8.5px; font-weight: bold; border: 1px solid #64748b; }
        td { padding: 4px 6px; font-size: 9px; color: #334155; text-align: right; border: 1px solid #94a3b8; background: #fff; }

        .client-head td { font-weight: bold; font-size: 9px; color: #fff; border: none; }
        .inv-head td { font-weight: bold; font-size: 8.5px; background: #f1f5f9; border-top: 2px solid #94a3b8; }
        .col-head th { background: #dce4ee; color: #0f172a; }
        .item-row td { background: #dce4ee; }
        .item-row-even td { background: #dce4ee; }
        .client-total td { background: #e8eaf6; font-weight: bold; border-top: 2px solid #0f172a; }

        td.num { text-align: center; font-weight: bold; color: #0f172a; }
        td.price-val { text-align: center; background: #fffbeb; color: #c2410c; font-weight: bold; }

        .spacer { height: 14px; }
        .footer { display: table; width: 100%; margin-top: 14px; padding-top: 8px; border-top: 1px solid #e2e8f0; }
        .footer-l { display: table-cell; text-align: right; font-size: 7.5px; color: #94a3b8; }
        .footer-r { display: table-cell; text-align: left; font-size: 7.5px; color: #94a3b8; }
    </style>
</head>
<body>

<div id="page-header">
    <div class="ph-tbl">
        <div class="ph-l">
            <img src="{{ public_path('logo.png') }}" style="max-height:65px; max-width:120px; display:block;">
        </div>
        <div class="ph-c">
            <span class="ph-pagenum"><span class="page-num"></span> / {{ $labels['totalPages'] }}</span>
        </div>
        <div class="ph-r">
            <div class="ph-title">{{ $labels['title'] }}</div>
            <div style="margin-top:3px; font-size:9px; color:#64748b; text-align:right;">
                {{ $labels['dateTo'] }} - {{ $labels['dateFrom'] }}
                @if(!empty($labels['filterEntity']))
                &nbsp;|&nbsp; {{ $labels['filterEntity'] }}
                @endif
                @if(!empty($labels['filterSearch']))
                &nbsp;|&nbsp; {{ $labels['filterSearch'] }}
                @endif
                @if(!empty($labels['filterStaff']))
                &nbsp;|&nbsp; {{ $labels['filterStaff'] }}
                @endif
                @if(!empty($labels['filterProd']))
                &nbsp;|&nbsp; {{ $labels['filterProd'] }}
                @endif
            </div>
        </div>
    </div>
</div>

@php $n = fn($v) => number_format((float)$v, 2); $ni = fn($v) => number_format((int)$v); @endphp

{{-- Cover Page --}}
<div style="page-break-after: always; min-height: 700px; text-align: center; padding: 60px 40px;">

    <div style="margin-bottom: 30px;">
        <img src="{{ public_path('images/company.png') }}" style="max-height:180px; max-width:350px;">
    </div>

    <div style="border-bottom: 3px solid #0f172a; padding-bottom: 16px; margin-bottom: 30px; width: 100%;">
        <div style="font-size: 22px; font-weight: bold; color: #0f172a; margin-bottom: 10px; text-align:center;">{{ $labels['title'] }}</div>
        <table style="width: auto; border-collapse: collapse; direction: rtl; margin: 0 auto;">
            <tr>
                <td style="border: none; padding: 4px 16px; text-align: center;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 3px;">{{ $labels['labelTo'] }}</div>
                    <div style="font-size: 16px; font-weight: bold; color: #0f172a; border: 1px solid #e2e8f0; padding: 6px 14px;">{{ $labels['dateTo'] }}</div>
                </td>
                <td style="border: none; padding: 4px 8px; font-size: 18px; color: #64748b; text-align: center;">&#8592;</td>
                <td style="border: none; padding: 4px 16px; text-align: center;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 3px;">{{ $labels['labelFrom'] }}</div>
                    <div style="font-size: 16px; font-weight: bold; color: #0f172a; border: 1px solid #e2e8f0; padding: 6px 14px;">{{ $labels['dateFrom'] }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table style="width: 80%; border-collapse: collapse; direction: rtl; margin: 0 auto; border: 2px solid #0f172a;">
        <tr style="background: #f8fafc; border-bottom: 2px solid #0f172a;">
            <td colspan="2" style="padding: 8px 12px; font-size: 10px; font-weight: bold; color: #64748b; text-align: center; border: none; letter-spacing: 1px;">&#x2014; {{ $labels['filterLabel'] ?? '' }} &#x2014;</td>
        </tr>
        <tr style="border-bottom: 1px solid #94a3b8;">
            <td style="padding: 10px 16px; font-size: 13px; font-weight: bold; color: #0f172a; text-align: right; border: none; border-left: 2px solid #0f172a;">{{ $labels['filterEntity'] }}</td>
            <td style="padding: 10px 16px; font-size: 11px; font-weight: bold; color: #64748b; text-align: right; width: 35%; border: none;">{{ $labels['filterEntityLabel'] }}</td>
        </tr>
        @if(!empty($labels['filterStaff']))
        <tr style="border-bottom: 1px solid #94a3b8;">
            <td style="padding: 10px 16px; font-size: 13px; font-weight: bold; color: #0f172a; text-align: right; border: none; border-left: 2px solid #0f172a;">{{ $labels['filterStaff'] }}</td>
            <td style="padding: 10px 16px; font-size: 11px; font-weight: bold; color: #64748b; text-align: right; border: none;">{{ $labels['filterStaffLabel'] }}</td>
        </tr>
        @endif
        @if(!empty($labels['filterSearch']))
        <tr style="border-bottom: 1px solid #94a3b8;">
            <td style="padding: 10px 16px; font-size: 13px; font-weight: bold; color: #0f172a; text-align: right; border: none; border-left: 2px solid #0f172a;">{{ $labels['filterSearch'] }}</td>
            <td style="padding: 10px 16px; font-size: 11px; font-weight: bold; color: #64748b; text-align: right; border: none;">{{ $labels['filterSearchLabel'] }}</td>
        </tr>
        @endif
        @if(!empty($labels['filterProd']))
        <tr style="border-bottom: 1px solid #94a3b8;">
            <td style="padding: 10px 16px; font-size: 13px; font-weight: bold; color: #0f172a; text-align: right; border: none; border-left: 2px solid #0f172a;">{{ $labels['filterProd'] }}</td>
            <td style="padding: 10px 16px; font-size: 11px; font-weight: bold; color: #64748b; text-align: right; border: none;">{{ $labels['filterProdLabel'] }}</td>
        </tr>
        @endif
    </table>

    <div style="margin-top: 40px; width: 90%; border-top: 2px solid #0f172a; padding-top: 20px; margin-left: auto; margin-right: auto;">
        <table style="width: 60%; border-collapse: collapse; direction: rtl; margin: 0 auto; border: 2px solid #0f172a;">
            <tr>
                <td style="padding: 14px 8px; text-align: center; border-left: 2px solid #0f172a; background: #f8fafc;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 6px; letter-spacing: 1px;">{{ $labels['amount'] }}</div>
                    <div style="font-size: 18px; font-weight: bold; color: #0f172a;">{{ $n($labels['grandAmount']) }}</div>
                </td>
                <td style="padding: 14px 8px; text-align: center; background: #f8fafc;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 6px; letter-spacing: 1px;">{{ $labels['invoices_label'] }}</div>
                    <div style="font-size: 18px; font-weight: bold; color: #0f172a;">{{ $labels['grandCount'] }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 40px; font-size: 9px; color: #94a3b8;">{{ now()->format('Y-m-d H:i') }}</div>
</div>

@foreach($entries as $entry)
<div style="page-break-inside: avoid; margin-bottom: 16px;">

{{-- رأس الزبون --}}
<table style="margin-bottom:0; border-collapse:collapse; width:100%;">
    <tr>
        <td style="background:#{{ $entry['color'] }}; padding:7px 12px; text-align:left; color:#fff; font-weight:bold; font-size:11px; width:22%; white-space:nowrap;">
            {{ $n($entry['total_amount']) }}
        </td>
        <td style="background:#{{ $entry['color'] }}; padding:7px 12px; text-align:center; color:#fff; font-size:10.5px; font-weight:bold; width:22%; white-space:nowrap;">
            {{ $entry['invoice_count'] }} {{ $labels['invoices_label'] }}
        </td>
        <td style="background:#{{ $entry['color'] }}; padding:7px 12px; text-align:right; color:#fff; font-weight:bold; font-size:11px;">
            {{ $entry['name'] }}
        </td>
    </tr>
</table>

@foreach($entry['invoices'] as $inv)
{{-- رأس الفاتورة --}}
<table style="margin-bottom:0; border-collapse:collapse; width:100%;">
    <tr style="background:#dce4ee; border-top:2px solid #94a3b8;">
        <td style="padding:5px 10px; text-align:left; font-weight:bold; font-size:10px; color:#0f172a; width:25%;">
            {{ $n($inv['total_amount']) }}
        </td>
        <td style="padding:5px 10px; text-align:center; font-size:10px; font-weight:bold; color:#1e293b; width:30%;">
            {{ $inv['date'] }}
        </td>
        <td style="padding:5px 10px; text-align:right; font-weight:bold; font-size:9.5px; color:#1e40af; width:45%;">
            {{ $inv['invoice_number'] }}
        </td>
    </tr>
</table>
{{-- أعمدة المنتجات --}}
<table style="margin-bottom:0; border-collapse:collapse; width:100%;">
    <tr class="col-head">
        <th style="width:18%; text-align:center;">{{ $labels['amount'] }}</th>
        <th style="width:17%; text-align:center;">{{ $labels['price'] }}</th>
        <th style="width:15%; text-align:center;">{{ $labels['qty'] }}</th>
        <th style="width:50%; text-align:right; padding-right:12px;">{{ $labels['product'] }}</th>
    </tr>
    @foreach($inv['items'] as $i => $item)
    <tr class="{{ $i % 2 !== 0 ? 'item-row-even' : 'item-row' }}">
        <td class="num" style="font-size:9px;">{{ $n($item['quantity'] * $item['unit_price']) }}</td>
        <td class="price-val" style="font-size:9px;">{{ $n($item['unit_price']) }}</td>
        <td class="num" style="font-size:9px;">{{ $ni($item['quantity']) }}</td>
        <td style="text-align:right; padding-right:12px; font-size:10px; font-weight:bold; color:#1e293b;">{{ $item['product_name'] }}</td>
    </tr>
    @endforeach
</table>
@endforeach

{{-- إجمالي الزبون --}}
<table style="margin-bottom:0; border-collapse:collapse; width:100%;">
    <tr class="client-total">
        <td class="num" style="width:18%; font-size:10px;">{{ $n($entry['total_amount']) }}</td>
        <td style="text-align:right; padding-right:12px; width:82%; color:#475569;">{{ $labels['total'] }}</td>
    </tr>
</table>

</div>
@endforeach

<div class="footer">
    <div class="footer-l">{{ $labels['title'] }}</div>
    <div class="footer-r">{{ now()->format('Y-m-d  H:i') }}</div>
</div>

</body>
</html>
