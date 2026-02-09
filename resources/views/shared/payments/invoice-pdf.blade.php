<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>إيصال قبض</title>
    <style>
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path("fonts/Cairo-Regular.ttf") }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path("fonts/Cairo-Bold.ttf") }}') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        @font-face {
            font-family: 'Cairo';
            src: url('{{ public_path("fonts/Cairo-ExtraBold.ttf") }}') format('truetype');
            font-weight: 900;
            font-style: normal;
        }
        @page { margin: 15px; }
        * { font-family: 'Cairo', 'DejaVu Sans', sans-serif; box-sizing: border-box; direction: rtl; }
        body { font-family: 'Cairo', 'DejaVu Sans', sans-serif; color: #000; margin: 0; padding: 15px; position: relative; background: #fff; direction: rtl; }
        @if($isInvalid)
        body::before {
            content: "{{ $labels['invalidPayment'] }}";
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            font-weight: 900;
            color: rgba(220, 53, 69, 0.12);
            z-index: 1000;
            pointer-events: none;
            white-space: nowrap;
        }
        @endif
        
        .container { border: 4px double #000; padding: 0; display: flex; flex-direction: column; }
        
        .header { background: #000; color: #fff; padding: 20px; text-align: center; border-bottom: 3px solid #000; }
        .header .title { font-size: 32px; font-weight: 900; margin: 0 0 12px 0; letter-spacing: 2px; }
        .header .receipt-number { font-size: 18px; font-weight: bold; margin: 0; letter-spacing: 1px; }
        
        .content { padding: 20px; display: flex; flex-direction: column; }
        
        .info-section { margin-bottom: 25px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 12px 15px; font-size: 15px; border-bottom: 1px solid #e0e0e0; }
        .info-table td.label { font-weight: 900; text-align: right; width: 35%; background: #f8f8f8; border-left: 4px solid #000; }
        .info-table td.value { text-align: right; font-weight: bold; width: 65%; }
        
        .amounts-row { display: table; width: 100%; margin: 30px 0; border-collapse: separate; border-spacing: 15px; }
        .amount-section { display: table-cell; text-align: center; padding: 25px 20px; background: #f8f8f8; border: 3px solid #000; width: 50%; vertical-align: top; }
        .amount-section .label { font-size: 15px; font-weight: 900; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .amount-section .value { font-size: 36px; font-weight: 900; margin: 0; letter-spacing: 2px; }
        
        .debt-section { display: table-cell; text-align: center; padding: 25px 20px; background: #fff; border: 3px solid #000; width: 50%; vertical-align: top; }
        .debt-section .label { font-size: 15px; font-weight: 900; margin-bottom: 12px; }
        .debt-section .value { font-size: 36px; font-weight: 900; margin: 0; letter-spacing: 2px; }
        .debt-section .no-debt { font-size: 17px; font-weight: bold; color: #666; font-style: italic; }
        
        .signatures { margin-top: 60px; padding: 20px; border-top: 3px solid #000; background: #fafafa; }
        .signatures table { width: 100%; border-collapse: collapse; }
        .signatures td { text-align: center; padding: 40px 10px 10px 10px; font-size: 14px; font-weight: 900; border-top: 2px solid #000; width: 33.33%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">{{ $title }}</div>
            <div class="receipt-number">#{{ $paymentNumber }}</div>
        </div>
        
        <div class="content">
            <div class="info-section">
                <table class="info-table">
                    <tr>
                        <td class="value">{{ $storeName }}</td>
                        <td class="label">{{ $labels['store'] }}</td>
                    </tr>
                    <tr>
                        <td class="value">{{ $marketerName }}</td>
                        <td class="label">{{ $labels['marketer'] }}</td>
                    </tr>
                    <tr>
                        <td class="value">{{ $date }}</td>
                        <td class="label">{{ $labels['date'] }}</td>
                    </tr>
                    <tr>
                        <td class="value">{{ $paymentMethod }}</td>
                        <td class="label">{{ $labels['paymentMethod'] }}</td>
                    </tr>
                    <tr>
                        <td class="value">{{ $status }}</td>
                        <td class="label">{{ $labels['status'] }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="amounts-row">
                <div class="amount-section">
                    <div class="label">{{ $labels['amount'] }}</div>
                    <div class="value">{{ $amount }} {{ $labels['currency'] }}</div>
                </div>
                
                <div class="debt-section">
                    <div class="label">{{ $labels['remainingDebt'] }}</div>
                    @if($remainingDebt > 0)
                        <div class="value">{{ number_format($remainingDebt, 2) }} {{ $labels['currency'] }}</div>
                    @else
                        <div class="no-debt">{{ $labels['noDebt'] }}</div>
                    @endif
                </div>
            </div>
            
            <div class="signatures">
                <table>
                    <tr>
                        <td>{{ $labels['store'] }}</td>
                        <td>{{ $labels['marketer'] }}</td>
                        <td>{{ $labels['keeper'] }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
