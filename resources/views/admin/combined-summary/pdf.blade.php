<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $labels['title'] }}</title>
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
        @page { size: A4; margin: 15px; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', 'DejaVu Sans', sans-serif; }
        body { font-size: 10px; color: #1f2937; direction: rtl; background: white; }
        .container { padding: 0 5px; }

        /* ===== HEADER ===== */
        .header { display: table; width: 100%; padding-bottom: 10px; margin-bottom: 14px; border-bottom: 3px solid #1d4ed8; }
        .header-right { display: table-cell; text-align: right; vertical-align: middle; }
        .header-left  { display: table-cell; text-align: left;  vertical-align: middle; width: 90px; padding-left: 4px; }
        .title    { font-size: 18px; font-weight: bold; color: #1d4ed8; letter-spacing: 0.3px; }
        .subtitle { font-size: 10px; color: #6b7280; margin-top: 5px; }
        .date-range { display: inline-block; direction: rtl; }
        .date-label { color: #9ca3af; font-size: 9px; }
        .date-val   { color: #1d4ed8; font-weight: bold; font-size: 10px; }
        .date-arrow { color: #9ca3af; margin: 0 4px; font-size: 10px; }

        /* ===== SUMMARY CARDS ===== */
        .summary-wrap { display: table; width: 100%; margin-bottom: 12px; }
        .summary-card { display: table-cell; width: 32%; vertical-align: top; border-radius: 6px; overflow: hidden; }
        .summary-gap  { display: table-cell; width: 2%; }

        .card-header { padding: 5px 8px; font-size: 10px; font-weight: bold; color: #fff; }
        .card-header-blue   { background: #1d4ed8; }
        .card-header-green  { background: #059669; }
        .card-header-purple { background: #7c3aed; }

        .card-body { border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 6px 6px; padding: 6px 8px; }
        .s-row { display: table; width: 100%; margin-bottom: 3px; border-bottom: 1px solid #f3f4f6; padding-bottom: 2px; }
        .s-row:last-child { border-bottom: none; margin-bottom: 0; }
        .s-label { display: table-cell; font-size: 9px; color: #6b7280; text-align: right; width: 55%; }
        .s-val   { display: table-cell; font-size: 10px; font-weight: bold; text-align: left; width: 45%; letter-spacing: 0.3px; }

        /* ===== TABLE ===== */
        table { width: 100%; border-collapse: collapse; direction: rtl; table-layout: fixed; }
        thead tr { background: #1d4ed8; }
        th { color: #fff; text-align: right; padding: 6px 5px; font-size: 9px; font-weight: bold; border: 1px solid #1e40af; }
        td { border: 1px solid #e5e7eb; padding: 5px 6px; font-size: 9px; color: #111; text-align: right; }
        td.num { font-size: 9.5px; letter-spacing: 0.4px; font-weight: bold; color: #374151; }
        tr:nth-child(even) td { background: #f8fafc; }
        tfoot tr td { background: #eff6ff; font-weight: bold; border-top: 2px solid #1d4ed8; }

        .check-store    { color: #1d4ed8; font-weight: bold; font-size: 11px; }
        .check-customer { color: #7c3aed; font-weight: bold; font-size: 11px; }
        .debt-pos { color: #dc2626; font-weight: bold; }
        .debt-neg { color: #059669; font-weight: bold; }
        .badge-debtor   { background: #fee2e2; color: #dc2626; padding: 1px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; }
        .badge-creditor { background: #d1fae5; color: #059669; padding: 1px 5px; border-radius: 3px; font-size: 8px; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">

    {{-- Header --}}
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('logo.png') }}" style="max-height:75px; max-width:140px; display:block;">
        </div>
        <div class="header-right">
            <div class="title">{{ $labels['title'] }}</div>
            <div class="subtitle">
                <span class="date-range">
                    <span class="date-val">{{ $labels['dateTo'] }}</span>
                    <span class="date-arrow">&#8592;</span>
                    <span class="date-val">{{ $labels['dateFrom'] }}</span>
                </span>
            </div>
        </div>
    </div>

    @php
        $storeRows    = $rows->where('type', 'متجر');
        $customerRows = $rows->where('type', 'عميل');
        $n = fn($v) => str_replace(['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'], ['0','1','2','3','4','5','6','7','8','9'], number_format($v, 2));
    @endphp

    {{-- Summary Cards --}}
    <div class="summary-wrap">
        <div class="summary-card">
            <div class="card-header card-header-blue">{{ $labels['grandTotal'] }}</div>
            <div class="card-body">
                <div class="s-row"><span class="s-val">{{ $n($grandInvoices) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['invoices'] }}</span></div>
                <div class="s-row"><span class="s-val">{{ $n($grandPayments) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['payments'] }}</span></div>
                <div class="s-row"><span class="s-val">{{ $n($grandReturns) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['returns'] }}</span></div>
                <div class="s-row"><span class="s-val {{ $grandDebt > 0 ? 'debt-pos' : 'debt-neg' }}">{{ $n($grandDebt) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['debt'] }}</span></div>
            </div>
        </div>
        <div class="summary-gap"></div>
        <div class="summary-card">
            <div class="card-header card-header-green">{{ $labels['stores'] }}</div>
            <div class="card-body">
                <div class="s-row"><span class="s-val">{{ $n($storeRows->sum('total_invoices')) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['invoices'] }}</span></div>
                <div class="s-row"><span class="s-val">{{ $n($storeRows->sum('total_payments')) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['payments'] }}</span></div>
                <div class="s-row"><span class="s-val">{{ $n($storeRows->sum('total_returns')) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['returns'] }}</span></div>
                <div class="s-row"><span class="s-val {{ $storeRows->sum('total_debt') > 0 ? 'debt-pos' : 'debt-neg' }}">{{ $n($storeRows->sum('total_debt')) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['debt'] }}</span></div>
            </div>
        </div>
        <div class="summary-gap"></div>
        <div class="summary-card">
            <div class="card-header card-header-purple">{{ $labels['customers'] }}</div>
            <div class="card-body">
                <div class="s-row"><span class="s-val">{{ $n($customerRows->sum('total_invoices')) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['invoices'] }}</span></div>
                <div class="s-row"><span class="s-val">{{ $n($customerRows->sum('total_payments')) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['payments'] }}</span></div>
                <div class="s-row"><span class="s-val">{{ $n($customerRows->sum('total_returns')) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['returns'] }}</span></div>
                <div class="s-row"><span class="s-val {{ $customerRows->sum('total_debt') > 0 ? 'debt-pos' : 'debt-neg' }}">{{ $n($customerRows->sum('total_debt')) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['debt'] }}</span></div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th style="width:9%">{{ $labels['debtStatus'] }}</th>
                <th style="width:13%">{{ $labels['debt'] }}</th>
                <th style="width:13%">{{ $labels['returns'] }}</th>
                <th style="width:13%">{{ $labels['payments'] }}</th>
                <th style="width:13%">{{ $labels['invoices'] }}</th>
                <th style="width:7%">{{ $labels['store'] }}</th>
                <th style="width:7%">{{ $labels['customer'] }}</th>
                <th style="width:21%">{{ $labels['name'] }}</th>
                <th style="width:4%">#</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processedRows as $i => $row)
            <tr>
                <td style="text-align:center">
                    @if($row->total_debt > 0) <span class="badge-debtor">{{ $labels['debtor'] }}</span>
                    @elseif($row->total_debt < 0) <span class="badge-creditor">{{ $labels['creditor'] }}</span>
                    @else -- @endif
                </td>
                <td class="num {{ $row->total_debt > 0 ? 'debt-pos' : ($row->total_debt < 0 ? 'debt-neg' : '') }}">{{ $row->total_debt != 0 ? $n($row->total_debt) : '--' }}</td>
                <td class="num">{{ $row->total_returns != 0 ? $n($row->total_returns) : '--' }}</td>
                <td class="num">{{ $row->total_payments != 0 ? $n($row->total_payments) : '--' }}</td>
                <td class="num">{{ $row->total_invoices != 0 ? $n($row->total_invoices) : '--' }}</td>
                <td style="text-align:center">@if($row->is_store)<span class="check-store">&#10003;</span>@endif</td>
                <td style="text-align:center">@if(!$row->is_store)<span class="check-customer">&#10003;</span>@endif</td>
                <td>{{ $row->name }}</td>
                <td style="text-align:center">{{ $i + 1 }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td style="text-align:center">
                    @if($grandDebt > 0) <span class="badge-debtor">{{ $labels['debtor'] }}</span>
                    @elseif($grandDebt < 0) <span class="badge-creditor">{{ $labels['creditor'] }}</span>
                    @else -- @endif
                </td>
                <td class="num {{ $grandDebt > 0 ? 'debt-pos' : 'debt-neg' }}">{{ $n($grandDebt) }}</td>
                <td class="num">{{ $n($grandReturns) }}</td>
                <td class="num">{{ $n($grandPayments) }}</td>
                <td class="num">{{ $n($grandInvoices) }}</td>
                <td colspan="4" style="text-align:right">{{ $labels['total'] }}</td>
            </tr>
        </tfoot>
    </table>

</div>
</body>
</html>
