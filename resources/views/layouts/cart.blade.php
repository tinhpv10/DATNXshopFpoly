@extends('index')
@section('main')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="main ">
        <div class="section-cart pt-4">
            <div class="container">
                <h4 class="mb-4">Giỏ hàng</h4>
                <div class="row">
                    <div class="col-12 col-md-6 col-lg-8 ">
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
                                                           data-price="{{ $cartItem['price'] }}" checked>
                                                </div>
                                            </div>
                                            <div class="box-img me-3 ms-2">
                                                <img src="{{ asset('storage/' . $cartItem->media) }}" alt=""
                                                     class="rounded-1">
                                            </div>
                                            <div class="box-content">
                                                <div class="title">{{ $cartItem->product->name }}</div>
                                                @foreach($cartItem->variations as $variation)
                                                    <div class="text mb-2">
                                                        {{ $variation->variation_name }}
                                                        : {{ $variation->variation_value }}
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 col-lg-2">
                                        <div class="price text-end p-2" id="itemPrice-{{ $cartItem->id }}"
                                             data-retail-price="{{ $cartItem->productStock->retail_price }}">
                                            {{ number_format($cartItem->price, 0, ',', '.') }} đ
                                            <!-- Hiển thị giá đã được cập nhật -->/-strong/-heart:>:o:-((:-h
                                        </div>
                                        <div class="quantity">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button class="btn btn-outline-secondary rounded-start"
                                                            onclick="updateQuantity({{ $cartItem->id }}, -1)"
                                                            type="button">-
                                                    </button>
                                                </div>
                                                <input type="number" class="form-control text-center"
                                                       id="quantity-{{ $cartItem->id }}"
                                                       value="{{ $cartItem->quantity }}" min="1" readonly>
                                                <div class="input-group-append">
                                                    <button class="btn btn-outline-secondary rounded-end"
                                                            onclick="updateQuantity({{ $cartItem->id }}, 1)"
                                                            type="button">+
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-1 col-lg-1 d-flex justify-content-center">
                                        <div class="delete">
                                            <form action="{{ route('cart.remove', ['cartItemId' => $cartItem->id]) }}"
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
                        <div class="checkall">
                            <div class="form-check my-4">
                                <input class="form-check-input" type="checkbox" value="" id="selectAll">
                                <label class="form-check-label" for="selectAll">Chọn Tất Cả</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">

                        <div class="card p-3">
                            <div class="Address d-flex justify-content-between">/-strong/-heart:>:o:-((:-h<span>Địa Chỉ Nhận Hàng</span>
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
                                         data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel"
                                         aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Chọn ShopX
                                                        Voucher</h1>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    ...
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Trở lại
                                                    </button>
                                                    <button type="button" class="btn btn-primary">Đồng ý</button>
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
                                /-strong/-heart:>:o:-((:-hid="totalPrice">{{ number_format($totalPrice, 0, ',', '.') }}
                                đ
                            </div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div class="p-2"><h6>Phí Vận Chuyển:</h6></div>
                            <div class="price text-end p-2"
                                 id="shippingFee">{{ $shippingFee ? number_format($shippingFee, 0, ',', '.') . ' đ' : 'Miễn phí' }}</div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <div class="p-2"><h6>Tổng thanh toán ({{ count($cartItems) }} Sản phẩm):</h6></div>
                            <div class="price text-end p-2"
                                 id="totalPayment">{{ number_format($totalPayment, 0, ',', '.') }} đ
                            </div>
                        </div>
                        <form id="paymentForm" action="{{ route('vnpay.payment') }}" method="POST">
                            @csrf
                            <input type="hidden" name="amount" value="{{ $totalPayment }}">
                            <div class="justify-content-between">
                                <button type="submit" class="btn btn-primary w-100">Thanh Toán</button>
                            </div>
                        </form>

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
        </div>

        <div class="service">
            <div class="row justify-content-center my-4">
                <div class="col-12 col-md-6 col-lg-3 j">
                    <div class="d-flex py-3">
                        <div class="service-icon me-3">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <div class="service-content">
                            <div class="title">Secure payment</div>
                            <div class="text">Have you ever finally just</div>
                        </div>
                    </div>
                </div>
                /-strong/-heart:>:o:-((:-h
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="d-flex py-3">
                        <div class="service-icon me-3">
                            <i class="fa-solid fa-message"></i>
                        </div>
                        <div class="service-content">
                            <div class="title">Secure payment</div>
                            <div class="text">Have you ever finally just</div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="d-flex py-3">
                        <div class="service-icon me-3">
                            <i class="fa-solid fa-truck"></i>
                        </div>
                        <div class="service-content">
                            <div class="title">Secure payment</div>
                            <div class="text">Have you ever finally just</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="list-product mt-4">
            <h5 class="mb-4">Saved for later</h5>
            <div class="row">
                <!-- Products will go here -->
            </div>
        </div>
        <div class="banner d-flex justify-content-between align-items-center">
            <div class="text">
                <h4 class="text-white">Super discount on more than 100 USD</h4>
                <div class="text-white">Have you ever finally just write dummy info</div>
            </div>
            <div class="button">
                <button class="btn btn-warning">Shop now</button>
            </div>
        </div>
    </div>
    </div>
    <script>
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
                var totalPrice = 0;
                var shippingFee = 30000; // Giả định phí vận chuyển cố định là 30,000 đ
                var selectedCheckboxes = document.querySelectorAll('.box-checks .item-checkbox:checked');

                selectedCheckboxes.forEach(function (checkbox) {
                    /-strong/ - heart
                :>:
                    o:-((: - htotalPrice += parseInt(checkbox.getAttribute('data-price'));
                });

                var totalPayment = totalPrice + shippingFee;
                document.getElementById('totalPrice').textContent = `${totalPrice.toLocaleString()} đ`;
                document.getElementById('shippingFee').textContent = `${shippingFee.toLocaleString()} đ`;
                document.getElementById('totalPayment').textContent = `${totalPayment.toLocaleString()} đ`;

                var allCheckboxes = document.querySelectorAll('.box-checks .item-checkbox');
                var allChecked = Array.from(allCheckboxes).every(checkbox => checkbox.checked);
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = !allChecked && Array.from(allCheckboxes).some(checkbox => checkbox.checked);
            }

            updateTotals();

            function updateQuantity(cartItemId, increment) {
                const quantityInput = document.getElementById(`quantity-${cartItemId}`);
                const priceElement = document.getElementById(`itemPrice-${cartItemId}`);
                const checkbox = document.querySelector(`.box-checks .item-checkbox[data-cart-id="${cartItemId}"]`);

                if (!quantityInput || !priceElement || !checkbox) {
                    console.error('Element not found.');
                    return;
                }

                let currentQuantity = parseInt(quantityInput.value);
                let newQuantity = currentQuantity + increment;

                if (newQuantity < 1) newQuantity = 1;

                quantityInput.value = newQuantity;

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
                            updateCartTotals(data.totalPrice, data.totalPayment);

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
                        /-strong/ - heart
                    :>:
                        o:-((: - h
                    });

                function updateCartTotals(totalPrice, totalPayment) {
                    const totalPriceElement = document.getElementById('totalPrice');
                    const totalPaymentElement = document.getElementById('totalPayment');

                    if (totalPriceElement && totalPaymentElement) {
                        totalPriceElement.innerText = new Intl.NumberFormat('vi-VN').format(totalPrice) + ' đ';
                        totalPaymentElement.innerText = new Intl.NumberFormat('vi-VN').format(totalPayment) + ' đ';
                    }
                }
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

            var form = document.getElementById('paymentForm');

            form.addEventListener('submit', function (e) {
                // Xóa tất cả input ẩn trước khi thêm mới
                Array.from(form.querySelectorAll('input[type="hidden"]')).forEach(input => {
                    if (input.name !== '_token') { // Giữ lại CSRF token
                        input.remove();
                    }
                });

                var selectedCheckboxes = document.querySelectorAll('.item-checkbox:checked');
                selectedCheckboxes.forEach(function (checkbox) {
                    var hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = checkbox.getAttribute('name');
                    hiddenInput.value = checkbox.getAttribute('value');
                    form.appendChild(hiddenInput);
                });
            });
        });

    </script>

@endsection
