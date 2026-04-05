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
        @page { size: A4; margin: 20px 32px; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', 'DejaVu Sans', sans-serif; }
        body { font-size: 10px; color: #1e293b; direction: rtl; background: #fff; }

        /* REPEATING HEADER */
        #page-header { position: fixed; top: -20px; left: 0; right: 0; background: #fff; border-bottom: 2px solid #0f172a; padding: 8px 32px; }
        #page-header .ph-tbl { display: table; width: 100%; }
        #page-header .ph-r { display: table-cell; vertical-align: middle; text-align: right; }
        #page-header .ph-c { display: table-cell; vertical-align: middle; text-align: center; width: 80px; }
        #page-header .ph-l { display: table-cell; vertical-align: middle; text-align: left; width: 110px; }
        #page-header .ph-title { font-size: 15px; font-weight: bold; color: #0f172a; }
        #page-header .ph-date { margin-top: 2px; font-size: 8.5px; color: #64748b; }
        #page-header .ph-date span { color: #0f172a; font-weight: bold; }
        #page-header .ph-pagenum { font-size: 12px;border-bottom: 2px solid #0f172a; color: #0f172a; font-weight: bold;  padding: 2px 10px; display: inline-block; }
        .page-num:before { content: counter(page); }
        @page { counter-increment: page; }
        body { margin-top: 80px; }

        /* HEADER */
        .header     { display: table; width: 100%; padding-bottom: 8px; margin-bottom: 12px; border-bottom: 2px solid #0f172a; }
        .h-right    { display: table-cell; vertical-align: middle; text-align: right; }
        .h-left     { display: table-cell; vertical-align: middle; text-align: left; width: 110px; }
        .h-title    { font-size: 15px; font-weight: bold; color: #0f172a; }
        .h-date     { margin-top: 2px; font-size: 8.5px; color: #64748b; }
        .h-date span { color: #0f172a; font-weight: bold; }

        /* CARDS */
        .card-bg  { background: #f8fafc; border: 1px solid #e2e8f0; border-top: 2px solid #0f172a; padding: 10px 12px; }
        .card-title { font-size: 8px; font-weight: bold; color: #64748b; }
        .cl { text-align: right; color: #475569; font-size: 8.5px; font-weight: bold; padding: 2px 4px 2px 0; }
        .cv { text-align: left;  color: #0f172a; font-weight: bold; font-size: 9px; padding: 2px 0 2px 4px; }

        /* TABLE */
        table  { width: 100%; border-collapse: collapse; direction: rtl; table-layout: fixed; }
        thead tr { background: #dce4ee; }
        th     { color: #0f172a; text-align: right; padding: 6px 6px; font-size: 7.5px; font-weight: bold; border: 1px solid #64748b; }
        td     { padding: 5px 6px; font-size: 8px; color: #334155; text-align: right; border: 1px solid #94a3b8; background: #fff; }
        tr.row-even td { background: #f8fafc; }
        td.num      { font-weight: bold; color: #0f172a; font-size: 8px; }
        td.name-td  { font-weight: bold; color: #0f172a; font-size: 8px; }
        td.idx-td   { color: #64748b; font-size: 7.5px; text-align: center; }
        tfoot tr td { background: #f1f5f9; font-weight: bold; font-size: 8px; padding: 6px 6px; border: 1px solid #94a3b8; border-top: 2px solid #0f172a; }

        /* MISC */
        .debt-pos { color: #dc2626; font-weight: bold; }
        .debt-neg { color: #16a34a; font-weight: bold; }
        .badge-debtor   { color: #dc2626; font-size: 14px; font-weight: bold; }
        .badge-creditor { color: #16a34a; font-size: 14px; font-weight: bold; }
        .check-store    { color: #1d4ed8; font-size: 14px; font-weight: bold; }
        .check-customer { color: #7c3aed; font-size: 14px; font-weight: bold; }

        /* FOOTER */
        .footer   { display: table; width: 100%; margin-top: 14px; padding-top: 8px; border-top: 1px solid #e2e8f0; }
        .footer-r { display: table-cell; text-align: right; font-size: 7.5px; color: #94a3b8; }
        .footer-l { display: table-cell; text-align: left;  font-size: 7.5px; color: #94a3b8; }
    </style>
</head>
<body>

<div id="page-header">
    <div class="ph-tbl">
        <div class="ph-l">
            <img src="{{ public_path('logo.png') }}" style="max-height:65px; max-width:120px; display:block;">
        </div>
        <div class="ph-c">
            <span class="ph-pagenum"><span class="page-num"></span> / {{ $labels['totalPages'] ?? '' }}</span>
        </div>
        <div class="ph-r">
            <div class="ph-title">{{ $labels['title'] }}</div>
            <div style="margin-top:3px; font-size:8px; color:#64748b; text-align:right;">
                {{ $labels['dateTo'] }} - {{ $labels['dateFrom'] }}
                @if(!empty($labels['filterEntity']))
                &nbsp;|&nbsp; {{ $labels['filterEntity'] }}
                @endif
            </div>
        </div>
    </div>
</div>

    @php
        $storeRows    = $rows->where('type', 'متجر');
        $customerRows = $rows->where('type', 'عميل');
        $n = fn($v) => str_replace(['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'], ['0','1','2','3','4','5','6','7','8','9'], number_format($v, 2));
        $hasFilter   = !empty($labels['filterStaff']) || !empty($labels['filterStore']) || !empty($labels['filterCustomer']);
        $showOldDebt = $labels['showOldDebt'] ?? true;
    @endphp

    {{-- Cards --}}
    <table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-bottom:18px;">
        <tr>
            {{-- كارت الإجماليات الكلية --}}
            <td style="width:32%; vertical-align:top; padding:0;">
                <div class="card-bg">
                    <div class="card-title">{{ $labels['grandTotal'] }}</div>
                    <table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-top:6px; direction:ltr;">
                        <tr><td class="cv" style="width:40%">{{ $n($grandInvoices) }}</td><td class="cl" style="width:60%">{{ $labels['invoices'] }}</td></tr>
                        <tr><td class="cv">{{ $n($grandPayments) }}</td><td class="cl">{{ $labels['payments'] }}</td></tr>
                        <tr><td class="cv">{{ $n($grandReturns) }}</td><td class="cl">{{ $labels['returns'] }}</td></tr>
                        @if($showOldDebt)
                        <tr><td class="cv" style="color:#d97706; font-weight:bold">{{ $n($rows->sum('old_debt')) }}</td><td class="cl">{{ $labels['old_debt'] }}</td></tr>
                        @endif
                        <tr><td class="cv {{ $grandDebt > 0 ? 'debt-pos' : 'debt-neg' }}">{{ $n($grandDebt) }}</td><td class="cl">{{ $labels['debt'] }}</td></tr>
                    </table>
                </div>
            </td>
            <td style="width:2%"></td>

            @if($hasFilter)
            {{-- كارت الفلاتر المطبقة --}}
            <td style="width:66%; vertical-align:top; padding:0;" colspan="3">
                <div class="card-bg" style="border-top-color:#7c3aed;">
                    <div class="card-title" style="color:#7c3aed;">{{ $labels['filterLabel'] ?? 'الفلاتر المطبقة' }}</div>
                    <table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-top:6px; direction:rtl;">
                        @if(!empty($labels['filterCustomer']))
                        <tr>
                            <td class="cv" style="width:65%; text-align:right; font-size:10px;">{{ $labels['filterCustomer'] }}</td>
                            <td class="cl" style="width:35%; text-align:right;">{{ $labels['filterCustomerLabel'] ?? 'العميل' }}</td>
                        </tr>
                        @endif
                        @if(!empty($labels['filterStore']))
                        <tr>
                            <td class="cv" style="text-align:right; font-size:10px;">{{ $labels['filterStore'] }}</td>
                            <td class="cl" style="text-align:right;">{{ $labels['filterStoreLabel'] ?? 'المتجر' }}</td>
                        </tr>
                        @endif
                        @if(!empty($labels['filterStaff']))
                        <tr>
                            <td class="cv" style="text-align:right; font-size:10px;">{{ $labels['filterStaff'] }}</td>
                            <td class="cl" style="text-align:right;">{{ $labels['filterStaffLabel'] ?? 'الموظف' }}</td>
                        </tr>
                        @endif
                        @if(!empty($labels['filterOldDebt']))
                        <tr>
                            <td class="cv" style="text-align:right; font-size:10px; {{ str_contains($labels['filterOldDebt'] ?? '', 'غير') ? 'color:#dc2626;' : 'color:#16a34a;' }}">{{ $labels['filterOldDebt'] }}</td>
                            <td class="cl" style="text-align:right;">{{ $labels['filterOldDebtLabel'] ?? 'الديون السابقة' }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </td>
            @else
            {{-- كارت المتاجر --}}
            <td style="width:32%; vertical-align:top; padding:0;">
                <div class="card-bg">
                    <div class="card-title">{{ $labels['stores'] }}</div>
                    <table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-top:6px; direction:ltr;">
                        <tr><td class="cv" style="width:40%">{{ $n($storeRows->sum('total_invoices')) }}</td><td class="cl" style="width:60%">{{ $labels['invoices'] }}</td></tr>
                        <tr><td class="cv">{{ $n($storeRows->sum('total_payments')) }}</td><td class="cl">{{ $labels['payments'] }}</td></tr>
                        <tr><td class="cv">{{ $n($storeRows->sum('total_returns')) }}</td><td class="cl">{{ $labels['returns'] }}</td></tr>
                        <tr><td class="cv" style="color:#d97706; font-weight:bold">{{ $n($storeRows->sum('old_debt')) }}</td><td class="cl">{{ $labels['old_debt'] }}</td></tr>
                        <tr><td class="cv {{ $storeRows->sum('total_debt') > 0 ? 'debt-pos' : 'debt-neg' }}">{{ $n($storeRows->sum('total_debt')) }}</td><td class="cl">{{ $labels['debt'] }}</td></tr>
                    </table>
                </div>
            </td>
            <td style="width:2%"></td>
            {{-- كارت العملاء --}}
            <td style="width:32%; vertical-align:top; padding:0;">
                <div class="card-bg">
                    <div class="card-title">{{ $labels['customers'] }}</div>
                    <table style="width:100%; border-collapse:collapse; table-layout:fixed; margin-top:6px; direction:ltr;">
                        <tr><td class="cv" style="width:40%">{{ $n($customerRows->sum('total_invoices')) }}</td><td class="cl" style="width:60%">{{ $labels['invoices'] }}</td></tr>
                        <tr><td class="cv">{{ $n($customerRows->sum('total_payments')) }}</td><td class="cl">{{ $labels['payments'] }}</td></tr>
                        <tr><td class="cv">{{ $n($customerRows->sum('total_returns')) }}</td><td class="cl">{{ $labels['returns'] }}</td></tr>
                        <tr><td class="cv" style="color:#d97706; font-weight:bold">{{ $n($customerRows->sum('old_debt')) }}</td><td class="cl">{{ $labels['old_debt'] }}</td></tr>
                        <tr><td class="cv {{ $customerRows->sum('total_debt') > 0 ? 'debt-pos' : 'debt-neg' }}">{{ $n($customerRows->sum('total_debt')) }}</td><td class="cl">{{ $labels['debt'] }}</td></tr>
                    </table>
                </div>
            </td>
            @endif
        </tr>
    </table>

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th style="width:6%; text-align:center; font-size:7px">{{ $labels['debtor'] }}</th>
                <th style="width:6%; text-align:center; font-size:7px">{{ $labels['creditor'] }}</th>
                <th style="width:{{ $hasFilter ? '13' : '11' }}%">{{ $labels['debt'] }}</th>
                <th style="width:{{ $hasFilter ? '12' : '10' }}%">{{ $labels['returns'] }}</th>
                <th style="width:{{ $hasFilter ? '12' : '10' }}%">{{ $labels['payments'] }}</th>
                <th style="width:{{ $hasFilter ? '12' : '10' }}%">{{ $labels['invoices'] }}</th>
                @if($showOldDebt)
                <th style="width:{{ $hasFilter ? '12' : '10' }}%; color:#d97706">{{ $labels['old_debt'] }}</th>
                @endif
                @if(!$hasFilter)
                <th style="width:6%; text-align:center">{{ $labels['store'] }}</th>
                <th style="width:6%; text-align:center">{{ $labels['customer'] }}</th>
                @endif
                <th style="width:{{ $hasFilter ? '25' : '21' }}%">{{ $labels['name'] }}</th>
                <th style="width:4%; text-align:center">#</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processedRows as $i => $row)
            <tr class="{{ $i % 2 !== 0 ? 'row-even' : '' }}">
                <td style="text-align:center">@if($row->total_debt > 0)<span class="badge-debtor">&#10003;</span>@else<span style="color:#e2e8f0">--</span>@endif</td>
                <td style="text-align:center">@if($row->total_debt < 0)<span class="badge-creditor">&#10003;</span>@else<span style="color:#e2e8f0">--</span>@endif</td>
                <td class="num {{ $row->total_debt > 0 ? 'debt-pos' : ($row->total_debt < 0 ? 'debt-neg' : '') }}">{{ $row->total_debt != 0 ? $n($row->total_debt) : '-' }}</td>
                <td class="num">{{ $row->total_returns != 0 ? $n($row->total_returns) : '-' }}</td>
                <td class="num">{{ $row->total_payments != 0 ? $n($row->total_payments) : '-' }}</td>
                <td class="num">{{ $row->total_invoices != 0 ? $n($row->total_invoices) : '-' }}</td>
                @if($showOldDebt)
                <td class="num" style="{{ $row->old_debt > 0 ? 'color:#d97706; font-weight:bold;' : 'color:#94a3b8;' }}">{{ $row->old_debt > 0 ? $n($row->old_debt) : '-' }}</td>
                @endif
                @if(!$hasFilter)
                <td style="text-align:center">@if($row->is_store)<span class="check-store">&#10003;</span>@endif</td>
                <td style="text-align:center">@if(!$row->is_store)<span class="check-customer">&#10003;</span>@endif</td>
                @endif
                <td class="name-td">{{ $row->name }}</td>
                <td class="idx-td">{{ $i + 1 }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td style="text-align:center">@if($grandDebt > 0)<span class="badge-debtor">&#10003;</span>@else<span style="color:#e2e8f0">--</span>@endif</td>
                <td style="text-align:center">@if($grandDebt < 0)<span class="badge-creditor">&#10003;</span>@else<span style="color:#e2e8f0">--</span>@endif</td>
                <td class="num {{ $grandDebt > 0 ? 'debt-pos' : 'debt-neg' }}">{{ $n($grandDebt) }}</td>
                <td class="num">{{ $n($grandReturns) }}</td>
                <td class="num">{{ $n($grandPayments) }}</td>
                <td class="num">{{ $n($grandInvoices) }}</td>
                @if($showOldDebt)
                <td class="num" style="color:#d97706; font-weight:bold">{{ $n($rows->sum('old_debt')) }}</td>
                @endif
                <td colspan="{{ $hasFilter ? 2 : 4 }}" style="text-align:right; color:#64748b;">{{ $labels['total'] }}</td>
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
