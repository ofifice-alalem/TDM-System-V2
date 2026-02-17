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
        
        .container { border: 4px double #000; padding: 0; position: relative; }
        
        .company-header { text-align: center; padding: 20px; border-bottom: 2px solid #e0e0e0; }
        .company-header img { max-height: 150px; margin-bottom: 10px; }
        .company-header .company-name { font-size: 18px; font-weight: bold; color: #333; }
        
        .header { background: #000; color: #fff; padding: 20px; text-align: center; border-bottom: 3px solid #000; }
        .header .title { font-size: 32px; font-weight: 900; margin: 0 0 12px 0; letter-spacing: 2px; }
        .header .receipt-number { font-size: 18px; font-weight: bold; margin: 0; letter-spacing: 1px; }
        
        .content { padding: 20px; }
        
        .info-section { margin-bottom: 25px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { padding: 12px 15px; font-size: 15px; border-bottom: 1px solid #e0e0e0; }
        .info-table td.label { font-weight: 900; text-align: right; width: 35%; background: #f8f8f8; border-left: 4px solid #000; }
        .info-table td.value { text-align: right; font-weight: bold; width: 65%; }
        
        .amount-section { text-align: center; padding: 25px 20px; background: #f8f8f8; border: 3px solid #000; margin: 30px 0; }
        .amount-section .label { font-size: 15px; font-weight: 900; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 1px; }
        .amount-section .value { font-size: 36px; font-weight: 900; margin: 0; letter-spacing: 2px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="company-header">
            @if($logoBase64)
            <img src="data:image/png;base64,{{ $logoBase64 }}" alt="شعار الشركة">
            @endif
            <div class="company-name">{{ $companyName }}</div>
        </div>
        
        <div class="header">
            <div class="title">{{ $title }}</div>
            <div class="receipt-number">#{{ $paymentNumber }}</div>
        </div>
        
        <div class="content">
            <div class="info-section">
                <table class="info-table">
                    <tr>
                        <td class="value">{{ $customerName }}</td>
                        <td class="label">{{ $labels['customer'] }}</td>
                    </tr>
                    <tr>
                        <td class="value">{{ $customerPhone }}</td>
                        <td class="label">{{ $labels['phone'] }}</td>
                    </tr>
                    <tr>
                        <td class="value">{{ $date }}</td>
                        <td class="label">{{ $labels['date'] }}</td>
                    </tr>
                    <tr>
                        <td class="value">{{ $paymentMethod }}</td>
                        <td class="label">{{ $labels['paymentMethod'] }}</td>
                    </tr>
                </table>
            </div>
            
            <div class="amount-section">
                <div class="label">{{ $labels['amount'] }}</div>
                <div class="value">{{ $amount }} {{ $labels['currency'] }}</div>
            </div>
        </div>
    </div>
</body>
</html>
