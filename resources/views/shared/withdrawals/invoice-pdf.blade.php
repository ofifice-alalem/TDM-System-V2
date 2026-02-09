<!DOCTYPE html>
<html dir="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>طلب سحب أرباح</title>
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
            content: "{{ $labels['invalidWithdrawal'] }}";
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            font-weight: 900;
            color: rgba(0, 0, 0, 0.08);
            z-index: 1000;
            pointer-events: none;
            white-space: nowrap;
        }
        @endif
        
        .container { border: 4px double #000; padding: 0; position: relative; min-height: 1050px; }
        
        .header { background: #000; color: #fff; padding: 20px; text-align: center; border-bottom: 3px solid #000; }
        .header .title { font-size: 32px; font-weight: 900; margin: 0 0 12px 0; letter-spacing: 2px; }
        .header .withdrawal-number { font-size: 18px; font-weight: bold; margin: 0; letter-spacing: 1px; }
        
        .content { padding: 20px; padding-bottom: 120px; }
        
        .info-section { margin-bottom: 25px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 12px 15px; font-size: 15px; border-bottom: 1px solid #e0e0e0; }
        .info-table td.label { font-weight: 900; text-align: right; width: 35%; background: #fff; border-left: 4px solid #000; }
        .info-table td.value { text-align: right; font-weight: bold; width: 65%; }
        
        .amount-section { text-align: center; padding: 30px 20px; background: #fff; border: 3px solid #000; margin: 30px 0; }
        .amount-section .label { font-size: 15px; font-weight: 900; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .amount-section .value { font-size: 42px; font-weight: 900; margin: 0; letter-spacing: 2px; color: #000; }
        
        .notes-section { background: #fff; padding: 15px; border: 2px solid #000; margin: 20px 0; }
        .notes-section .label { font-size: 14px; font-weight: 900; margin-bottom: 8px; color: #000; }
        .notes-section .value { font-size: 13px; line-height: 1.6; }
        
        .signatures { padding: 20px; border-bottom: 4px double #000; background: #fff; position: absolute; bottom: 0; left: 0; right: 0; }
        .signatures table { width: 100%; border-collapse: collapse; }
        .signatures td { text-align: center; padding: 40px 10px 10px 10px; font-size: 14px; font-weight: 900; width: 50%; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">{{ $title }}</div>
            <div class="withdrawal-number">#{{ $withdrawalNumber }}</div>
        </div>
        
        <div class="content">
            <div>
            <div class="info-section">
                <table class="info-table">
                    <tr>
                        <td class="value">{{ $marketerName }}</td>
                        <td class="label">{{ $labels['marketer'] }}</td>
                    </tr>
                    <tr>
                        <td class="value">{{ $date }}</td>
                        <td class="label">{{ $labels['date'] }}</td>
                    </tr>
                    <tr>
                        <td class="value">{{ $status }}</td>
                        <td class="label">{{ $labels['status'] }}</td>
                    </tr>
                    @if($approvedBy)
                    <tr>
                        <td class="value">{{ $approvedBy }}</td>
                        <td class="label">{{ $labels['approvedBy'] }}</td>
                    </tr>
                    @endif
                    @if($rejectedBy)
                    <tr>
                        <td class="value">{{ $rejectedBy }}</td>
                        <td class="label">{{ $labels['rejectedBy'] }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            
            <div class="amount-section">
                <div class="label">{{ $labels['amount'] }}</div>
                <div class="value">{{ $amount }} {{ $labels['currency'] }}</div>
            </div>
            
            @if($notes)
            <div class="notes-section">
                <div class="label">{{ $labels['notes'] }}</div>
                <div class="value">{{ $notes }}</div>
            </div>
            @endif
            </div>
        </div>
            
        <div class="signatures">
            <table>
                <tr>
                    <td>{{ $labels['marketerSignature'] }}</td>
                    <td>{{ $labels['adminSignature'] }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
