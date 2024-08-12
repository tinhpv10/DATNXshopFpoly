@extends('layouts.profile')
@section('main-profile')
    @foreach($orders as $order)
    <div class="custom-container">
        <div class="custom-alert">
            <a href="#" class="custom-back-button text-black text-decoration-none ms-3">TRỞ LẠI</a>
            <p class="d-flex align-items-center mt-3 me-3">Đã hủy đơn hàng
                vào {{ $order->updated_at->format('H:i d-m-Y') }}.</p>
        </div>

        <div class="custom-order-item mt-3">
            <div class="custom-shop-name">
                <svg width="17" height="16" class="me-2" viewBox="0 0 17 16">
                    <title>Shop Icon</title>
                    <path
                        d="M1.95 6.6c.156.804.7 1.867 1.357 1.867.654 0 1.43 0 1.43-.933h.932s0 .933 1.155.933c1.176 0 1.15-.933 1.15-.933h.984s-.027.933 1.148.933c1.157 0 1.15-.933 1.15-.933h.94s0 .933 1.43.933c1.368 0 1.356-1.867 1.356-1.867H1.95zm11.49-4.666H3.493L2.248 5.667h12.437L13.44 1.934zM2.853 14.066h11.22l-.01-4.782c-.148.02-.295.042-.465.042-.7 0-1.436-.324-1.866-.86-.376.53-.88.86-1.622.86-.667 0-1.255-.417-1.64-.86-.39.443-.976.86-1.643.86-.74 0-1.246-.33-1.623-.86-.43.536-1.195.86-1.895.86-.152 0-.297-.02-.436-.05l-.018 4.79zM14.996 12.2v.933L14.984 15H1.94l-.002-1.867V8.84C1.355 8.306 1.003 7.456 1 6.6L2.87 1h11.193l1.866 5.6c0 .943-.225 1.876-.934 2.39v3.21z"
                        stroke-width=".3" stroke="#333" fill="#333" fill-rule="evenodd"></path>
                </svg>
                <span>{{ $order->shop->name ?? ''}}</span>
                <a class="btn btn-outline-secondary btn-sm ms-2 text-black text-decoration-none" href="{{ route('shop',['id' => $order->shop_id]) }}">Xem Shop</a>
            </div>
            @foreach($order->OrderDetail as $detail)
                <div class="custom-order-details">
                    <img src="{{ asset('storage/'.$detail->Product->mainMedia->media) ?? '' }}" alt="Product Image">
                    <div class="custom-order-info">
                        <p>{{ $detail->Product->name ?? ''}} </p>
                        @foreach($detail->Product->productVariation as $variation)
                            <p>{{ $variation->variation_name ?? '' }}</p>
                        @endforeach
                        <p>{{ $detail->product_quantity ?? '' }}</p>
                    </div>
                    <div class="custom-order-price">
                        <p class="text-danger mb-0">{{ number_format($detail->Product->getPrice()) }} VND</p>
                    </div>
                </div>
            @endforeach
            <div class="custom-order-actions">
                <table>
                    <tr>
                        <td>Yêu cầu bởi:</td>
                        <td>{{ $order->canceled_by }}</td>
                    </tr>
                    <tr>
                        <td>Phương thức thanh toán:</td>
                        <td>COD</td>
                    </tr>
                    <tr>
                        <td>Mã đơn hàng:</td>
                        <td class="text-danger">{{ $order->code }}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="custom-reason">
            <p><strong>Lý do:</strong> {{ $order->cancel_reason }}</p>
        </div>
    </div>
    @endforeach
@endsection
