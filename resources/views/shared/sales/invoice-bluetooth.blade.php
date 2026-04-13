<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة البلوتوث - {{ $sale->invoice_number }}</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;500;700&display=swap');

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            color: white;
        }

        .header h1 {
            font-size: 2rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        #invoice-container {
            background: white;
            padding: 2.5rem;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 350px;
            margin-bottom: 20px;
        }

        #invoice-to-print {
            width: 272px;
            margin: 0 auto;
            background: #fff;
            color: #000;
        }

        .inv-header {
            text-align: center;
            margin-bottom: 15px;
        }

        .inv-header h3 {
            font-size: 16px;
            margin: 0 0 10px 0;
            font-weight: bold;
        }

        .inv-title {
            background: #000;
            color: #fff;
            padding: 5px 15px;
            display: inline-block;
            font-size: 14px;
            font-weight: bold;
            margin: 10px 0;
        }

        .inv-line {
            border-top: 1px dotted #000;
            margin: 10px 0;
        }

        .inv-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .inv-table th {
            border-bottom: 1px solid #000;
            font-size: 12px;
            padding-bottom: 5px;
            font-weight: bold;
        }

        .inv-table td {
            font-size: 13px;
            padding: 5px 0;
        }

        .inv-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin: 3px 0;
        }

        #bluetooth-render-area {
            position: absolute;
            left: -9999px;
            top: 0;
            width: 576px;
            box-sizing: border-box;
            background: white;
            color: black;
            padding: 15px;
            font-family: "Segoe UI", Arial, sans-serif;
        }

        #bluetooth-render-area .bt-company {
            font-size: 40px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 15px;
        }

        #bluetooth-render-area .bt-title-box {
            text-align: center;
            margin-bottom: 25px;
        }

        #bluetooth-render-area .bt-title {
            background: #000;
            color: #fff;
            padding: 10px 40px;
            font-size: 32px;
            font-weight: bold;
            display: inline-block;
        }

        #bluetooth-render-area .bt-info {
            font-size: 26px;
            text-align: center;
            margin: 8px 0;
        }

        #bluetooth-render-area .bt-dot-line {
            border-top: 3px dashed #000;
            margin: 20px 0;
        }

        #bluetooth-render-area .bt-solid-line {
            border-top: 4px solid #000;
            margin: 20px 0;
        }

        #bluetooth-render-area .bt-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        #bluetooth-render-area .bt-table th {
            font-size: 28px;
            font-weight: bold;
            padding-bottom: 15px;
            text-align: right;
        }

        #bluetooth-render-area .bt-table td {
            font-size: 30px;
            padding: 15px 0;
            font-weight: 600;
        }

        #bluetooth-render-area .bt-total-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 30px;
        }

        #bluetooth-render-area .bt-total-label {
            font-size: 32px;
        }

        #bluetooth-render-area .bt-total-val {
            font-size: 42px;
            font-weight: bold;
        }

        #bluetooth-render-area .bt-footer {
            text-align: center;
            font-size: 26px;
            margin-top: 40px;
            line-height: 1.6;
        }

        .btn-bluetooth {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 18px 50px;
            border-radius: 50px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            margin: 10px;
        }

        .btn-bluetooth:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.6);
        }

        .btn-bluetooth:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        #bt-status {
            margin-top: 15px;
            font-size: 14px;
            color: white;
            text-align: center;
            padding: 10px;
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }

        .buttons-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>🖨️ طباعة البلوتوث المباشرة</h1>
        <p>اطبع الفاتورة مباشرة على طابعة X-Printer عبر البلوتوث</p>
    </div>

    <div id="invoice-container">
        <div id="invoice-to-print">
            <div class="inv-header">
                <h3>شركة المتفوقون الأوائل</h3>
                <div class="inv-title">فاتورة مبيعات</div>
                <p style="font-size:12px">{{ $sale->invoice_number }}<br>{{ $sale->created_at->format('Y-m-d') }}</p>
            </div>

            <div class="inv-line"></div>

            <div class="inv-row">
                <span>المتجر:</span>
                <span style="font-weight:bold">{{ $sale->store->name }}</span>
            </div>
            <div class="inv-row">
                <span>المسوق:</span>
                <span>{{ $sale->marketer->full_name }}</span>
            </div>

            <div class="inv-line"></div>

            <table class="inv-table">
                <thead>
                    <tr>
                        <th style="text-align:right">المنتج</th>
                        <th style="text-align:center">كمية</th>
                        <th style="text-align:left">إجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr>
                        <td style="text-align:right">{{ $item->product->name }}</td>
                        <td style="text-align:center">{{ $item->quantity + $item->free_quantity }}</td>
                        <td style="text-align:left">{{ number_format(($item->quantity + $item->free_quantity) * $item->unit_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="inv-line"></div>

            @if($sale->product_discount > 0)
            <div class="inv-row" style="color: #059669;">
                <span>تخفيض المنتجات:</span>
                <span>- {{ number_format($sale->product_discount, 2) }}</span>
            </div>
            @endif

            @if($sale->invoice_discount_amount > 0)
            <div class="inv-row" style="color: #059669;">
                <span>تخفيض الفاتورة:</span>
                <span>- {{ number_format($sale->invoice_discount_amount, 2) }}</span>
            </div>
            @endif

            <div class="inv-row" style="margin-top: 10px;">
                <span style="font-weight:bold; font-size:15px;">المجموع النهائي:</span>
                <span style="font-weight:bold; font-size:16px;">{{ number_format($sale->total_amount, 2) }} د.ل</span>
            </div>

            <div class="inv-header" style="margin-top:20px;">
                <p style="font-size:11px;">شكراً لتعاملكم معنا<br>{{ $sale->store->name }}</p>
            </div>
        </div>
    </div>

    <div id="bluetooth-render-area"></div>

    <div class="buttons-container">
        <button class="btn-bluetooth" onclick="printViaBluetooth()">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m7 7 10 10-5 5V2l5 5L7 17" />
            </svg>
            طباعة عن طريق البلوتوث
        </button>
        <button class="btn-bluetooth" onclick="window.close()" style="background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            إغلاق
        </button>
    </div>
    <div id="bt-status">حالة الاتصال: غير متصل</div>

    <script>
        let bluetoothDevice = null;
        let bluetoothCharacteristic = null;

        async function printViaBluetooth() {
            const btn = document.querySelector('.btn-bluetooth');
            const statusText = document.getElementById('bt-status');
            const originalText = btn.innerHTML;

            btn.innerHTML = '⏳ جاري المعالجة...';
            btn.disabled = true;

            try {
                if (!navigator.bluetooth) {
                    alert('❌ متصفحك أو جهازك لا يدعم تقنية Web Bluetooth.');
                    throw new Error('Web Bluetooth API not supported');
                }

                statusText.innerText = '⚡ بناء الفاتورة للطباعة...';

                const renderArea = document.getElementById('bluetooth-render-area');
                const originalInv = document.getElementById('invoice-to-print');

                renderArea.innerHTML = `
                    <div class="bt-company">${originalInv.querySelector('h3').innerText}</div>
                    <div class="bt-title-box"><div class="bt-title">${originalInv.querySelector('.inv-title').innerText}</div></div>
                    <div class="bt-info">رقم: ${originalInv.querySelector('.inv-header p').innerHTML.split('<br>')[0]}</div>
                    <div class="bt-info">تاريخ: ${originalInv.querySelector('.inv-header p').innerHTML.split('<br>')[1]}</div>
                    <div class="bt-dot-line"></div>
                    <table class="bt-table">
                        <thead>
                            <tr><th style="text-align:right">المنتج</th><th style="text-align:center">كمية</th><th style="text-align:left">السعر</th></tr>
                        </thead>
                    </table>
                    <div class="bt-solid-line"></div>
                    <table class="bt-table">
                        <tbody>
                            ${Array.from(originalInv.querySelectorAll('.inv-table tbody tr')).map(tr => `
                                <tr>
                                    <td style="text-align:right; width:50%">${tr.cells[0].innerText}</td>
                                    <td style="text-align:center; width:20%">${tr.cells[1].innerText}</td>
                                    <td style="text-align:left; width:30%">${tr.cells[2].innerText}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                    <div class="bt-dot-line"></div>
                    <div class="bt-total-section">
                        <span class="bt-total-label">المجموع:</span>
                        <span class="bt-total-val">${originalInv.querySelectorAll('.inv-row')[originalInv.querySelectorAll('.inv-row').length - 1].querySelector('span:last-child').innerText}</span>
                    </div>
                    <div class="bt-footer">
                        ${originalInv.querySelectorAll('.inv-header p')[1].innerHTML}
                    </div>
                `;

                const canvas = await html2canvas(renderArea, {
                    scale: 1,
                    backgroundColor: '#ffffff'
                });

                statusText.innerText = '⚡ تحويل الصورة لبيانات الطابعة...';
                const rasterData = canvasToRaster(canvas);

                if (!bluetoothDevice || !bluetoothDevice.gatt.connected) {
                    statusText.innerText = '📡 يرجى اختيار طابعة البلوتوث...';
                    btn.innerHTML = '📡 بانتظار اختيار الطابعة...';
                    bluetoothDevice = await navigator.bluetooth.requestDevice({
                        filters: [{ services: ['000018f0-0000-1000-8000-00805f9b34fb'] }],
                        optionalServices: ['000018f0-0000-1000-8000-00805f9b34fb']
                    });

                    bluetoothDevice.addEventListener('gattserverdisconnected', () => {
                        document.getElementById('bt-status').innerText = 'حالة الاتصال: غير متصل ❌';
                        bluetoothDevice = null;
                        bluetoothCharacteristic = null;
                    });

                    statusText.innerText = '🔌 جاري الاتصال بالطابعة...';
                    const server = await bluetoothDevice.gatt.connect();
                    const service = await server.getPrimaryService('000018f0-0000-1000-8000-00805f9b34fb');
                    bluetoothCharacteristic = await service.getCharacteristic('00002af1-0000-1000-8000-00805f9b34fb');
                }

                statusText.innerText = '🖨️ جاري الطباعة...';
                btn.innerHTML = '🖨️ جاري النقل للطابعة...';
                await sendInChunks(bluetoothCharacteristic, rasterData, statusText);

                statusText.innerText = '✅ اكتملت الطباعة بنجاح! متصل بالطابعة.';
                alert('✅ تم إرسال الفاتورة للطابعة');

            } catch (error) {
                console.error(error);
                statusText.innerText = '❌ فشلت العملية: ' + error.message;
                if (error.name === 'NotFoundError') {
                    statusText.innerText = 'حالة الاتصال: تم الإلغاء';
                }
                bluetoothDevice = null;
                bluetoothCharacteristic = null;
            } finally {
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        }

        function canvasToRaster(canvas) {
            const ctx = canvas.getContext('2d');
            const width = canvas.width;
            const height = canvas.height;
            const imageData = ctx.getImageData(0, 0, width, height).data;

            let lastUsedRow = height;
            for (let y = height - 1; y >= 0; y--) {
                let hasData = false;
                for (let x = 0; x < width; x++) {
                    const idx = (y * width + x) * 4;
                    if (imageData[idx + 3] > 0 && imageData[idx] < 220) { hasData = true; break; }
                }
                if (hasData) { lastUsedRow = y + 20; break; }
            }
            if (lastUsedRow > height) lastUsedRow = height;

            const widthBytes = Math.ceil(width / 8);
            const raster = [];

            raster.push(0x1B, 0x40);
            raster.push(0x1D, 0x76, 0x30, 0x00);
            raster.push(widthBytes & 0xFF, (widthBytes >> 8) & 0xFF);
            raster.push(lastUsedRow & 0xFF, (lastUsedRow >> 8) & 0xFF);

            for (let y = 0; y < lastUsedRow; y++) {
                for (let x = 0; x < widthBytes; x++) {
                    let byte = 0;
                    for (let bit = 0; bit < 8; bit++) {
                        const xPos = x * 8 + bit;
                        if (xPos < width) {
                            const idx = (y * width + xPos) * 4;
                            const br = (imageData[idx] * 0.299 + imageData[idx + 1] * 0.587 + imageData[idx + 2] * 0.114);
                            if (imageData[idx + 3] > 128 && br < 185) { byte |= (0x80 >> bit); }
                        }
                    }
                    raster.push(byte);
                }
            }

            raster.push(0x1B, 0x4A, 0x20, 0x1B, 0x64, 0x05, 0x1D, 0x56, 0x00);

            return new Uint8Array(raster);
        }

        async function sendInChunks(characteristic, data, statusText) {
            const CHUNK_SIZE = 180;
            const total = data.length;

            for (let i = 0; i < total; i += CHUNK_SIZE) {
                const chunk = data.slice(i, i + CHUNK_SIZE);

                if (characteristic.writeValueWithoutResponse) {
                    await characteristic.writeValueWithoutResponse(chunk);
                } else {
                    await characteristic.writeValue(chunk);
                }

                if (i % (CHUNK_SIZE * 5) === 0) {
                    const percent = Math.round((i / total) * 100);
                    statusText.innerText = `🖨️ جاري الإرسال للطابعة: ${percent}%`;
                }

                await new Promise(r => setTimeout(r, 2));
            }
        }
    </script>
</body>
</html>
