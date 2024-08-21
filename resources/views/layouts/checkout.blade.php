@extends('index')
@section('main')
    <div class="main">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 py-3">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="" class="text-decoration-none">Chi tiết</a></li>
                </ol>
            </nav>
        </div>

        <div class="container-lg section-checkout">
            <div class="row">
                <div class="checkout-address">
                    <h5><i class="bi bi-geo-alt-fill"></i> Địa chỉ nhận hàng</h5>
                    <div>
                        @if(!$address)
                            Vui lòng cập nhật địa chỉ
                            <a href="{{ route('profile.address') }}" class="text-primary text-decoration-none ms-5">Cập nhật</a>
                        @else
                            <strong class="me-3">{{ $address->name }} {{ $address->phone }}</strong>
                            {{ $address->address_specific }}, {{ $address->ward->name }}, {{ $address->district->name }}, {{ $address->province->name }}
                            <a href="{{ route('profile.address') }}" class="text-primary text-decoration-none ms-5">Thay đổi</a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row checkout-product">
                <div class="product-header">
                    <div class="row">
                        <div class="col-6">
                            <h5>Sản phẩm</h5>
                        </div>
                        <div class="col-2 text-end">Đơn giá</div>
                        <div class="col-2 text-end">Số lượng</div>
                        <div class="col-2 text-end">Thành tiền</div>
                    </div>
                </div>

                @foreach($products as $item)
                    <div class="product-shop">
                        <div class="product-shop-item">
                            <div class="row">
                                <div class="col-6">
                                    <div class="d-flex flex-row">
                                        <div class="box-img">
                                            <img src="{{ asset('storage/' . $item['media']) }}" alt="">
                                        </div>
                                        <div class="box-name">
                                            {{ $item['product']->name }}

                                            @if(isset($item['variations']))
                                                <br>
                                                @foreach($item['variations'] as $variation)
                                                    <strong>{{ $variation['variation_name'] ?? '' }}:</strong> {{ $variation['variation_value'] ?? ''}}
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-2 text-end">{{ number_format($item['price'], 0, ',', '.') }} VND</div>
                                <div class="col-2 text-end">{{ $item['quantity'] }}</div>
                                <div class="col-2 text-end">{{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} VND</div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="bg-primary bg-opacity-10 py-3">
                    <div class="row align-items-center pe-5">
                        <div class="col-6">
                            <div class="row g-3 align-items-center">
                                <div class="col-auto">
                                    <label for="note" class="col-form-label">Lời nhắn:</label>
                                </div>
                                <div class="col-auto">
                                    <input type="text" id="note" class="form-control mb-0" placeholder="Lưu ý cho người bán">
                                </div>
                            </div>
                        </div>
                        <div class="col-6 border-start">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>Đơn vị vận chuyển: Nhanh</div>
                                <div>34.000 VND</div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row pe-5">
                        <div class="col-6 offset-6">
                            <div class="d-flex justify-content-end align-items-center">
                                Tổng số tiền ({{ count($products) }} sản phẩm):
                                <div id="totalPayment" class="text-danger fw-medium fs-5 ms-2">{{ number_format($totalPayment, 0, ',', '.') }} VND</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row bg-white checkout-voucher py-3">
                <div class="col-6"><i class="bi bi-ticket-perforated text-danger"></i> Donna voucher</div>
                <div class="col-6 text-end">Chọn mã giảm giá</div>
            </div>

            <div class="row checkout-total">
                <div class="d-flex justify-content-between align-items-center bg-white py-3 border-bottom">
                    <div>Phương thức thanh toán</div>
                    <div class="pe-5">
                        <select class="form-select" id="paymentMethod">
                            <option value="">Thanh toán khi nhận hàng</option>
                            <option value="">Thanh toán VNPAY</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex flex-column align-items-end bg-warning bg-opacity-10 py-3">
                    <div class="pe-5">Tổng tiền hàng: <span id="totalAmount" class="ms-5">{{ number_format($totalPayment, 0, ',', '.') }} VND</span></div>
                    <div class="py-2 pe-5">Tổng phí vận chuyển: <span id="totalShippingFee" class="ms-5">{{ number_format($shippingFee, 0, ',', '.') }} VND</span></div>
                    <div class="pe-5">Tổng thanh toán: <span id="totalWithShipping" class="ms-5">{{ number_format($totalPayment + $shippingFee, 0, ',', '.') }} VND</span></div>
                    <form id="paymentForm" action="{{ route('vnpay.payment') }}" method="POST">
                        @csrf
                        <input type="hidden" name="amount" id="amount">
                        <input type="hidden" name="selected_items" id="selectedItems">
                        <button type="submit" class="btn btn-primary w-100">Thanh Toán</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function updateTotal() {
                var selectedItems = [];
                @foreach ($products as $product)
                selectedItems.push('{{ $product['product']->id }}');
                @endforeach

                $.ajax({
                    url: '{{ route('checkout.calculateTotalAjax') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        selectedItems: selectedItems
                    },
                    success: function(response) {
                        if (response.error) {
                            alert(response.error);
                        } else {
                            $('#totalPayment').text(response.totalPayment + ' VND');
                            $('#totalWithShipping').text(response.totalWithShipping + ' VND');
                            $('#amount').val(response.totalWithShipping.replace(/\D/g, ''));

                            // Cập nhật giá trị cho trường selected_items
                            $('#selectedItems').val(JSON.stringify(selectedItems));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Có lỗi xảy ra:", error);
                    }
                });
            }

            updateTotal();

            $('#paymentMethod').change(function() {
                updateTotal();
            });
        });

        $(document).ready(function() {
            // Hàm gửi yêu cầu Ajax
            function processCheckout() {
                $.ajax({
                    url: '{{ route('checkout.process') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        // Bạn có thể gửi thêm dữ liệu nếu cần
                    },
                    success: function(response) {
                        // Xử lý kết quả trả về, ví dụ:
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            // Hiển thị thông báo lỗi hoặc thành công
                            alert('Có lỗi xảy ra hoặc thành công!');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Xử lý lỗi nếu có
                        console.error('Có lỗi xảy ra:', error);
                    }
                });
            }

            // Gọi hàm khi cần
            processCheckout();
        });
    </script>



@endsection
