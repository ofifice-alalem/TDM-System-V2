<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $labels['title'] }}</title>
    <style>
        @font-face { font-family: 'Cairo'; src: url('{{ public_path("fonts/Cairo-Regular.ttf") }}') format('truetype'); font-weight: normal; }
        @font-face { font-family: 'Cairo'; src: url('{{ public_path("fonts/Cairo-Bold.ttf") }}') format('truetype'); font-weight: bold; }
        @page { size: A4; margin: 20px 32px; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', 'DejaVu Sans', sans-serif; }
        body { font-size: 10px; color: #1e293b; direction: rtl; background: #fff; margin-top: 80px; }

        #page-header { position: fixed; top: -20px; left: 0; right: 0; background: #fff; border-bottom: 2px solid #0f172a; padding: 8px 32px; }
        #page-header .ph-tbl { display: table; width: 100%; }
        #page-header .ph-r { display: table-cell; vertical-align: middle; text-align: right; }
        #page-header .ph-c { display: table-cell; vertical-align: middle; text-align: center; width: 80px; }
        #page-header .ph-l { display: table-cell; vertical-align: middle; text-align: left; width: 110px; }
        #page-header .ph-title { font-size: 14px; font-weight: bold; color: #0f172a; }
        #page-header .ph-sub   { font-size: 9px; color: #64748b; margin-top: 2px; }
        #page-header .ph-sub span { color: #0f172a; font-weight: bold; }
        #page-header .ph-pagenum { font-size: 12px; border-bottom: 2px solid #0f172a; color: #0f172a; font-weight: bold; padding: 2px 10px; display: inline-block; }
        .page-num:before { content: counter(page); }
        @page { counter-increment: page; }

        .card-bg { background: #f8fafc; border: 1px solid #e2e8f0; border-top: 3px solid #0f172a; padding: 10px 12px; }
        .card-title { font-size: 8px; font-weight: bold; color: #64748b; margin-bottom: 6px; }
        .cl { text-align: right; color: #475569; font-size: 8.5px; font-weight: bold; padding: 2px 4px 2px 0; }
        .cv { text-align: left; color: #0f172a; font-weight: bold; font-size: 9px; padding: 2px 0 2px 4px; }

        table { width: 100%; border-collapse: collapse; direction: ltr; table-layout: fixed; }
        thead tr { background: #dce4ee; }
        th { color: #0f172a; text-align: right; padding: 6px 5px; font-size: 7.5px; font-weight: bold; border: 1px solid #64748b; }
        td { padding: 5px 5px; font-size: 8px; color: #334155; text-align: right; border: 1px solid #94a3b8; background: #fff; }
        tr.row-even td { background: #f8fafc; }
        td.num { font-weight: bold; color: #0f172a; }
        td.idx-td { color: #64748b; font-size: 7.5px; text-align: center; }
        tfoot tr td { background: #f1f5f9; font-weight: bold; font-size: 8px; padding: 6px 5px; border: 1px solid #94a3b8; border-top: 2px solid #0f172a; }

        .s-pending   { color: #d97706; font-weight: bold; }
        .s-approved  { color: #16a34a; font-weight: bold; }
        .s-cancelled { color: #6b7280; }
        .s-rejected  { color: #dc2626; font-weight: bold; }

        .footer { display: table; width: 100%; margin-top: 14px; padding-top: 8px; border-top: 1px solid #e2e8f0; }
        .footer-r { display: table-cell; text-align: right; font-size: 7.5px; color: #94a3b8; }
        .footer-l { display: table-cell; text-align: left; font-size: 7.5px; color: #94a3b8; }
    </style>
</head>
<body>

<div id="page-header">
    <div class="ph-tbl">
        <div class="ph-l">
            <img src="{{ public_path('logo.png') }}" style="max-height:55px; max-width:110px; display:block;">
        </div>
        <div class="ph-c">
            <span class="ph-pagenum"><span class="page-num"></span> / {{ $labels['totalPages'] ?? '' }}</span>
        </div>
        <div class="ph-r">
            <div class="ph-title">{{ $labels['title'] }}</div>
            <div class="ph-sub">
                {{ $labels['entityName'] }}
                &nbsp;|&nbsp;
                <span>{{ $labels['dateTo'] }}</span>
                &nbsp;&#8592;&nbsp;
                <span>{{ $labels['dateFrom'] }}</span>
            </div>
        </div>
    </div>
</div>

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
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 3px;">{{ $labels['labelTo'] ?? '' }}</div>
                    <div style="font-size: 16px; font-weight: bold; color: #0f172a; border: 1px solid #e2e8f0; padding: 6px 14px;">{{ $labels['dateTo'] }}</div>
                </td>
                <td style="border: none; padding: 4px 8px; font-size: 18px; color: #64748b; text-align: center;">&#8592;</td>
                <td style="border: none; padding: 4px 16px; text-align: center;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 3px;">{{ $labels['labelFrom'] ?? '' }}</div>
                    <div style="font-size: 16px; font-weight: bold; color: #0f172a; border: 1px solid #e2e8f0; padding: 6px 14px;">{{ $labels['dateFrom'] }}</div>
                </td>
            </tr>
        </table>
    </div>

    <table style="width: 80%; border-collapse: collapse; direction: rtl; margin: 0 auto; border: 2px solid #0f172a;">
        <tr style="background: #f8fafc; border-bottom: 2px solid #0f172a;">
            <td colspan="2" style="padding: 8px 12px; font-size: 10px; font-weight: bold; color: #64748b; text-align: center; border: none; letter-spacing: 1px;">&#x2014; {{ $labels['filterInfo'] }} &#x2014;</td>
        </tr>
        <tr style="border-bottom: 1px solid #94a3b8;">
            <td style="padding: 10px 16px; font-size: 13px; font-weight: bold; color: #0f172a; text-align: right; border: none; border-left: 2px solid #0f172a;">{{ $labels['operationName'] }}</td>
            <td style="padding: 10px 16px; font-size: 11px; font-weight: bold; color: #64748b; text-align: right; width: 35%; border: none;">{{ $labels['operation'] }}</td>
        </tr>
        <tr style="border-bottom: 1px solid #94a3b8;">
            <td style="padding: 10px 16px; font-size: 13px; font-weight: bold; color: #0f172a; text-align: right; border: none; border-left: 2px solid #0f172a;">{{ $labels['entityName'] }}</td>
            <td style="padding: 10px 16px; font-size: 11px; font-weight: bold; color: #64748b; text-align: right; border: none;">{{ $labels['entityLabel'] }}</td>
        </tr>
        @if(!empty($labels['storeName']))
        <tr style="border-bottom: 1px solid #94a3b8;">
            <td style="padding: 10px 16px; font-size: 13px; font-weight: bold; color: #0f172a; text-align: right; border: none; border-left: 2px solid #0f172a;">{{ $labels['storeName'] }}</td>
            <td style="padding: 10px 16px; font-size: 11px; font-weight: bold; color: #64748b; text-align: right; border: none;">{{ $labels['store'] }}</td>
        </tr>
        @endif
        @if(!empty($labels['statusName']))
        <tr style="border-bottom: 1px solid #94a3b8;">
            <td style="padding: 10px 16px; font-size: 13px; font-weight: bold; color: #0f172a; text-align: right; border: none; border-left: 2px solid #0f172a;">{{ $labels['statusName'] }}</td>
            <td style="padding: 10px 16px; font-size: 11px; font-weight: bold; color: #64748b; text-align: right; border: none;">{{ $labels['status'] }}</td>
        </tr>
        @endif
    </table>

    @if(isset($data['status_totals']) && $data['status_totals'])
    <div style="margin-top: 30px; width: 80%; margin-left: auto; margin-right: auto;">
        <table style="width: 100%; border-collapse: collapse; direction: rtl; border: 2px solid #0f172a;">
            <tr style="background: #f8fafc; border-bottom: 2px solid #0f172a;">
                <td colspan="3" style="padding: 8px 12px; font-size: 10px; font-weight: bold; color: #64748b; text-align: center; border: none; letter-spacing: 1px;">&#x2014; {{ $labels['summary'] }} &#x2014;</td>
            </tr>
            <tr>
                <td style="padding: 14px 8px; text-align: center; border-left: 2px solid #0f172a; background: #f8fafc;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 6px;">{{ $labels['pending'] }}</div>
                    <div style="font-size: 18px; font-weight: bold; color: #0f172a;">{{ number_format($data['status_totals']['pending'] ?? 0, 2) }}</div>
                </td>
                <td style="padding: 14px 8px; text-align: center; border-left: 2px solid #0f172a; background: #f8fafc;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 6px;">{{ $labels['approved'] }}</div>
                    <div style="font-size: 18px; font-weight: bold; color: #0f172a;">{{ number_format($data['status_totals']['approved'] ?? 0, 2) }}</div>
                </td>
                <td style="padding: 14px 8px; text-align: center; background: #f8fafc;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 6px;">{{ $labels['total'] }}</div>
                    <div style="font-size: 20px; font-weight: bold; color: #0f172a;">{{ number_format($data['total'] ?? 0, 2) }}</div>
                </td>
            </tr>
        </table>
    </div>
    @endif

    @if(isset($data['payment_method_totals']) && $data['payment_method_totals'])
    <div style="margin-top: 30px; width: 80%; margin-left: auto; margin-right: auto;">
        <table style="width: 100%; border-collapse: collapse; direction: rtl; border: 2px solid #0f172a;">
            <tr style="background: #f8fafc; border-bottom: 2px solid #0f172a;">
                <td colspan="4" style="padding: 8px 12px; font-size: 10px; font-weight: bold; color: #64748b; text-align: center; border: none; letter-spacing: 1px;">&#x2014; {{ $labels['grandTotal'] }} &#x2014;</td>
            </tr>
            <tr>
                <td style="padding: 14px 8px; text-align: center; border-left: 2px solid #0f172a; background: #f8fafc;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 6px;">{{ $labels['check'] }}</div>
                    <div style="font-size: 18px; font-weight: bold; color: #0f172a;">{{ number_format($data['payment_method_totals']['check'] ?? 0, 2) }}</div>
                </td>
                <td style="padding: 14px 8px; text-align: center; border-left: 2px solid #0f172a; background: #f8fafc;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 6px;">{{ $labels['transfer'] }}</div>
                    <div style="font-size: 18px; font-weight: bold; color: #0f172a;">{{ number_format($data['payment_method_totals']['transfer'] ?? 0, 2) }}</div>
                </td>
                <td style="padding: 14px 8px; text-align: center; border-left: 2px solid #0f172a; background: #f8fafc;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 6px;">{{ $labels['cash'] }}</div>
                    <div style="font-size: 18px; font-weight: bold; color: #0f172a;">{{ number_format($data['payment_method_totals']['cash'] ?? 0, 2) }}</div>
                </td>
                <td style="padding: 14px 8px; text-align: center; background: #f8fafc;">
                    <div style="font-size: 9px; color: #64748b; font-weight: bold; margin-bottom: 6px;">{{ $labels['total'] }}</div>
                    <div style="font-size: 20px; font-weight: bold; color: #0f172a;">{{ number_format($data['total'] ?? 0, 2) }}</div>
                </td>
            </tr>
        </table>
    </div>
    @endif

    <div style="margin-top: 30px; font-size: 9px; color: #94a3b8;">{{ now()->format('Y-m-d H:i') }}</div>

</div>

@php
    $n = fn($v) => str_replace(
        ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'],
        ['0','1','2','3','4','5','6','7','8','9'],
        number_format($v, 2)
    );
    $statusLabel = fn($s) => match($s) {
        'pending'    => 'معلق',
        'approved','documented' => 'موثق',
        'cancelled'  => 'ملغي',
        'rejected'   => 'مرفوض',
        default      => $s
    };
    $statusClass = fn($s) => match($s) {
        'pending'              => 's-pending',
        'approved','documented'=> 's-approved',
        'cancelled'            => 's-cancelled',
        'rejected'             => 's-rejected',
        default                => ''
    };
@endphp

{{-- ===== ملخص ===== --}}
@if($type === 'summary')

@if(isset($data['is_marketer_summary']) && $data['is_marketer_summary'])
{{-- ملخص المسوقين --}}
<table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:16px;">
    <tr>
        <td style="width:100%; vertical-align:top; padding:0;">
            <div class="card-bg">
                <div class="card-title">{{ $labels['grandTotal'] }}</div>
                <table style="width:100%; border-collapse:collapse; table-layout:fixed; direction:ltr;">
                    <tr><td class="cv" style="width:45%">{{ $n($data['total_commissions']) }}</td><td class="cl" style="width:55%">{{ $labels['grandTotal'] }} {{ $g('الأرباح') }}</td></tr>
                    <tr><td class="cv">{{ $n($data['total_withdrawals']) }}</td><td class="cl">{{ $g('إجمالي المسحوب') }}</td></tr>
                    <tr><td class="cv" style="{{ $data['remaining'] >= 0 ? 'color:#16a34a;' : 'color:#dc2626;' }} font-weight:bold;">{{ $n($data['remaining']) }}</td><td class="cl">{{ $g('المتبقي') }}</td></tr>
                </table>
            </div>
        </td>
    </tr>
</table>

@if(count($data['marketers_data']) > 0)
<table>
    <thead>
        <tr>
            <th style="width:15%">{{ $g('المتبقي') }}</th>
            <th style="width:15%">{{ $g('المسحوب') }}</th>
            <th style="width:15%">{{ $g('الأرباح') }}</th>
            <th style="width:50%">{{ $labels['marketer'] }}</th>
            <th style="width:5%; text-align:center">#</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['marketers_data'] as $i => $row)
        <tr class="{{ $i % 2 !== 0 ? 'row-even' : '' }}">
            <td class="num" style="{{ $row['balance'] >= 0 ? 'color:#16a34a;' : 'color:#dc2626;' }}">{{ $n($row['balance']) }}</td>
            <td class="num">{{ $n($row['withdrawals']) }}</td>
            <td class="num">{{ $n($row['commissions']) }}</td>
            <td style="font-weight:bold; color:#0f172a;">{{ $g($row['marketer_name']) }}</td>
            <td class="idx-td">{{ $i + 1 }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td class="num" style="{{ $data['remaining'] >= 0 ? 'color:#16a34a;' : 'color:#dc2626;' }}">{{ $n($data['remaining']) }}</td>
            <td class="num">{{ $n($data['total_withdrawals']) }}</td>
            <td class="num">{{ $n($data['total_commissions']) }}</td>
            <td colspan="2" style="text-align:right; color:#64748b;">{{ $labels['total'] }}</td>
        </tr>
    </tfoot>
</table>
@endif

@else
{{-- ملخص المتاجر --}}
<table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:16px;">
    <tr>
        <td style="width:32%; vertical-align:top; padding:0;">
            <div class="card-bg">
                <div class="card-title">{{ $labels['grandTotal'] }}</div>
                <table style="width:100%; border-collapse:collapse; table-layout:fixed; direction:ltr;">
                    <tr><td class="cv" style="width:45%">{{ $n($data['total_sales']) }}</td><td class="cl" style="width:55%">{{ $labels['sales'] }}</td></tr>
                    <tr><td class="cv">{{ $n($data['total_payments']) }}</td><td class="cl">{{ $labels['payments'] }}</td></tr>
                    <tr><td class="cv">{{ $n($data['total_returns']) }}</td><td class="cl">{{ $labels['returns'] }}</td></tr>
                    <tr><td class="cv" style="{{ ($data['current_balance'] ?? $data['total_debt'] ?? 0) > 0 ? 'color:#dc2626;' : 'color:#16a34a;' }} font-weight:bold;">{{ $n($data['current_balance'] ?? $data['total_debt'] ?? 0) }}</td><td class="cl">{{ $labels['debt'] }}</td></tr>
                </table>
            </div>
        </td>
        <td style="width:2%"></td>
        <td style="width:66%; vertical-align:top; padding:0;">
            <div class="card-bg" style="border-top-color:#1d4ed8;">
                <div class="card-title" style="color:#1d4ed8;">{{ $labels['filterInfo'] }}</div>
                <table style="width:100%; border-collapse:collapse; table-layout:fixed; direction:rtl; margin-top:4px;">
                    <tr>
                        <td style="font-size:9px; font-weight:bold; color:#0f172a; width:70%">{{ $labels['entityName'] }}</td>
                        <td style="font-size:8px; color:#64748b; width:30%">{{ $labels['entityLabel'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:9px; font-weight:bold; color:#0f172a;">{{ $labels['operationName'] }}</td>
                        <td style="font-size:8px; color:#64748b;">{{ $labels['operation'] }}</td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

@if(isset($data['stores_data']) && count($data['stores_data']) > 0)
<table>
    <thead>
        <tr>
            <th style="width:15%">{{ $labels['debt'] }}</th>
            <th style="width:15%">{{ $labels['returns'] }}</th>
            <th style="width:15%">{{ $labels['payments'] }}</th>
            <th style="width:15%">{{ $labels['sales'] }}</th>
            <th style="width:35%">{{ $labels['store'] }}</th>
            <th style="width:5%; text-align:center">#</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data['stores_data'] as $i => $row)
        <tr class="{{ $i % 2 !== 0 ? 'row-even' : '' }}">
            <td class="num" style="{{ $row['balance'] > 0 ? 'color:#dc2626;' : ($row['balance'] < 0 ? 'color:#16a34a;' : 'color:#94a3b8;') }}">{{ $n($row['balance']) }}</td>
            <td class="num">{{ $n($row['returns']) }}</td>
            <td class="num">{{ $n($row['payments']) }}</td>
            <td class="num">{{ $n($row['sales']) }}</td>
            <td style="font-weight:bold; color:#0f172a;">{{ $g($row['store_name']) }}</td>
            <td class="idx-td">{{ $i + 1 }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td class="num" style="{{ ($data['current_balance'] ?? 0) > 0 ? 'color:#dc2626;' : 'color:#16a34a;' }}">{{ $n($data['current_balance'] ?? 0) }}</td>
            <td class="num">{{ $n($data['total_returns']) }}</td>
            <td class="num">{{ $n($data['total_payments']) }}</td>
            <td class="num">{{ $n($data['total_sales']) }}</td>
            <td colspan="2" style="text-align:right; color:#64748b;">{{ $labels['total'] }}</td>
        </tr>
    </tfoot>
</table>
@endif
@endif

{{-- ===== عمليات تفصيلية ===== --}}
@else

<table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:16px;">
    <tr>
        <td style="width:32%; vertical-align:top; padding:0;">
            <div class="card-bg">
                <div class="card-title">{{ $labels['summary'] }}</div>
                <table style="width:100%; border-collapse:collapse; table-layout:fixed; direction:ltr;">
                    @if(isset($data['status_totals']))
                    <tr><td class="cv" style="width:45%; color:#d97706;">{{ $n($data['status_totals']['pending'] ?? 0) }}</td><td class="cl" style="width:55%">{{ $labels['pending'] }}</td></tr>
                    <tr><td class="cv" style="color:#16a34a;">{{ $n($data['status_totals']['approved'] ?? 0) }}</td><td class="cl">{{ $labels['approved'] }}</td></tr>
                    <tr><td class="cv" style="font-weight:bold;">{{ $n($data['total'] ?? 0) }}</td><td class="cl">{{ $labels['total'] }}</td></tr>
                    @endif
                    @if(isset($data['payment_method_totals']))
                    <tr><td class="cv">{{ $n($data['payment_method_totals']['cash'] ?? 0) }}</td><td class="cl">{{ $labels['cash'] }}</td></tr>
                    <tr><td class="cv">{{ $n($data['payment_method_totals']['transfer'] ?? 0) }}</td><td class="cl">{{ $labels['transfer'] }}</td></tr>
                    <tr><td class="cv">{{ $n($data['payment_method_totals']['check'] ?? 0) }}</td><td class="cl">{{ $labels['check'] }}</td></tr>
                    @endif
                </table>
            </div>
        </td>
        <td style="width:2%"></td>
        <td style="width:66%; vertical-align:top; padding:0;">
            <div class="card-bg" style="border-top-color:#7c3aed;">
                <div class="card-title" style="color:#7c3aed;">{{ $labels['filterInfo'] }}</div>
                <table style="width:100%; border-collapse:collapse; table-layout:fixed; direction:rtl; margin-top:4px;">
                    <tr>
                        <td style="font-size:9px; font-weight:bold; color:#0f172a; width:70%">{{ $labels['entityName'] }}</td>
                        <td style="font-size:8px; color:#64748b; width:30%">{{ $labels['entityLabel'] }}</td>
                    </tr>
                    <tr>
                        <td style="font-size:9px; font-weight:bold; color:#0f172a;">{{ $labels['operationName'] }}</td>
                        <td style="font-size:8px; color:#64748b;">{{ $labels['operation'] }}</td>
                    </tr>
                    @if(!empty($labels['storeName']))
                    <tr>
                        <td style="font-size:9px; font-weight:bold; color:#0f172a;">{{ $labels['storeName'] }}</td>
                        <td style="font-size:8px; color:#64748b;">{{ $labels['store'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($labels['statusName']))
                    <tr>
                        <td style="font-size:9px; font-weight:bold; color:#0f172a;">{{ $labels['statusName'] }}</td>
                        <td style="font-size:8px; color:#64748b;">{{ $labels['status'] }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            @if($labels['hasAmount'])
            <th style="width:12%">{{ $labels['amount'] }}</th>
            @endif
            @if($labels['hasStatus'])
            <th style="width:8%">{{ $labels['status'] }}</th>
            @endif
            <th style="width:9%">{{ $labels['date'] }}</th>
            @if($labels['hasPaymentMethod'])
            @if($labels['hasCommission'])
            <th style="width:10%">{{ $labels['commAmount'] }}</th>
            <th style="width:8%">{{ $labels['commRate'] }}</th>
            @endif
            <th style="width:10%">{{ $labels['payMethod'] }}</th>
            @endif
            <th style="width:20%">{{ $labels['invoiceNum'] }}</th>
            @if($labels['hasStore'] ?? false)
            <th style="width:20%">{{ $labels['store'] }}</th>
            @endif
            @if($labels['hasMarketer'] ?? false)
            <th style="width:20%">{{ $labels['marketer'] }}</th>
            @endif
            <th style="width:4%; text-align:center">#</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $i => $item)
        @php
            $sl = $statusLabel($item->status);
            $sc = $statusClass($item->status);
            $num = match($operation) {
                'sales'         => $item->invoice_number,
                'payments'      => $item->payment_number,
                'sales_returns' => $item->return_number,
                'returns'       => $item->return_number ?? $item->invoice_number,
                'requests'      => $item->invoice_number,
                'withdrawals'   => 'WD-'.$item->id,
                default         => '-'
            };
            $amount = match($operation) {
                'sales','sales_returns' => $item->total_amount,
                'payments'              => $item->amount,
                'withdrawals'           => $item->requested_amount,
                default                 => null
            };
        @endphp
        <tr class="{{ $i % 2 !== 0 ? 'row-even' : '' }}">
            @if($labels['hasAmount'])
            <td class="num">{{ $amount !== null ? $n($amount) : '-' }}</td>
            @endif
            @if($labels['hasStatus'])
            <td class="{{ $sc }}" style="font-size:7.5px;">{{ $g($sl) }}</td>
            @endif
            <td style="font-size:7.5px;">{{ $item->created_at->format('Y-m-d') }}</td>
            @if($labels['hasPaymentMethod'])
            @if($labels['hasCommission'])
            <td class="num" style="font-size:7.5px;">{{ $n($item->commission->commission_amount ?? 0) }}</td>
            <td class="num" style="font-size:7.5px;">{{ ($item->commission->commission_rate ?? '-') }}%</td>
            @endif
            <td style="font-size:7.5px;">{{ $g(match($item->payment_method ?? '') { 'cash' => 'نقدي', 'transfer' => 'تحويل', 'check' => 'شيك', 'certified_check' => 'شيك مصدق', default => '-' }) }}</td>
            @endif
            <td style="font-size:7.5px; font-weight:bold; color:#0f172a;">{{ $en($num) }}</td>
            @if($labels['hasStore'] ?? false)
            <td style="font-size:7.5px;">{{ $en($g($item->store->name ?? '-')) }}</td>
            @endif
            @if($labels['hasMarketer'] ?? false)
            <td style="font-size:7.5px;">{{ $en($g($item->marketer->full_name ?? '-')) }}</td>
            @endif
            <td class="idx-td">{{ $i + 1 }}</td>
        </tr>
        @endforeach
    </tbody>
    @if($labels['hasAmount'] && count($rows) > 0)
    <tfoot>
        <tr>
            <td class="num">{{ $n($data['total'] ?? 0) }}</td>
            <td colspan="20" style="text-align:right; color:#64748b;">{{ $labels['total'] }}</td>
        </tr>
    </tfoot>
    @endif
</table>

@endif

<div class="footer">
    <div class="footer-r">{{ $labels['title'] }}</div>
    <div class="footer-l">{{ now()->format('Y-m-d  H:i') }}</div>
</div>

</body>
</html>
