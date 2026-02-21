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
        @page { margin: 12px; }
        * { font-family: 'Cairo', 'DejaVu Sans', sans-serif; box-sizing: border-box; direction: rtl; }
        body { font-family: 'Cairo', 'DejaVu Sans', sans-serif; color: #000; margin: 0; padding: 12px; position: relative; background: #fff; direction: rtl; }
        @if($isInvalid)
        body::before {
            content: "{{ $labels['invalidWithdrawal'] }}";
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
        
        .container { border: 4px double #000; padding: 0; position: relative; }
        
        .company-header { text-align: center; padding: 15px; border-bottom: 2px solid #e0e0e0; }
        .company-header img { max-height: 130px; margin-bottom: 8px; }
        .company-header .company-name { font-size: 18px; font-weight: bold; color: #333; }
        
        .header { background: #eee; color: #000; padding: 14px 20px; text-align: center; border-bottom: 3px solid #000; }
        .header .title { font-size: 30px; font-weight: 900; margin: 0 0 7px 0; letter-spacing: 2px; }
        .header .withdrawal-number { font-size: 17px; font-weight: bold; margin: 0; letter-spacing: 1px; }
        
        .content { padding: 17px; }
        
        .info-section { margin-bottom: 18px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 10px 13px; font-size: 14px; border-bottom: 1px solid #e0e0e0; }
        .info-table td.label { font-weight: 900; text-align: right; width: 35%; background: #f8f8f8; border-left: 4px solid #000; }
        .info-table td.value { text-align: right; font-weight: bold; width: 65%; }
        
        .amount-section { text-align: center; padding: 20px 17px; background: #f8f8f8; border: 3px solid #000; margin: 22px 0; }
        .amount-section .label { font-size: 14px; font-weight: 900; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px; }
        .amount-section .value { font-size: 34px; font-weight: 900; margin: 0; letter-spacing: 2px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="company-header">
            @if($logoBase64)
            <img src="data:image/png;base64,{{ $logoBase64 }}" alt="شعار الشركة">
            @endif
            <div class="company-name">{!! $companyName !!}</div>
        </div>
        
        <div class="header">
            <div class="title">{{ $title }}</div>
            <div class="withdrawal-number">#{{ $withdrawalNumber }}</div>
        </div>
        
        <div class="content">
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
                <div class="value">{{ $labels['currency'] }} {{ $amount }}</div>
            </div>
            
            @if($notes)
            <div style="background: #f8f8f8; padding: 12px; border: 2px solid #ddd; margin: 15px 0; border-radius: 8px;">
                <div style="font-size: 13px; font-weight: 900; margin-bottom: 6px; color: #000;">{{ $labels['notes'] }}</div>
                <div style="font-size: 12px; line-height: 1.6;">{{ $notes }}</div>
            </div>
            @endif
        </div>
    </div>
</body>
</html>
