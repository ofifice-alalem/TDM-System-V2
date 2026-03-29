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
        @page { size: A4; margin: 10px; }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Cairo', 'DejaVu Sans', sans-serif; }
        body { font-size: 10px; color: #000; direction: rtl; background: white; }
        .container { padding: 5px 15px; }

        .header { border-bottom: 3px solid #1d4ed8; padding-bottom: 8px; margin-bottom: 10px; display: table; width: 100%; }
        .header-right { display: table-cell; text-align: right; vertical-align: middle; }
        .header-left  { display: table-cell; text-align: left; vertical-align: middle; width: 120px; }
        .title    { font-size: 17px; font-weight: bold; color: #1d4ed8; }
        .subtitle { font-size: 10px; color: #6b7280; margin-top: 3px; }

        .summary-wrap { display: table; width: 100%; margin-bottom: 10px; }
        .summary-card { display: table-cell; width: 32%; border: 1px solid #d1d5db; border-radius: 6px; padding: 7px 10px; vertical-align: top; }
        .summary-gap  { display: table-cell; width: 2%; }
        .card-title { font-size: 11px; font-weight: bold; color: #1d4ed8; border-bottom: 1px solid #e5e7eb; padding-bottom: 3px; margin-bottom: 5px; }
        .s-row { display: table; width: 100%; margin-bottom: 2px; }
        .s-label { display: table-cell; font-size: 10px; color: #6b7280; text-align: right; width: 60%; }
        .s-val   { display: table-cell; font-size: 10px; font-weight: bold; text-align: left; width: 40%; }

        table { width: 100%; border-collapse: separate; border-spacing: 0; border: 1px solid #d1d5db; overflow: hidden; table-layout: fixed; direction: rtl; }
        th { background: #1d4ed8; color: #fff; text-align: right; padding: 5px 6px; font-size: 10px; font-weight: bold; border-bottom: 2px solid #1e40af; }
        td { border-bottom: 1px solid #f3f4f6; padding: 4px 6px; font-size: 10px; color: #111; text-align: right; }
        tr:nth-child(even) td { background-color: #f9fafb; }
        tfoot td { background: #e8eaf6; font-weight: bold; font-size: 10px; border-top: 2px solid #1d4ed8; }

        .type-store    { background: #dbeafe; color: #1d4ed8; padding: 1px 4px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .type-customer { background: #f3e8ff; color: #7c3aed; padding: 1px 4px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .debt-pos { color: #dc2626; font-weight: bold; }
        .debt-neg { color: #16a34a; font-weight: bold; }
        .badge-debtor   { background: #fee2e2; color: #dc2626; padding: 1px 4px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-creditor { background: #22c55e; color: #fff; padding: 1px 4px; border-radius: 3px; font-size: 9px; font-weight: bold; }
    </style>
</head>
<body>
<div class="container">

    <div class="header">
        <div class="header-right">
            <div class="title">{{ $labels['title'] }}</div>
            <div class="subtitle">{{ $labels['from'] }} {{ $fromDate }} {{ $labels['to'] }} {{ $toDate }}</div>
        </div>
        <div class="header-left">
            <img src="{{ public_path('logo.png') }}" style="max-height:55px; max-width:110px;">
        </div>
    </div>

    @php
        $storeRows    = $rows->where('type', 'متجر');
        $customerRows = $rows->where('type', 'عميل');
    @endphp

    <div class="summary-wrap">
        <div class="summary-card">
            <div class="card-title">{{ $labels['grandTotal'] }}</div>
            <div class="s-row"><span class="s-val">{{ number_format($grandInvoices, 2) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['invoices'] }}</span></div>
            <div class="s-row"><span class="s-val">{{ number_format($grandPayments, 2) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['payments'] }}</span></div>
            <div class="s-row"><span class="s-val">{{ number_format($grandReturns, 2) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['returns'] }}</span></div>
            <div class="s-row"><span class="s-val {{ $grandDebt > 0 ? 'debt-pos' : 'debt-neg' }}">{{ number_format($grandDebt, 2) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['debt'] }}</span></div>
        </div>
        <div class="summary-gap"></div>
        <div class="summary-card">
            <div class="card-title">{{ $labels['stores'] }}</div>
            <div class="s-row"><span class="s-val">{{ number_format($storeRows->sum('total_invoices'), 2) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['invoices'] }}</span></div>
            <div class="s-row"><span class="s-val">{{ number_format($storeRows->sum('total_payments'), 2) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['payments'] }}</span></div>
            <div class="s-row"><span class="s-val">{{ number_format($storeRows->sum('total_returns'), 2) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['returns'] }}</span></div>
            <div class="s-row"><span class="s-val {{ $storeRows->sum('total_debt') > 0 ? 'debt-pos' : 'debt-neg' }}">{{ number_format($storeRows->sum('total_debt'), 2) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['debt'] }}</span></div>
        </div>
        <div class="summary-gap"></div>
        <div class="summary-card">
            <div class="card-title">{{ $labels['customers'] }}</div>
            <div class="s-row"><span class="s-val">{{ number_format($customerRows->sum('total_invoices'), 2) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['invoices'] }}</span></div>
            <div class="s-row"><span class="s-val">{{ number_format($customerRows->sum('total_payments'), 2) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['payments'] }}</span></div>
            <div class="s-row"><span class="s-val">{{ number_format($customerRows->sum('total_returns'), 2) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['returns'] }}</span></div>
            <div class="s-row"><span class="s-val {{ $customerRows->sum('total_debt') > 0 ? 'debt-pos' : 'debt-neg' }}">{{ number_format($customerRows->sum('total_debt'), 2) }} {{ $labels['currency'] }}</span><span class="s-label">{{ $labels['debt'] }}</span></div>
        </div>
    </div>

    {{-- أعمدة الجدول معكوسة لأن DomPDF+RTL يعكسها --}}
    <table>
        <thead>
            <tr>
                <th style="width:6%">{{ $labels['debtStatus'] }}</th>
                <th style="width:13%">{{ $labels['debt'] }}</th>
                <th style="width:15%">{{ $labels['returns'] }}</th>
                <th style="width:15%">{{ $labels['payments'] }}</th>
                <th style="width:15%">{{ $labels['invoices'] }}</th>
                <th style="width:8%">{{ $labels['type'] }}</th>
                <th style="width:24%">{{ $labels['name'] }}</th>
                <th style="width:4%">#</th>
            </tr>
        </thead>
        <tbody>
            @foreach($processedRows as $i => $row)
            <tr>
                <td>
                    @if($row->total_debt > 0) <span class="badge-debtor">{{ $labels['debtor'] }}</span>
                    @elseif($row->total_debt < 0) <span class="badge-creditor">{{ $labels['creditor'] }}</span>
                    @else -- @endif
                </td>
                <td class="{{ $row->total_debt > 0 ? 'debt-pos' : ($row->total_debt < 0 ? 'debt-neg' : '') }}">{{ number_format($row->total_debt, 2) }}</td>
                <td>{{ number_format($row->total_returns, 2) }}</td>
                <td>{{ number_format($row->total_payments, 2) }}</td>
                <td>{{ number_format($row->total_invoices, 2) }}</td>
                <td><span class="{{ $row->is_store ? 'type-store' : 'type-customer' }}">{{ $row->type }}</span></td>
                <td>{{ $row->name }}</td>
                <td>{{ $i + 1 }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>
                    @if($grandDebt > 0) <span class="badge-debtor">{{ $labels['debtor'] }}</span>
                    @elseif($grandDebt < 0) <span class="badge-creditor">{{ $labels['creditor'] }}</span>
                    @else -- @endif
                </td>
                <td class="{{ $grandDebt > 0 ? 'debt-pos' : 'debt-neg' }}">{{ number_format($grandDebt, 2) }}</td>
                <td>{{ number_format($grandReturns, 2) }}</td>
                <td>{{ number_format($grandPayments, 2) }}</td>
                <td>{{ number_format($grandInvoices, 2) }}</td>
                <td colspan="3">{{ $labels['total'] }}</td>
            </tr>
        </tfoot>
    </table>

</div>
</body>
</html>
