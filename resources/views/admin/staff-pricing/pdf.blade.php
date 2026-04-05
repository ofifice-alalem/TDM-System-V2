<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Staff Pricing</title>
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
        #page-header .ph-sub { margin-top: 2px; font-size: 8px; color: #64748b; }
        #page-header .ph-pagenum { font-size: 12px; border-bottom: 2px solid #0f172a; color: #0f172a; font-weight: bold; padding: 2px 10px; display: inline-block; }
        .page-num:before { content: counter(page); }
        @page { counter-increment: page; }
        body { margin-top: 80px; }

        table { width: 100%; border-collapse: collapse; table-layout: fixed; direction: ltr; }
        th { text-align: right; padding: 5px 6px; font-size: 7.5px; font-weight: bold; border: 1px solid #64748b; }
        td { padding: 4px 6px; font-size: 8px; color: #334155; text-align: right; border: 1px solid #94a3b8; background: #fff; }

        .staff-head td { font-weight: bold; font-size: 9px; color: #fff; border: none; }
        .col-head th { background: #dce4ee; color: #0f172a; }
        .product-row td { background: #f1f5f9; font-weight: bold; border-top: 2px solid #94a3b8; }
        .price-row td { background: #fff; }
        .price-row-even td { background: #fafafa; }
        .total-row td { background: #e8eaf6; font-weight: bold; border-top: 2px solid #0f172a; }

        td.num { text-align: center; font-weight: bold; color: #0f172a; }
        td.avg { text-align: center; color: #b45309; font-weight: bold; }
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
            <div class="ph-sub">
                {{ $labels['dateTo'] }} &nbsp;&#8592;&nbsp; {{ $labels['dateFrom'] }}
                @if($labels['filterProd'])
                &nbsp;|&nbsp; {{ $labels['filterProd'] }}
                @endif
                @if($labels['filterSort'])
                &nbsp;|&nbsp; {{ $labels['filterSort'] }}
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
                    <div style="font-size: 9px; color: #94a3b8; font-weight: bold; margin-bottom: 3px;">{{ $labels['labelTo'] }}</div>
                    <div style="font-size: 16px; font-weight: bold; color: #0f172a; background: #f1f5f9; padding: 6px 14px; border-radius: 4px;">{{ $labels['dateTo'] }}</div>
                </td>
                <td style="border: none; padding: 4px 8px; font-size: 18px; color: #94a3b8; text-align: center;">&#8592;</td>
                <td style="border: none; padding: 4px 16px; text-align: center;">
                    <div style="font-size: 9px; color: #94a3b8; font-weight: bold; margin-bottom: 3px;">{{ $labels['labelFrom'] }}</div>
                    <div style="font-size: 16px; font-weight: bold; color: #0f172a; background: #f1f5f9; padding: 6px 14px; border-radius: 4px;">{{ $labels['dateFrom'] }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table style="width: 80%; border-collapse: collapse; direction: rtl; margin: 0 auto;">
        <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 10px 12px; font-size: 12px; font-weight: bold; color: #0f172a; text-align: right; border: none;">
                @if(count($entries) === 1)
                    {{ $entries[0]['name'] }}
                @else
                    {{ $labels['allStaff'] }}
                @endif
            </td>
            <td style="padding: 10px 12px; font-size: 11px; font-weight: bold; color: #64748b; text-align: right; width: 40%; border: none;">{{ $labels['staffLabel'] }}</td>
        </tr>
        @if(!empty($labels['filterProd']))
        <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 10px 12px; font-size: 12px; font-weight: bold; color: #0f172a; text-align: right; border: none;">{{ $labels['filterProd'] }}</td>
            <td style="padding: 10px 12px; font-size: 11px; font-weight: bold; color: #64748b; text-align: right; border: none;">{{ $labels['filterProdLabel'] }}</td>
        </tr>
        @else
        <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 10px 12px; font-size: 12px; font-weight: bold; color: #0f172a; text-align: right; border: none;">{{ $labels['allProducts'] }}</td>
            <td style="padding: 10px 12px; font-size: 11px; font-weight: bold; color: #64748b; text-align: right; border: none;">{{ $labels['filterProdLabel'] }}</td>
        </tr>
        @endif
        @if(!empty($labels['filterSort']))
        <tr style="border-bottom: 1px solid #e2e8f0;">
            <td style="padding: 10px 12px; font-size: 12px; font-weight: bold; color: #0f172a; text-align: right; border: none;">{{ $labels['filterSort'] }}</td>
            <td style="padding: 10px 12px; font-size: 11px; font-weight: bold; color: #64748b; text-align: right; border: none;">{{ $labels['filterSortLabel'] }}</td>
        </tr>
        @endif
    </table>

    <div style="margin-top: 40px; width: 90%; border-top: 2px solid #0f172a; padding-top: 20px; margin-left: auto; margin-right: auto;">
        <table style="width: 60%; border-collapse: collapse; direction: rtl; margin: 0 auto;">
            <tr>
                <td style="padding: 12px 8px; text-align: center; border: 1px solid #e2e8f0; background: #f0fdf4;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 6px;">{{ $labels['amount'] }}</div>
                    <div style="font-size: 15px; font-weight: bold; color: #16a34a;">{{ $n($labels['grandAmount']) }}</div>
                </td>
                <td style="padding: 12px 8px; text-align: center; border: 1px solid #e2e8f0; background: #f8fafc;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 6px;">{{ $labels['qty'] }}</div>
                    <div style="font-size: 15px; font-weight: bold; color: #0f172a;">{{ $ni($labels['grandQty']) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 40px; font-size: 9px; color: #94a3b8;">{{ now()->format('Y-m-d H:i') }}</div>

</div>

@foreach($entries as $entry)
<div style="{{ !$loop->first ? 'page-break-before: always;' : '' }}">
{{-- Staff header - separate table --}}
<table style="margin-bottom:0; border-collapse:collapse; width:100%;">
    <tr class="staff-head">
        <td style="background:#{{ $entry['color'] }}; padding:6px 10px; text-align:right;">
            {{ $entry['name'] }} &nbsp;({{ $entry['role'] }})
        </td>
    </tr>
</table>
{{-- Products table --}}
<table style="margin-bottom:0;">
    <tr class="col-head">
        <th style="width:20%; text-align:center">{{ $labels['amount'] }}</th>
        <th style="width:15%; text-align:center">{{ $labels['qty'] }}</th>
        <th style="width:15%; text-align:center">{{ $labels['times'] }}</th>
        <th style="width:15%; text-align:center">{{ $labels['price'] }}</th>
        <th style="width:35%; text-align:right">{{ $labels['product'] }}</th>
    </tr>
    @foreach($entry['products'] as $product)
    <tr class="product-row">
        <td class="num">{{ $n($product['total_amount']) }}</td>
        <td class="num">{{ $ni($product['total_qty']) }}</td>
        <td class="num">{{ $product['times'] }}</td>
        <td class="avg">{{ $n($product['avg_price']) }} {{ $labels['avg'] }}</td>
        <td>{{ $product['product_name'] }}</td>
    </tr>
    @foreach($product['prices'] as $i => $price)
    <tr class="{{ $i % 2 !== 0 ? 'price-row-even' : 'price-row' }}">
        <td class="num" style="color:#475569;">{{ $n($price['total_amount']) }}</td>
        <td class="num" style="color:#475569;">{{ $ni($price['total_qty']) }}</td>
        <td class="num" style="color:#475569;">{{ $price['times'] }}</td>
        <td class="price-val">{{ $n($price['price']) }}</td>
        <td style="padding-right:18px; color:#64748b;">{{ $labels['price'] }} {{ $i + 1 }}</td>
    </tr>
    @endforeach
    @endforeach
    <tr class="total-row">
        <td class="num">{{ $n($entry['total_amount']) }}</td>
        <td class="num">{{ $ni($entry['total_qty']) }}</td>
        <td></td>
        <td colspan="2" style="color:#475569; text-align:right;">{{ $labels['total'] }}</td>
    </tr>
</table>
<div class="spacer"></div>
</div>
@endforeach

<div class="footer">
    <div class="footer-l">{{ $labels['title'] }}</div>
    <div class="footer-r">{{ now()->format('Y-m-d  H:i') }}</div>
</div>

</body>
</html>
