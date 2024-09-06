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

        .shop-info,
        .buyer-info {
            margin-top: 15px;
        }

        .shop-info div,
        .buyer-info div {
            margin-bottom: 5px;
        }

        .product-table {
            width: 100%;
            margin-top: 15px;
            border-collapse: collapse;
        }

        .product-table th,
        .product-table td {
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

        .barcode,
        .qrcode {
            text-align: center;
        }

        .barcode img,
        .qrcode img {
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

        /* Adjust image sizes and layout */
        .header img {
            width: 100%;
            max-width: 150px;
        }

        .text-end img {
            max-width: 200px;
            height: auto;
        }
    </style>
</head>

<body>
<div class="shipping-label ms-5">
    <!-- Header -->
    <div class="header pb-2 border-bottom">
        <div class="row">
            <div class="col-8 text-end">
                <img src="https://img.freepik.com/premium-vector/d-logo-design_731343-825.jpg?w=740" style="display: flex; justify-content: center;" alt="Shopee Logo" width="100px" height="100px" class=" img-fluid mt-4">
                <img src="https://vietpos.sgp1.digitaloceanspaces.com/wp-content/uploads/2017/04/26010706/1d-code.png" alt="Barcode" height="60px" class="img-fluid mt-2">
                <div class="order-info">
                    <p>Ma van Don: {{ $record->lading_code ?? ''}}</p>
                    <p>Ma Don Hang: {{ $record->code ?? ''}}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content" class="mt-3">
        <div class="row">
            <div class="col-6 shop-info bg-secondary">
                <strong>Tu:</strong>
                <div>{{ $record->shop->name ?? '' }}</div>
                <div>{{ $record->shop->getFullAddress() ?? ''}}</div>
                <div>Phone: {{ $record->shop->phone ?? '' }}</div>
            </div>
            <div class="col-6 buyer-info bg-primary">
                <strong>Den:</strong>
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
                <th>San pham</th> <!-- 'San pham' thành 'Product' -->
                <th>So luong</th> <!-- 'SL' thành 'Qty' -->
                <th>Gia</th> <!-- 'Gia' thành 'Price' -->
            </tr>
            </thead>
            <tbody>
            @foreach($record->OrderDetail as $detail)
                <tr>
                    <td>{{ $detail->product->name }}</td> <!-- Tên sản phẩm -->
                    <td>{{ $detail->product_quantity }}</td> <!-- Số lượng -->
                    <td>{{ number_format($detail->product_price, 0, ',', '.') }} VND</td> <!-- Giá sản phẩm -->
                </tr>

{{--                <!-- Hiển thị các biến thể của sản phẩm trong đơn hàng -->--}}
{{--                @foreach($detail->productVariations() as $variation)--}}
{{--                    @foreach($variation->appProductVariationValue as $value)--}}
{{--                        <tr>--}}
{{--                            <td colspan="3">{{ $variation->variation_name }}: {{ $value->variation_value_name }}</td>--}}
{{--                        </tr>--}}
{{--                    @endforeach--}}
{{--                @endforeach--}}
            @endforeach
            </tbody>
        </table>
    </div>



    <!-- Total & Signature -->
    <div class="total mt-3">
        <strong>Tong tien: {{ number_format((float)$record->total_price ,0,',','.')}} VND</strong>
    </div>
    <br>
    <div class="row">
        <div class="col-6">
            <div class="receiver-signature">
                <strong>Ky nhan:</strong>
                <div class="signature-box"></div>
            </div>
        </div>
        <div class="col-6">
            <div class="code-section">
                <div class="qrcode">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/3/30/Superqr.svg" alt="QR Code">
                    <p>028AA1</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer mt-4">
        <strong>Khong dong kiem</strong>
    </div>
</div>
</body>

</html>
