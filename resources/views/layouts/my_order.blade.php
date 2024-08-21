@extends('layouts.profile')
@section('main-profile')

    <div class="container col-sm-9 h-500">
        <ul class="nav nav-tabs mt-3 bg-white" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane"
                   type="button"
                   role="tab" aria-controls="home-tab-pane" aria-selected="true">Tất cả</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane"
                   type="button"
                   role="tab" aria-controls="profile-tab-pane" aria-selected="false">Chờ thanh toán
                    @if(isset($orderCounts['Processing']) && $orderCounts['Processing'] > 0)
                        ({{ $orderCounts['Processing'] }})
                    @endif
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="shipping-tab" data-bs-toggle="tab" data-bs-target="#shipping-tab-pane"
                   type="button"
                   role="tab" aria-controls="shipping-tab-pane" aria-selected="false">Vận chuyển
                    @if(isset($orderCounts['Shipped']) && $orderCounts['Shipped'] > 0)
                        ({{ $orderCounts['Shipped'] }})
                    @endif
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="waiting-delivery-tab" data-bs-toggle="tab"
                   data-bs-target="#waiting-delivery-tab-pane" type="button"
                   role="tab" aria-controls="waiting-delivery-tab-pane" aria-selected="false">Chờ giao hàng
                    @if(isset($orderCounts['waitingDelivery']) && $orderCounts['waitingDelivery'] > 0)
                        ({{ $orderCounts['waitingDelivery'] }})
                    @endif
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-tab-pane"
                   type="button"
                   role="tab" aria-controls="completed-tab-pane" aria-selected="false">Hoàn thành </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="canceled-tab" data-bs-toggle="tab" data-bs-target="#canceled-tab-pane"
                   type="button"
                   role="tab" aria-controls="canceled-tab-pane" aria-selected="false">Đã hủy</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link" id="refund-tab" data-bs-toggle="tab" data-bs-target="#refund-tab-pane" type="button"
                   role="tab" aria-controls="refund-tab-pane" aria-selected="false">Hoàn tiền</a>
            </li>
        </ul>
        <div class="input-group mt-3">
            <input type="text" class="form-control search-input"
                   placeholder="Bạn có thể tìm kiếm theo tên Shop, ID đơn hàng hoặc Tên Sản phẩm">
            <i class="bi bi-search search-icon"></i>
        </div>

        <div class="tab-content" id="myTabContent">
            {{-- Đơn hàng tất cả --}}
            <div class="orders-container mt-3 tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab">
                @if($orders->isEmpty())
                    <div class="bg-white box-myorder_img">
                        <img class="background-image" src="{{ asset('images/icon_my_order.png') }}">
                        <span>Chưa có đơn hàng</span>
                    </div>
                @else
                    @foreach($orders as $order)
                        <div class="order-item bg-white p-3 mb-3">
                            <div class="shop-name d-flex align-items-center mb-3">
                                <!-- Shop SVG and Name -->
                                <svg width="17" height="16" class="me-2" viewBox="0 0 17 16">
                                    <!-- SVG code here -->
                                </svg>
                                <span>{{ $order->shop->name ?? 'Không có tên cửa hàng' }}</span>

                                <a class="btn btn-outline-secondary btn-sm ms-2 text-black text-decoration-none"
                                   href="{{ url('shop', ['id' => $order->shop_id]) }}">Xem Shop</a>
                                <span class="badge ms-auto text-danger float-end stop">{{ $order->status ?? 'Không có trạng thái' }}</span>
                            </div>
                            <hr>
                            @if($order->OrderDetail && $order->OrderDetail->isNotEmpty())
                                @foreach($order->OrderDetail as $detail)
                                    <div class="order-details d-flex mb-3">
                                        <img class="img-thumbnails"
                                             src="{{ asset('storage/'.$detail->product_image ?? 'default-image.jpg') }}"
                                             alt="Product Image">
                                        <div class="d-flex justify-content-between w-100 ms-4">
                                            <div class="w-50">
                                                <p class="m-0">{{ $detail->Product->name ?? 'Không có tên sản phẩm' }}</p>

                                                @if($detail->appProductStock && $detail->appProductStock->productAttribute->isNotEmpty())
                                                    @foreach($detail->appProductStock->productAttribute as $attribute)
                                                        <p class="m-0">
                                                            {{ $attribute->appProductVariation->variation_name ?? 'Không có tên biến thể' }}:
                                                            {{ $attribute->appProductVariationValue->variation_value_name ?? 'Không có giá trị biến thể' }}
                                                        </p>
                                                    @endforeach
                                                @endif

                                                <p class="m-0">Số lượng: {{ $detail->product_quantity ?? '0' }}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center gap-2">
                                                    <p class="text-danger mb-0">{{ number_format($detail->product_price ?? 0, 0, ',', '.') }} VND</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p>Không có thông tin chi tiết cho đơn hàng này.</p>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-end">
                                <div class="order-actions mt-3">
                                    <p class="text-danger fw-bold">
                                        <strong class="text-black money">Thành tiền :</strong>
                                        {{ number_format($order->total_price, 0, ',', '.') }} VND
                                    </p>
                                    <button class="btn btn-secondary" disabled>chờ</button>
                                    <button class="btn btn-light border" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal" onclick="setOrderId({{ $order->id }})">
                                        Huỷ đơn hàng
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Chờ thanh toán --}}
            <div class="tab-pane fade orders-container" id="profile-tab-pane" role="tabpanel"
                 aria-labelledby="profile-tab">
                @if($orderProcessing->isEmpty())
                    <div class="bg-white box-myorder_img">
                        <img class="background-image" src="{{ asset('images/icon_my_order.png')  }}">
                        <span>Chưa có đơn hàng</span>
                    </div>
                @else
                    @foreach($orderProcessing as $order)
                        <div class="order-item bg-white p-3 mb-3">
                            <div class="shop-name d-flex align-items-center mb-3">
                                <!-- Shop SVG and Name -->
                                <svg width="17" height="16" class="me-2" viewBox="0 0 17 16">
                                    <!-- SVG code here -->
                                </svg>
                                <span>{{ $order->shop->name ?? 'Không có tên cửa hàng' }}</span>

                                <a class="btn btn-outline-secondary btn-sm ms-2 text-black text-decoration-none"
                                   href="{{ url('shop', ['id' => $order->shop_id]) }}">Xem Shop</a>
                                <span class="badge ms-auto text-danger float-end stop">{{ $order->status ?? 'Không có trạng thái' }}</span>
                            </div>
                            <hr>
                            @if($order->OrderDetail && $order->OrderDetail->isNotEmpty())
                                @foreach($order->OrderDetail as $detail)
                                    <div class="order-details d-flex mb-3">
                                        <img class="img-thumbnails"
                                             src="{{ asset('storage/'.$detail->product_image ?? 'default-image.jpg') }}"
                                             alt="Product Image">
                                        <div class="d-flex justify-content-between w-100 ms-4">
                                            <div class="w-50">
                                                <p class="m-0">{{ $detail->Product->name ?? 'Không có tên sản phẩm' }}</p>

                                                @if($detail->appProductStock && $detail->appProductStock->productAttribute->isNotEmpty())
                                                    @foreach($detail->appProductStock->productAttribute as $attribute)
                                                        <p class="m-0">
                                                            {{ $attribute->appProductVariation->variation_name ?? 'Không có tên biến thể' }}:
                                                            {{ $attribute->appProductVariationValue->variation_value_name ?? 'Không có giá trị biến thể' }}
                                                        </p>
                                                    @endforeach
                                                @endif

                                                <p class="m-0">Số lượng: {{ $detail->product_quantity ?? '0' }}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center gap-2">
                                                @if(!isset($detail->Product->sale_price))
                                                    <p class="text-danger mb-0">{{ number_format($detail->product_price ?? 0, 0, ',', '.') }} VND</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p>Không có thông tin chi tiết cho đơn hàng này.</p>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-end">
                                <div class="order-actions mt-3">
                                    <p class="text-danger fw-bold">
                                        <strong class="text-black money">Thành tiền :</strong>
                                        {{ number_format($order->total_price, 0, ',', '.') }} VND
                                    </p>
                                    <button class="btn btn-secondary" disabled>chờ</button>
                                    <button class="btn btn-light border" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal" onclick="setOrderId({{ $order->id }})">
                                        Huỷ đơn hàng
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Vận chuyển --}}
            <div class="tab-pane fade orders-container" id="shipping-tab-pane" role="tabpanel"
                 aria-labelledby="shipping-tab">
                @if($Shipped->isEmpty())
                    <div class="bg-white box-myorder_img">
                        <img class="background-image" src="{{ asset('images/icon_my_order.png')  }}">
                        <span>Chưa có đơn hàng</span>
                    </div>
                @else
                    @foreach($Shipped as $order)
                        <div class="order-item bg-white p-3 mb-3">
                            <div class="shop-name d-flex align-items-center mb-3">
                                <!-- Shop SVG and Name -->
                                <svg width="17" height="16" class="me-2" viewBox="0 0 17 16">
                                    <!-- SVG code here -->
                                </svg>
                                <span>{{ $order->shop->name ?? 'Không có tên cửa hàng' }}</span>

                                <a class="btn btn-outline-secondary btn-sm ms-2 text-black text-decoration-none"
                                   href="{{ url('shop', ['id' => $order->shop_id]) }}">Xem Shop</a>
                                <span class="badge ms-auto text-danger float-end stop">{{ $order->status ?? 'Không có trạng thái' }}</span>
                            </div>
                            <hr>
                            @if($order->OrderDetail && $order->OrderDetail->isNotEmpty())
                                @foreach($order->OrderDetail as $detail)
                                    <div class="order-details d-flex mb-3">
                                        <img class="img-thumbnails"
                                             src="{{ asset('storage/'.$detail->product_image ?? 'default-image.jpg') }}"
                                             alt="Product Image">
                                        <div class="d-flex justify-content-between w-100 ms-4">
                                            <div class="w-50">
                                                <p class="m-0">{{ $detail->Product->name ?? 'Không có tên sản phẩm' }}</p>

                                                @if($detail->appProductStock && $detail->appProductStock->productAttribute->isNotEmpty())
                                                    @foreach($detail->appProductStock->productAttribute as $attribute)
                                                        <p class="m-0">
                                                            {{ $attribute->appProductVariation->variation_name ?? 'Không có tên biến thể' }}:
                                                            {{ $attribute->appProductVariationValue->variation_value_name ?? 'Không có giá trị biến thể' }}
                                                        </p>
                                                    @endforeach
                                                @endif

                                                <p class="m-0">Số lượng: {{ $detail->product_quantity ?? '0' }}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center gap-2">
                                                @if(!isset($detail->Product->sale_price))
                                                    <p class="text-danger mb-0">{{ number_format($detail->product_price ?? 0, 0, ',', '.') }} VND</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p>Không có thông tin chi tiết cho đơn hàng này.</p>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-end">
                                <div class="order-actions mt-3">
                                    <p class="text-danger fw-bold">
                                        <strong class="text-black money">Thành tiền :</strong>
                                        {{ number_format($order->total_price, 0, ',', '.') }} VND
                                    </p>
                                    <button class="btn btn-secondary" disabled>chờ</button>
                                    <button class="btn btn-light border" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal" onclick="setOrderId({{ $order->id }})">
                                        Huỷ đơn hàng
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Chờ giao hàng --}}
            <div class="tab-pane fade orders-container" id="waiting-delivery-tab-pane" role="tabpanel"
                 aria-labelledby="waiting-delivery-tab">
                @if($waitingDelivery->isEmpty())
                    <div class="bg-white box-myorder_img">
                        <img class="background-image" src="{{ asset('images/icon_my_order.png')  }}">
                        <span>Chưa có đơn hàng</span>
                    </div>
                @else
                    @foreach($waitingDelivery as $order)
                        <div class="order-item bg-white p-3 mb-3">
                            <div class="shop-name d-flex align-items-center mb-3">
                                <!-- Shop SVG and Name -->
                                <svg width="17" height="16" class="me-2" viewBox="0 0 17 16">
                                    <!-- SVG code here -->
                                </svg>
                                <span>{{ $order->shop->name ?? 'Không có tên cửa hàng' }}</span>

                                <a class="btn btn-outline-secondary btn-sm ms-2 text-black text-decoration-none"
                                   href="{{ url('shop', ['id' => $order->shop_id]) }}">Xem Shop</a>
                                <span class="badge ms-auto text-danger float-end stop">{{ $order->status ?? 'Không có trạng thái' }}</span>
                            </div>
                            <hr>
                            @if($order->OrderDetail && $order->OrderDetail->isNotEmpty())
                                @foreach($order->OrderDetail as $detail)
                                    <div class="order-details d-flex mb-3">
                                        <img class="img-thumbnails"
                                             src="{{ asset('storage/'.$detail->product_image ?? 'default-image.jpg') }}"
                                             alt="Product Image">
                                        <div class="d-flex justify-content-between w-100 ms-4">
                                            <div class="w-50">
                                                <p class="m-0">{{ $detail->Product->name ?? 'Không có tên sản phẩm' }}</p>

                                                @if($detail->appProductStock && $detail->appProductStock->productAttribute->isNotEmpty())
                                                    @foreach($detail->appProductStock->productAttribute as $attribute)
                                                        <p class="m-0">
                                                            {{ $attribute->appProductVariation->variation_name ?? 'Không có tên biến thể' }}:
                                                            {{ $attribute->appProductVariationValue->variation_value_name ?? 'Không có giá trị biến thể' }}
                                                        </p>
                                                    @endforeach
                                                @endif

                                                <p class="m-0">Số lượng: {{ $detail->product_quantity ?? '0' }}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center gap-2">
                                                @if(!isset($detail->Product->sale_price))
                                                    <p class="text-danger mb-0">{{ number_format($detail->product_price ?? 0, 0, ',', '.') }} VND</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p>Không có thông tin chi tiết cho đơn hàng này.</p>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-end">
                                <div class="order-actions mt-3">
                                    <p class="text-danger fw-bold">
                                        <strong class="text-black money">Thành tiền :</strong>
                                        {{ number_format($order->total_price, 0, ',', '.') }} VND
                                    </p>
                                    <button class="btn btn-secondary" disabled>chờ</button>
                                    <button class="btn btn-light border" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal" onclick="setOrderId({{ $order->id }})">
                                        Huỷ đơn hàng
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>

            {{-- Hoàn thành --}}
            <div class="tab-pane fade orders-container" id="completed-tab-pane" role="tabpanel" aria-labelledby="completed-tab">
                @if($Delivered->isEmpty())
                    <div class="bg-white box-myorder_img">
                        <img class="background-image" src="{{ asset('images/icon_my_order.png')  }}">
                        <span>Chưa có đơn hàng</span>
                    </div>
                @else
                    @foreach($Delivered as $order)
                        <div class="order-item bg-white p-3 mb-3">
                            <div class="shop-name d-flex align-items-center mb-3">
                                <!-- Shop SVG and Name -->
                                <svg width="17" height="16" class="me-2" viewBox="0 0 17 16">
                                    <!-- SVG code here -->
                                </svg>
                                <span>{{ $order->shop->name ?? 'Không có tên cửa hàng' }}</span>

                                <a class="btn btn-outline-secondary btn-sm ms-2 text-black text-decoration-none"
                                   href="{{ url('shop', ['id' => $order->shop_id]) }}">Xem Shop</a>
                                <span class="badge ms-auto text-danger float-end stop">{{ $order->status ?? 'Không có trạng thái' }}</span>
                            </div>
                            <hr>
                            @if($order->OrderDetail && $order->OrderDetail->isNotEmpty())
                                @foreach($order->OrderDetail as $detail)
                                    <div class="order-details d-flex mb-3">
                                        <img class="img-thumbnails"
                                             src="{{ asset('storage/'.$detail->product_image ?? 'default-image.jpg') }}"
                                             alt="Product Image">
                                        <div class="d-flex justify-content-between w-100 ms-4">
                                            <div class="w-50">
                                                <p class="m-0">{{ $detail->Product->name ?? 'Không có tên sản phẩm' }}</p>

                                                @if($detail->appProductStock && $detail->appProductStock->productAttribute->isNotEmpty())
                                                    @foreach($detail->appProductStock->productAttribute as $attribute)
                                                        <p class="m-0">
                                                            {{ $attribute->appProductVariation->variation_name ?? 'Không có tên biến thể' }}:
                                                            {{ $attribute->appProductVariationValue->variation_value_name ?? 'Không có giá trị biến thể' }}
                                                        </p>
                                                    @endforeach
                                                @endif

                                                <p class="m-0">Số lượng: {{ $detail->product_quantity ?? '0' }}</p>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center gap-2">
                                                @if(!isset($detail->Product->sale_price))
                                                    <p class="text-danger mb-0">{{ number_format($detail->product_price ?? 0, 0, ',', '.') }} VND</p>
                                                @else
                                                    <p class="text-secondary text-decoration-line-through mb-0">{{ number_format($detail->Product->regular_price ?? 0, 0, ',', '.') }} VND</p>
                                                    @isset($detail->Product->sale_price)
                                                        <p class="text-danger mb-0">{{ number_format((float)$detail->Product->sale_price, 0, ',', '.') }} VND</p>
                                                    @endisset
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p>Không có thông tin chi tiết cho đơn hàng này.</p>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-end">
                                <div class="order-actions mt-3">
                                    <p class="text-danger fw-bold">
                                        <strong class="text-black money">Thành tiền :</strong>
                                        {{ number_format($order->total_price, 0, ',', '.') }} VND
                                    </p>
                                    <button class="btn btn-secondary" disabled>chờ</button>
                                    <button class="btn btn-light border" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal" onclick="setOrderId({{ $order->id }})">
                                        Huỷ đơn hàng
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

        {{-- Đã hủy --}}
        <div class="tab-pane fade orders-container" id="canceled-tab-pane" role="tabpanel" aria-labelledby="canceled-tab">
            @if($canceled->isEmpty())
                <div class="bg-white box-myorder_img">
                    <img class="background-image" src="{{ asset('images/icon_my_order.png')  }}">
                    <span>Chưa có đơn hàng</span>
                </div>
            @else
                @foreach($canceled as $order)
                    <div class="order-item bg-white p-3 mb-3">
                        <div class="shop-name d-flex align-items-center mb-3">
                            <!-- Shop SVG and Name -->
                            <svg width="17" height="16" class="me-2" viewBox="0 0 17 16">
                                <!-- SVG code here -->
                            </svg>
                            <span>{{ $order->shop->name ?? 'Không có tên cửa hàng' }}</span>

                            <a class="btn btn-outline-secondary btn-sm ms-2 text-black text-decoration-none"
                               href="{{ url('shop', ['id' => $order->shop_id]) }}">Xem Shop</a>
                            <span class="badge ms-auto text-danger float-end stop">{{ $order->status ?? 'Không có trạng thái' }}</span>
                        </div>
                        <hr>
                        @if($order->OrderDetail && $order->OrderDetail->isNotEmpty())
                            @foreach($order->OrderDetail as $detail)
                                <div class="order-details d-flex mb-3">
                                    <img class="img-thumbnails"
                                         src="{{ asset('storage/'.$detail->product_image ?? 'default-image.jpg') }}"
                                         alt="Product Image">
                                    <div class="d-flex justify-content-between w-100 ms-4">
                                        <div class="w-50">
                                            <p class="m-0">{{ $detail->Product->name ?? 'Không có tên sản phẩm' }}</p>

                                            @if($detail->appProductStock && $detail->appProductStock->productAttribute->isNotEmpty())
                                                @foreach($detail->appProductStock->productAttribute as $attribute)
                                                    <p class="m-0">
                                                        {{ $attribute->appProductVariation->variation_name ?? 'Không có tên biến thể' }}:
                                                        {{ $attribute->appProductVariationValue->variation_value_name ?? 'Không có giá trị biến thể' }}
                                                    </p>
                                                @endforeach
                                            @endif

                                            <p class="m-0">Số lượng: {{ $detail->product_quantity ?? '0' }}</p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            @if(!isset($detail->Product->sale_price))
                                                <p class="text-danger mb-0">{{ number_format($detail->product_price ?? 0, 0, ',', '.') }} VND</p>
                                            @else
                                                <p class="text-secondary text-decoration-line-through mb-0">{{ number_format($detail->Product->regular_price ?? 0, 0, ',', '.') }} VND</p>
                                                @isset($detail->Product->sale_price)
                                                    <p class="text-danger mb-0">{{ number_format((float)$detail->Product->sale_price, 0, ',', '.') }} VND</p>
                                                @endisset
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>Không có thông tin chi tiết cho đơn hàng này.</p>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-end">
                            <div class="order-actions mt-3">
                                <p class="text-danger fw-bold">
                                    <strong class="text-black money">Thành tiền :</strong>
                                    {{ number_format($order->total_price, 0, ',', '.') }} VND
                                </p>
                                <button class="btn btn-secondary" disabled>chờ</button>
                                <button class="btn btn-light border" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal" onclick="setOrderId({{ $order->id }})">
                                    Huỷ đơn hàng
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Hoàn tiền --}}
        <div class="tab-pane fade orders-container" id="refund-tab-pane" role="tabpanel" aria-labelledby="refund-tab">
            @if($canceled->isEmpty())
                <div class="bg-white box-myorder_img">
                    <img class="background-image" src="{{ asset('images/icon_my_order.png')  }}">
                    <span>Bạn hiện không có yêu cầu Trả hàng/Hoàn tiền nào</span>
                </div>
            @else
            @endif
        </div>
    </div>
    </div>
    <div style="height: 100px;"></div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="cancelOrderForm" action="" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Lý do huỷ</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body sm-4">
                        <!-- chọn lý do huỷ đơn -->
                        @foreach($CancelledReasons as $cancelReason)
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="cancelReason"
                                       id="cancelReason{{ $loop->index }}" value="{{ $cancelReason->value }}">
                                <label class="form-check-label" for="cancelReason{{ $loop->index }}">
                                    {{ $cancelReason->value }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Không phải bây giờ
                        </button>
                        <button type="submit" class="btn btn-danger">Huỷ Đơn Hàng</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function setOrderId(orderId) {
            const form = document.getElementById('cancelOrderForm');
            form.action = `/orders/cancel/${orderId}`;
        }
    </script>

@endsection
