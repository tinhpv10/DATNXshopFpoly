@extends('index')
@section('main')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main ">
        <div class="section-cart pt-4">
            <div class="container">
                @if(\Illuminate\Support\Facades\Auth::check())
                    @if($groupedItems->isNotEmpty())
                        <h4 class="mb-4">Giỏ hàng</h4>
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-8 ">
                                @foreach($groupedItems as $shopId => $cartItems)
                                    @php
                                        $shop = \App\Models\Shop::find($shopId); // Lấy thông tin shop
                                    @endphp
                                    <div class="shop-group">

                                        <div class="shop-info d-flex align-items-center mb-3">
                                            <span class="fw-bold">{{ $shop->name ?? 'Không có tên cửa hàng' }}</span>
                                            <a class="btn btn-outline-secondary btn-sm ms-2 text-black text-decoration-none"
                                               href="{{ url('shop', ['id' => $shop->id]) }}">Xem Shop</a>
                                        </div>
                                        @foreach($cartItems as $cartItem)
                                            <div class="cart-item py-3 border my-3">
                                                <div class="row align-items-center">
                                                    <div class="col-12 col-md-8 col-lg-9">
                                                        <div class="d-flex">
                                                            <div class="box-checks">
                                                                <div class="form-check my-4">

                                                                    <input class="item-checkbox" type="checkbox"
                                                                           name="item_{{ $cartItem['product_id'] }}"
                                                                           value="{{ $cartItem['id'] }}"
                                                                           data-cart-id="{{ $cartItem['id'] }}"
                                                                           data-price="{{ $cartItem['price'] }}"
                                                                           checked>
                                                                </div>
                                                            </div>
                                                            <div class="box-img me-3 ms-2">
                                                                <img src="{{ asset('storage/' . $cartItem->media) }}"
                                                                     alt=""
                                                                     class="rounded-1">
                                                            </div>
                                                            <div class="box-content">
                                                                <div class="title">{{ $cartItem->product->name }}</div>
                                                                <!-- Hiển thị biến thể -->
                                                                @if(!empty($cartItem->variations))
                                                                    @foreach($cartItem->variations as $variation)
                                                                        <div class="text mb-2">
                                                                            {{ $variation['variation_name'] }}
                                                                            : {{ $variation['variation_value'] }}
                                                                        </div>
                                                                    @endforeach
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-6 col-md-3 col-lg-2">

                                                        <div class="row">
                                                            <div class="col-md-12 d-flex justify-content-end p-2">
                                                                <div class="price flex-grow-1 text-end d-flex align-items-center"
                                                                     id="itemPrice-{{ $cartItem->id }}"
                                                                     data-retail-price="{{ $cartItem->productStock->retail_price ?? $cartItem->product->getPrice() }}"
                                                                     style="white-space: nowrap;">
                                                                    {{ number_format($cartItem->productStock->retail_price ?? $cartItem->product->getPrice(), 0, ',', '.') }} VNĐ
                                                                </div>
                                                            </div>
                                                        </div>



                                                        <meta name="csrf-token" content="{{ csrf_token() }}">

                                                        <div class="quantity">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <button
                                                                        class="btn btn-outline-secondary rounded-start"
                                                                        type="button"
                                                                        onclick="updateQuantity(-1, {{ $cartItem->id }})">
                                                                        -
                                                                    </button>
                                                                </div>
                                                                <input type="number" class="form-control text-center"
                                                                       id="quantity-{{ $cartItem->id }}"
                                                                       value="{{ $cartItem->quantity }}" min="1">
                                                                <div class="input-group-append">
                                                                    <button
                                                                        class="btn btn-outline-secondary rounded-end"
                                                                        type="button"
                                                                        onclick="updateQuantity(1, {{ $cartItem->id }})">
                                                                        +
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <input type="hidden" id="cart-item-id"
                                                               value="{{ $cartItem->id }}"> <!-- ID sản phẩm -->

                                                        <script>

                                                        </script>
                                                    </div>
                                                    <div class="col-6 col-md-1 col-lg-1 d-flex justify-content-center">
                                                        <div class="delete">
                                                            <form
                                                                action="{{ route('cart.remove', ['cartItemId' => $cartItem->id]) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-link"><i
                                                                        class="bi bi-x-lg fs-4"></i></button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach

                                <div class="checkall">
                                    <div class="form-check my-4">
                                        <input class="form-check-input" type="checkbox" value="" id="selectAll">
                                        <label class="form-check-label" for="selectAll">Chọn Tất Cả</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-4">

                                <div class="card p-3">
                                    <div class="Address d-flex justify-content-between"><span>Địa Chỉ Nhận Hàng</span>
                                        <div>
                                            <a href="{{ route('profile.address') }}"
                                               class="product-link text-decoration-none text-black m-3">Xem
                                            </a>
                                        </div>
                                    </div>
                                    <div class="Voucher d-flex justify-content-between">
                                        <span>ShopX Voucher</span>
                                        <div>
                                            <button type="button" class="btn Voucher " data-bs-toggle="modal"
                                                    data-bs-target="#staticVoucher">
                                                Chọn hoặc nhập mã
                                            </button>
                                            <div class="modal fade" id="staticVoucher" data-bs-backdrop="static"
                                                 data-bs-keyboard="false" tabindex="-1"
                                                 aria-labelledby="staticBackdropLabel"
                                                 aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h1 class="modal-title fs-5" id="staticBackdropLabel">Chọn
                                                                ShopX
                                                                Voucher</h1>
                                                            <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal"
                                                                    aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            ...
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Trở lại
                                                            </button>
                                                            <button type="button" class="btn btn-primary">Đồng ý
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <div class="p-2"><h6>Tổng tiền hàng:</h6></div>
                                        <div class="price text-end p-2"
                                             id="totalPrice">{{ number_format($totalPrice, 0, ',', '.') }}
                                            VNĐ
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <div class="p-2"><h6>Tổng thanh toán ({{ count($cartItems) }}):</h6>
                                        </div>
                                        <div class="price text-end p-2"
                                             id="totalPayment">{{ number_format($totalPayment, 0, ',', '.') }} VNĐ
                                        </div>
                                    </div>
                                    {{--                            <form id="paymentForm" action="{{ route('vnpay.payment') }}" method="POST">--}}
                                    {{--                                @csrf--}}
                                    <input type="hidden" name="amount" value="{{ $totalPayment }}">
                                    <input type="hidden" name="selected_items" id="selectedItems">
                                    <form action="{{ route('checkout.show') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="selected_items" id="selectedItems">
                                        <button type="submit" class="btn btn-primary w-100" id="checkout-button">Thanh Toán</button>
                                    </form>

                                    {{--                            </form>--}}

                                    <div class="payment d-flex justify-content-center">
                                        <img class="mt-3 mx-2" src="{{ asset('image/payment/payment-1.png') }}" alt="">
                                        <img class="mt-3 mx-2" src="{{ asset('image/payment/payment-2.png') }}" alt="">
                                        <img class="mt-3 mx-2" src="{{ asset('image/payment/payment-3.png') }}" alt="">
                                        <img class="mt-3 mx-2" src="{{ asset('image/payment/payment-4.png') }}" alt="">
                                        <img class="mt-3 mx-2" src="{{ asset('image/payment/payment-5.png') }}" alt="">
                                    </div>
                                </div>
                            </div>


                        </div>
                    @else
                        <div class="row w-100 ">
                            <div class="bg-white box-myorder_img">
                                <img class="background-image" src="{{ asset('images/icon_my_order.png')  }}">
                                <span class="mb-2">Giỏ hàng bạn còn trống</span>
                                <a href="{{ url('/product') }}" type="button" class="btn btn-primary pt-2 text-decoration-none text-while">Mua hàng</a>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="row w-100 ">
                        <div class="bg-white box-myorder_img">
                            <img class="background-image" src="{{ asset('images/icon_my_order.png')  }}">
                            <span class="mb-2">Giỏ hàng bạn còn trống</span>
                            <a href="{{ url('/product') }}" type="button" class="btn btn-primary pt-2 text-decoration-none text-while">Mua hàng</a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="service">
                <div class="row justify-content-center my-4">
                    <div class="col-12 col-md-6 col-lg-3 j">
                        <div class="d-flex py-3">
                            <div class="service-icon me-3">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <div class="service-content">
                                <div class="title">An toàn</div>
                                <div class="text">Giúp bạn yên tâm hơn</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="d-flex py-3">
                            <div class="service-icon me-3">
                                <i class="fa-solid fa-message"></i>
                            </div>
                            <div class="service-content">
                                <div class="title">Liên hệ</div>
                                <div class="text">Hỗ trợ bạn mọi lúc</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="d-flex py-3">
                            <div class="service-icon me-3">
                                <i class="fa-solid fa-truck"></i>
                            </div>
                            <div class="service-content">
                                <div class="title">Giao hàng nhanh</div>
                                <div class="text">Tiện lợi nhanh chóng</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="section-recommend container">
                <h4 class="mb-3">Các sản phẩm đã thích</h4>
                <div class="list-product d-flex flex-wrap">
                    @if(!empty($recommendedProducts))
                        @foreach($recommendedProducts as $product)
                            <div class="product-item border rounded-2" style="margin: 5px;">
                                <a href="{{ route('product.detail', ['id' => $product->id]) }}" class="product-link text-decoration-none text-black">
                                    <div class="box-img">
                                        @if ($product->main_image)
                                            <img src="{{ asset('storage/' . $product->main_image) }}" alt="Product Image">
                                        @else
                                            <img src="https://thudaumot.binhduong.gov.vn/Portals/0/images/default.jpg" alt="Default Image">
                                        @endif
                                    </div>
                                    <div class="info-product">
                                        <div class="price-sale-container">
                                            <div class="salePrice">
                                                {{ $product->formattedSalePrice ?? $product->regular_price }} VNĐ
                                            </div>
                                            @if ($product->formattedSalePrice)
                                                <div class="product-price text-muted text-decoration-line-through">
                                                    {{ $product->formattedRegularPrice }} VNĐ
                                                </div>
                                            @endif
                                        </div>
                                        <div class="title-name fs-5">{{ $product->name }}</div>
                                        <div class="title-product">{{ $product->description }}</div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    <script>
        // bắt sự kiện onclick tắng giảm số lượng sản phẩm
        function updateQuantity(change, cartItemId) {
            const input = document.getElementById('quantity-' + cartItemId);
            let currentQuantity = parseInt(input.value, 10);
            let newQuantity = currentQuantity + change;

            if (newQuantity < 1) newQuantity = 1;

            input.value = newQuantity;
            updateCartItem(cartItemId, newQuantity);
        }

        function updateCartItem(cartItemId, quantity) {
            $.ajax({
                url: '/update-cart-item',
                type: 'POST',
                data: {
                    cartItemId: cartItemId,
                    quantity: quantity,
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                success: function(response) {
                    if (response.success) {
                        $('#itemPrice-' + cartItemId).text(response.newPrice);
                        $('#totalPrice').text(response.newPrice.toLocaleString() + ' VNĐ');
                        $('#totalPayment').text(response.totalPrice.toLocaleString() + ' VNĐ');
                    } else {
                        alert(response.message);
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error:', textStatus, errorThrown);
                    alert('Có lỗi xảy ra khi cập nhật giỏ hàng.');
                }
            });
        }



        // end
        document.addEventListener('DOMContentLoaded', function () {
            var selectAllCheckbox = document.getElementById('selectAll');

            selectAllCheckbox.addEventListener('change', function () {
                var itemCheckboxes = document.querySelectorAll('.box-checks .item-checkbox');
                itemCheckboxes.forEach(function (checkbox) {
                    checkbox.checked = selectAllCheckbox.checked;
                });
                updateTotals();
            });

            document.querySelectorAll('.box-checks .item-checkbox').forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    updateTotals();
                });
            });

            function updateTotals() {
                var newPrice = 0;
              // Giả định phí vận chuyển cố định là 30,000 đ
                var selectedCheckboxes = document.querySelectorAll('.box-checks .item-checkbox:checked');

                selectedCheckboxes.forEach(function (checkbox) {
                    newPrice += parseInt(checkbox.getAttribute('data-price'));
                });

                var totalPrice = newPrice ;
                document.getElementById('totalPrice').textContent = `${newPrice.toLocaleString()} đ`;
                document.getElementById('totalPayment').textContent = `${totalPrice.toLocaleString()} đ`;

                var allCheckboxes = document.querySelectorAll('.box-checks .item-checkbox');
                var allChecked = Array.from(allCheckboxes).every(checkbox => checkbox.checked);
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = !allChecked && Array.from(allCheckboxes).some(checkbox => checkbox.checked);
            }


            // các sự kiện thay đổi số lượng tính tiền


            function updateQuantityOnChange(cartItemId) {
                const quantityInput = document.getElementById(`quantity-${cartItemId}`);
                const priceElement = document.getElementById(`itemPrice-${cartItemId}`);
                const checkbox = document.querySelector(`.box-checks .item-checkbox[data-cart-id="${cartItemId}"]`);

                if (!quantityInput || !priceElement || !checkbox) {
                    console.error('Element not found.');
                    return;
                }

                let newQuantity = parseInt(quantityInput.value, 10);
                if (isNaN(newQuantity) || newQuantity < 1) {
                    newQuantity = 1;
                    quantityInput.value = newQuantity;
                }

                updateCartItem(cartItemId, newQuantity, priceElement, checkbox);
            }

            function updateCartItem(cartItemId, newQuantity, priceElement, checkbox) {
                const retailPrice = parseFloat(priceElement.getAttribute('data-retail-price'));
                const itemPrice = retailPrice * newQuantity;

                fetch(`/cart/update-quantity/${cartItemId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({quantity: newQuantity})
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            priceElement.innerText = new Intl.NumberFormat('vi-VN').format(itemPrice) + ' đ';
                            updateCartTotals(data.newPrice, data.totalPrice);

                            if (!checkbox.checked) {
                                checkbox.checked = true;
                                updateTotals();
                            }
                        } else {
                            console.error('Error:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });

                function updateCartTotals(newPrice, totalPrice) {
                    const totalPriceElement = document.getElementById('totalPrice');
                    const totalPaymentElement = document.getElementById('totalPayment');

                    if (totalPriceElement && totalPaymentElement) {
                        totalPriceElement.innerText = new Intl.NumberFormat('vi-VN').format(newPrice) + ' đ';
                        totalPaymentElement.innerText = new Intl.NumberFormat('vi-VN').format(totalPrice) + ' đ';
                    }
                }
            }

            function deleteCartItem(cartItemId) {
                fetch(`/cart/remove-item/${cartItemId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Xóa phần tử khỏi giao diện người dùng
                            document.querySelector(`#cart-item-${cartItemId}`).remove();
                            updateCartTotals(data.newPrice, data.totalPrice);
                        } else {
                            console.error('Error:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }

            @if(session('success'))
            Swal.fire({
                title: 'Thành công!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK'
            });
            @endif

            @if($errors->any())
            Swal.fire({
                title: 'Lỗi!',
                text: '{{ $errors->first() }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            @endif

            document.addEventListener('DOMContentLoaded', function () {
                // Xử lý sự kiện submit của form thanh toán
                document.getElementById('paymentForm').addEventListener('submit', function (e) {
                    // Ngăn form gửi dữ liệu ngay lập tức
                    e.preventDefault();

                    // Tìm tất cả các checkbox được chọn
                    const selectedItems = [];
                    document.querySelectorAll('.box-checks .item-checkbox:checked').forEach(checkbox => {
                        selectedItems.push({
                            id: checkbox.getAttribute('data-cart-id'),
                            quantity: document.getElementById('quantity-' + checkbox.getAttribute('data-cart-id')).value
                        });
                    });

                    // Nếu không có sản phẩm nào được chọn, thông báo lỗi và không gửi form
                    if (selectedItems.length === 0) {
                        alert('Vui lòng chọn ít nhất một sản phẩm để thanh toán.');
                        return;
                    }

                    // Chuyển đổi danh sách các sản phẩm được chọn thành JSON
                    document.getElementById('selectedItems').value = JSON.stringify(selectedItems);

                    // Gửi form
                    this.submit();
                });
            });


            // thanh toán
            //     var form = document.getElementById('paymentForm');
            //
            //     form.addEventListener('submit', function (e) {
            //         // Xóa tất cả input ẩn trước khi thêm mới
            //         Array.from(form.querySelectorAll('input[type="hidden"]')).forEach(input => {
            //             if (input.name !== '_token') { // Giữ lại CSRF token
            //                 input.remove();
            //             }
            //         });
            //
            //         var selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
            //         selectedCheckboxes.forEach(function (checkbox) {
            //             var hiddenInput = document.createElement('input');
            //             hiddenInput.type = 'hidden';
            //             hiddenInput.name = checkbox.getAttribute('name');
            //             hiddenInput.value = checkbox.getAttribute('value');
            //             form.appendChild(hiddenInput);
            //         });
            //     });

            document.getElementById('checkout-button').addEventListener('click', function (e) {
                e.preventDefault(); // Ngăn chặn hành động mặc định của nút

                let selectedItems = [];

                // Lấy dữ liệu sản phẩm từ các checkbox đã chọn
                document.querySelectorAll('.item-checkbox:checked').forEach(function (checkbox) {
                    let itemId = checkbox.value;
                    selectedItems.push(itemId);
                });

                // Gửi danh sách sản phẩm đã chọn qua AJAX
                fetch('/cart/save-selected-items', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        selectedItems: selectedItems
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // console.log('heheehe');
                            window.location.href = '/checkout'; // Chuyển hướng tới trang thanh toán
                        } else {
                            console.error('Error:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            });


        });


    </script>

@endsection
