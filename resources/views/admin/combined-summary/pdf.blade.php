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
        @page { size: A4; margin: 20px 24px; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', 'DejaVu Sans', sans-serif; }
        body { font-size: 10px; color: #1e293b; direction: rtl; background: #fff; }

        /* HEADER */
        .header     { display: table; width: 100%; padding-bottom: 12px; margin-bottom: 16px; border-bottom: 2px solid #0f172a; }
        .h-right    { display: table-cell; vertical-align: middle; text-align: right; }
        .h-left     { display: table-cell; vertical-align: middle; text-align: left; width: 130px; }
        .h-title    { font-size: 17px; font-weight: bold; color: #0f172a; }
        .h-date     { margin-top: 3px; font-size: 9px; color: #64748b; }
        .h-date span { color: #0f172a; font-weight: bold; }

        /* CARDS */
        .card-bg  { background: #f8fafc; border-top: 2px solid #0f172a; padding: 10px 12px; }
        .card-title { font-size: 8px; font-weight: bold; color: #64748b; }
        .cl { text-align: right; color: #475569; font-size: 8.5px; font-weight: bold; padding: 2px 4px 2px 0; }
        .cv { text-align: left;  color: #0f172a; font-weight: bold; font-size: 9px; padding: 2px 0 2px 4px; }

        /* TABLE */
        table  { width: 100%; border-collapse: collapse; direction: rtl; table-layout: fixed; }
        thead tr { background: #0f172a; }
        th     { color: #e2e8f0; text-align: right; padding: 7px 8px; font-size: 8.5px; font-weight: bold; }
        td     { padding: 6px 8px; font-size: 9px; color: #334155; text-align: right; border-bottom: 1px solid #f1f5f9; background: #fff; }
        tr.row-even td { background: #f8fafc; }
        td.num      { font-weight: bold; color: #0f172a; }
        td.name-td  { font-weight: bold; color: #0f172a; }
        td.idx-td   { color: #64748b; font-size: 8px; text-align: center; }
        tfoot tr td { background: #f1f5f9; font-weight: bold; font-size: 9px; padding: 7px 8px; border-top: 2px solid #0f172a; border-bottom: none; }

        /* MISC */
        .debt-pos { color: #dc2626; font-weight: bold; }
        .debt-neg { color: #16a34a; font-weight: bold; }
        .badge-debtor   { color: #dc2626; font-size: 8px; font-weight: bold; }
        .badge-creditor { color: #16a34a; font-size: 8px; font-weight: bold; }
        .check-store    { color: #0f172a; font-size: 11px; }
        .check-customer { color: #64748b; font-size: 11px; }

        /* FOOTER */
        .footer   { display: table; width: 100%; margin-top: 14px; padding-top: 8px; border-top: 1px solid #e2e8f0; }
        .footer-r { display: table-cell; text-align: right; font-size: 7.5px; color: #94a3b8; }
        .footer-l { display: table-cell; text-align: left;  font-size: 7.5px; color: #94a3b8; }
    </style>
</head>
<body>

    @php
        $storeRows    = $rows->where('type', 'متجر');
        $customerRows = $rows->where('type', 'عميل');
        $n = fn($v) => str_replace(['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'], ['0','1','2','3','4','5','6','7','8','9'], number_format($v, 2));
    @endphp

    {{-- Header --}}
    <div class="header">
        <div class="h-left">
            <img src="{{ public_path('logo.png') }}" style="max-height:105px; max-width:140px; display:block;">
        </div>
        <div class="h-right">
            <div class="h-title">{{ $labels['title'] }}</div>
            <div class="h-date">
                <span>{{ $labels['dateFrom'] }}</span>
                &nbsp;&#8592;&nbsp;
                <span>{{ $labels['dateTo'] }}</span>
            </div>
        </div>
    </div>

    {{-- Cards --}}
    <table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:18px;">
        <tr>
            <td style="width:32%; vertical-align:top; padding:0;">
                <div class="card-bg">
                    <div class="card-title">{{ $labels['grandTotal'] }}</div>
                    <table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-top:6px; direction:ltr;">
                        <tr><td class="cv" style="width:40%">{{ $n($grandInvoices) }}</td><td class="cl" style="width:60%">{{ $labels['invoices'] }}</td></tr>
                        <tr><td class="cv">{{ $n($grandPayments) }}</td><td class="cl">{{ $labels['payments'] }}</td></tr>
                        <tr><td class="cv">{{ $n($grandReturns) }}</td><td class="cl">{{ $labels['returns'] }}</td></tr>
                        <tr><td class="cv {{ $grandDebt > 0 ? 'debt-pos' : 'debt-neg' }}">{{ $n($grandDebt) }}</td><td class="cl">{{ $labels['debt'] }}</td></tr>
                    </table>
                </div>
            </td>
            <td style="width:2%"></td>
            <td style="width:32%; vertical-align:top; padding:0;">
                <div class="card-bg">
                    <div class="card-title">{{ $labels['stores'] }}</div>
                    <table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-top:6px; direction:ltr;">
                        <tr><td class="cv" style="width:40%">{{ $n($storeRows->sum('total_invoices')) }}</td><td class="cl" style="width:60%">{{ $labels['invoices'] }}</td></tr>
                        <tr><td class="cv">{{ $n($storeRows->sum('total_payments')) }}</td><td class="cl">{{ $labels['payments'] }}</td></tr>
                        <tr><td class="cv">{{ $n($storeRows->sum('total_returns')) }}</td><td class="cl">{{ $labels['returns'] }}</td></tr>
                        <tr><td class="cv {{ $storeRows->sum('total_debt') > 0 ? 'debt-pos' : 'debt-neg' }}">{{ $n($storeRows->sum('total_debt')) }}</td><td class="cl">{{ $labels['debt'] }}</td></tr>
                    </table>
                </div>
            </td>
            <td style="width:2%"></td>
            <td style="width:32%; vertical-align:top; padding:0;">
                <div class="card-bg">
                    <div class="card-title">{{ $labels['customers'] }}</div>
                    <table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-top:6px; direction:ltr;">
                        <tr><td class="cv" style="width:40%">{{ $n($customerRows->sum('total_invoices')) }}</td><td class="cl" style="width:60%">{{ $labels['invoices'] }}</td></tr>
                        <tr><td class="cv">{{ $n($customerRows->sum('total_payments')) }}</td><td class="cl">{{ $labels['payments'] }}</td></tr>
                        <tr><td class="cv">{{ $n($customerRows->sum('total_returns')) }}</td><td class="cl">{{ $labels['returns'] }}</td></tr>
                        <tr><td class="cv {{ $customerRows->sum('total_debt') > 0 ? 'debt-pos' : 'debt-neg' }}">{{ $n($customerRows->sum('total_debt')) }}</td><td class="cl">{{ $labels['debt'] }}</td></tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th style="width:9%">{{ $labels['debtStatus'] }}</th>
                <th style="width:13%">{{ $labels['debt'] }}</th>
                <th style="width:13%">{{ $labels['returns'] }}</th>
                <th style="width:13%">{{ $labels['payments'] }}</th>
                <th style="width:13%">{{ $labels['invoices'] }}</th>
                <th style="width:7%; text-align:center">{{ $labels['store'] }}</th>
                <th style="width:7%; text-align:center">{{ $labels['customer'] }}</th>
                <th style="width:21%">{{ $labels['name'] }}</th>
                <th style="width:4%; text-align:center">#</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processedRows as $i => $row)
            <tr class="{{ $i % 2 !== 0 ? 'row-even' : '' }}">
                <td style="text-align:center">
                    @if($row->total_debt > 0) <span class="badge-debtor">{{ $labels['debtor'] }}</span>
                    @elseif($row->total_debt < 0) <span class="badge-creditor">{{ $labels['creditor'] }}</span>
                    @else <span style="color:#e2e8f0">--</span> @endif
                </td>
                <td class="num {{ $row->total_debt > 0 ? 'debt-pos' : ($row->total_debt < 0 ? 'debt-neg' : '') }}">{{ $row->total_debt != 0 ? $n($row->total_debt) : '--' }}</td>
                <td class="num">{{ $row->total_returns != 0 ? $n($row->total_returns) : '--' }}</td>
                <td class="num">{{ $row->total_payments != 0 ? $n($row->total_payments) : '--' }}</td>
                <td class="num">{{ $row->total_invoices != 0 ? $n($row->total_invoices) : '--' }}</td>
                <td style="text-align:center">@if($row->is_store)<span class="check-store">&#10003;</span>@endif</td>
                <td style="text-align:center">@if(!$row->is_store)<span class="check-customer">&#10003;</span>@endif</td>
                <td class="name-td">{{ $row->name }}</td>
                <td class="idx-td">{{ $i + 1 }}</td>
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
                <td colspan="4" style="text-align:right; color:#64748b;">{{ $labels['total'] }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-r">{{ $labels['title'] }}</div>
        <div class="footer-l">{{ now()->format('Y-m-d  H:i') }}</div>
    </div>

</body>
</html>
