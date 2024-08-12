<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <title>In phiếu giao</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }

        .shipping-label {
            width: 100%;
            max-width: 450px;
            border: 1px solid #ddd;
            padding: 15px;
            background-color: #fff;
        }

        .header img {
            max-width: 100%;
            height: auto;
        }

        .order-info p {
            margin: 0;
            font-size: 12px;
        }

        .shop-info, .buyer-info {
            margin-top: 15px;
        }

        .shop-info div, .buyer-info div {
            margin-bottom: 5px;
        }

        .product-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }

        .product-table th, .product-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }

        .total {
            margin-top: 15px;
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            font-size: 12px;
            background-color: #F1F5F9;
            padding: 10px;
            text-align: center;
        }

        /* QR and Barcode Section */
        .code-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 20px;
        }

        .barcode, .qrcode {
            text-align: center;
        }

        .barcode img, .qrcode img {
            width: 100px;
            height: 100px;
        }

        .receiver-signature {
            margin-top: 20px;
            text-align: center;
        }

        .signature-box {
            height: 50px;
            border: 1px solid #000;
            margin-top: 10px;
        }

        .clearfix {
            clear: both;
        }
    </style>
</head>

<body class="ms-5">
<div class="shipping-label ms-5">
    <!-- Header -->
    <div class="header pb-2 border-bottom">
        <div class="row">
            <div class="col-4">
                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Shopee_logo.svg/1200px-Shopee_logo.svg.png" alt="Shopee Logo" class="img-fluid">
            </div>
            <div class="col-8 text-end">
                <img src="https://via.placeholder.com/200x50" alt="Barcode" class="img-fluid mt-2">
                <div class="order-info">
                    <p>Mã vận đơn: {{ $record->code ?? ''}}</p>
                    <p>Mã đơn hàng: {{ $record->lading_code ?? ''}}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="mt-3">
        <div class="row">
            <div class="col-6 shop-info bg-secondary">
                <strong>Từ:</strong>
                <div>{{ $record->shop->name ?? '' }}</div>
                <div>{{ $record->shop->user->UserAddressFormatted ?? ''}}</div>
                <div>Phone: {{ $record->shop->name ?? '' }}</div>
            </div>
            <div class="col-6 buyer-info bg-primary">
                <strong>Đến:</strong>
                <div>{{ $record->user->name ?? '' }}</div>
                <div>{{ $record->user->UserAddressFormatted ?? ''}}</div>
                <div>Phone: {{ $record->user->phone ?? '' }}</div>
            </div>
        </div>
    </div>

    <!-- Product Info -->
    <div class="product-info mt-3">
        <table class="product-table">
            <thead>
            <tr>
                <th>Nội dung hàng (sản phẩm)</th>
                <th>SL</th>
                <th>Giá</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Điện thoại thông minh</td>
                <td>1</td>
                <td>4,500,900 VND</td>
            </tr>
            </tbody>
        </table>
    </div>

    <!-- Total & Signature -->
    <div class="total mt-3">
        <strong>Tổng tiền thu người nhận: 4,500,900 VND</strong>
    </div>
    <br>
    <div class="row">
        <div class="col-6">
            <div class="receiver-signature">
                <strong>Ký nhận:</strong>
                <div class="signature-box"></div>
            </div>
        </div>
        <div class="col-6">
            <div class="code-section">
                <div class="qrcode">
                    <img src="https://via.placeholder.com/100x100" alt="QR Code">
                    <p>028AA1</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer mt-4">
        <strong>Không đồng kiểm</strong>
    </div>
</div>
</body>
</html>
