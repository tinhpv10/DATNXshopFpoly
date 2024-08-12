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
            <div class="orders-container mt-3 tab-pane fade show active" id="home-tab-pane" role="tabpanel"
                 aria-labelledby="home-tab">
                @if($orders->isEmpty())
                    <div class="bg-white box-myorder_img">
                        <img class="background-image" src="{{ asset('images/icon_my_order.png')  }}">
                        <span>Chưa có đơn hàng</span>
                    </div>
                @else
                    @foreach($orders as $order)
                        <div class="order-item bg-white p-3 mb-3">
                            <div class="shop-name d-flex align-items-center mb-3">
                                <svg width="17" height="16" class="me-2" viewBox="0 0 17 16"><title>Shop Icon</title>
                                    <path
                                        d="M1.95 6.6c.156.804.7 1.867 1.357 1.867.654 0 1.43 0 1.43-.933h.932s0 .933 1.155.933c1.176 0 1.15-.933 1.15-.933h.984s-.027.933 1.148.933c1.157 0 1.15-.933 1.15-.933h.94s0 .933 1.43.933c1.368 0 1.356-1.867 1.356-1.867H1.95zm11.49-4.666H3.493L2.248 5.667h12.437L13.44 1.934zM2.853 14.066h11.22l-.01-4.782c-.148.02-.295.042-.465.042-.7 0-1.436-.324-1.866-.86-.376.53-.88.86-1.622.86-.667 0-1.255-.417-1.64-.86-.39.443-.976.86-1.643.86-.74 0-1.246-.33-1.623-.86-.43.536-1.195.86-1.895.86-.152 0-.297-.02-.436-.05l-.018 4.79zM14.996 12.2v.933L14.984 15H1.94l-.002-1.867V8.84C1.355 8.306 1.003 7.456 1 6.6L2.87 1h11.193l1.866 5.6c0 .943-.225 1.876-.934 2.39v3.21z"
                                        stroke-width=".3" stroke="#333" fill="#333" fill-rule="evenodd"></path>
                                </svg>
                                <span>{{ $order->shop->name ?? ''}}</span>
                                <a class="btn btn-outline-secondary btn-sm ms-2 text-black text-decoration-none"
                                   href="{{ route('shop',['id' => $order->shop_id]) }}">Xem Shop</a>
                                <span class="badge ms-auto text-danger float-end stop">{{ $order->status }}</span>
                            </div>
                            <hr>
                            @foreach($order->OrderDetail as $detail)
                                <div class="order-details d-flex mb-3">
                                    <img class="img-thumbnails"
                                         src="{{ asset('storage/'.$detail->Product->mainMedia->media ?? '') }}"
                                         alt="Product Image">
                                    <div class="d-flex justify-content-between w-100 ms-4">
                                        <div class="w-50">
                                            <p class="m-0">{{ $detail->Product->name ?? ''}}</p>
                                            @foreach($detail->Product->productVariation as $variation)
                                                <p class="m-0">{{ $variation->variation_name ?? '' }}</p>
                                            @endforeach
                                            <p class="m-0">{{ $detail->product_quantity ?? ''}}</p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center gap-2">

                                            @if(!isset($detail->Product->sale_price))
                                                <p class="text-danger mb-0 mb-0">{{ number_format($detail->Product->regular_price ?? '') }}
                                                    VND</p>
                                            @else
                                                <p class="text-secondary text-decoration-line-through mb-0">{{ number_format($detail->Product->regular_price ?? '') }}
                                                    VND</p>
                                                @isset($detail->Product->sale_price)
                                                    <p class="text-danger mb-0">{{ number_format((float)$detail->Product->sale_price) }}
                                                        VND</p>
                                                @endisset
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <hr>
                            <div class="d-flex justify-content-end">
                                <div class="order-actions mt-3">
                                    <p class="text-danger fw-bold">
                                        <strong class="text-black money">Thành tiền
                                            :</strong> {{ number_format($order->OrderDetail->sum('product_price')) }}VND
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
                    @foreach($orderProcessing as $processing)
                        <div class="order-item bg-white p-3 mb-3">
                            <div class="shop-name d-flex align-items-center mb-3">
                                <svg width="17" height="16" class="me-2" viewBox="0 0 17 16"><title>Shop Icon</title>
                                    <path
                                        d="M1.95 6.6c.156.804.7 1.867 1.357 1.867.654 0 1.43 0 1.43-.933h.932s0 .933 1.155.933c1.176 0 1.15-.933 1.15-.933h.984s-.027.933 1.148.933c1.157 0 1.15-.933 1.15-.933h.94s0 .933 1.43.933c1.368 0 1.356-1.867 1.356-1.867H1.95zm11.49-4.666H3.493L2.248 5.667h12.437L13.44 1.934zM2.853 14.066h11.22l-.01-4.782c-.148.02-.295.042-.465.042-.7 0-1.436-.324-1.866-.86-.376.53-.88.86-1.622.86-.667 0-1.255-.417-1.64-.86-.39.443-.976.86-1.643.86-.74 0-1.246-.33-1.623-.86-.43.536-1.195.86-1.895.86-.152 0-.297-.02-.436-.05l-.018 4.79zM14.996 12.2v.933L14.984 15H1.94l-.002-1.867V8.84C1.355 8.306 1.003 7.456 1 6.6L2.87 1h11.193l1.866 5.6c0 .943-.225 1.876-.934 2.39v3.21z"
                                        stroke-width=".3" stroke="#333" fill="#333" fill-rule="evenodd"></path>
                                </svg>
                                <span>{{ $processing->shop->name ?? ''}}</span>
                                <a class="btn btn-outline-secondary btn-sm ms-2 text-black text-decoration-none"
                                   href="{{ route('shop',['id' => $processing->shop_id]) }}">Xem Shop</a>
                                <span class="badge ms-auto text-danger float-end stop">{{ $processing->status }}</span>
                            </div>
                            <hr>
                            @foreach($processing->OrderDetail as $detail)
                                <div class="order-details d-flex mb-3">
                                    <img class="img-thumbnails"
                                         src="{{ asset('storage/'.$detail->Product->mainMedia->media ?? '') }}"
                                         alt="Product Image">
                                    <div class="d-flex justify-content-between w-100 ms-4">
                                        <div class="w-50">
                                            <p class="m-0">{{ $detail->Product->name ?? ''}}</p>
                                            @foreach($detail->Product->productVariation as $variation)
                                                <p class="m-0">{{ $variation->variation_name ?? '' }}</p>
                                            @endforeach
                                            <p class="m-0">{{ $detail->product_quantity ?? ''}}</p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            @if(!isset($detail->Product->sale_price))
                                                <p class="text-danger mb-0 mb-0">{{ number_format($detail->Product->regular_price ?? '') }}
                                                    VND</p>
                                            @else
                                                <p class="text-secondary text-decoration-line-through mb-0">{{ number_format($detail->Product->regular_price ?? '') }}
                                                    VND</p>
                                                @isset($detail->Product->sale_price)
                                                    <p class="text-danger mb-0">{{ number_format((float)$detail->Product->sale_price) }}
                                                        VND</p>
                                                @endisset
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <hr>
                            <div class="d-flex justify-content-end">
                                <div class="order-actions mt-3">
                                    <p class="text-danger fw-bold">
                                        <strong class="text-black money">Thành tiền
                                            :</strong> {{ number_format($processing->OrderDetail->sum('product_price')) }}
                                        VND
                                    </p>
                                    <button class="btn btn-secondary" disabled>chờ</button>
                                    <button class="btn btn-light border" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal" onclick="setOrderId({{ $processing->id }})">
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
                    @foreach($Shipped as $Shippeds)
                        <div class="order-item bg-white p-3 mb-3">
                            <div class="shop-name d-flex align-items-center mb-3">
                                <svg width="17" height="16" class="me-2" viewBox="0 0 17 16"><title>Shop Icon</title>
                                    <path
                                        d="M1.95 6.6c.156.804.7 1.867 1.357 1.867.654 0 1.43 0 1.43-.933h.932s0 .933 1.155.933c1.176 0 1.15-.933 1.15-.933h.984s-.027.933 1.148.933c1.157 0 1.15-.933 1.15-.933h.94s0 .933 1.43.933c1.368 0 1.356-1.867 1.356-1.867H1.95zm11.49-4.666H3.493L2.248 5.667h12.437L13.44 1.934zM2.853 14.066h11.22l-.01-4.782c-.148.02-.295.042-.465.042-.7 0-1.436-.324-1.866-.86-.376.53-.88.86-1.622.86-.667 0-1.255-.417-1.64-.86-.39.443-.976.86-1.643.86-.74 0-1.246-.33-1.623-.86-.43.536-1.195.86-1.895.86-.152 0-.297-.02-.436-.05l-.018 4.79zM14.996 12.2v.933L14.984 15H1.94l-.002-1.867V8.84C1.355 8.306 1.003 7.456 1 6.6L2.87 1h11.193l1.866 5.6c0 .943-.225 1.876-.934 2.39v3.21z"
                                        stroke-width=".3" stroke="#333" fill="#333" fill-rule="evenodd"></path>
                                </svg>
                                <span>{{ $Shippeds->shop->name ?? ''}}</span>
                                <a class="btn btn-outline-secondary btn-sm ms-2 text-black text-decoration-none"
                                   href="{{ route('shop',['id' => $Shippeds->shop_id]) }}">Xem Shop</a>
                                <span class="badge ms-auto text-danger float-end stop">{{ $Shippeds->status }}</span>
                            </div>
                            <hr>
                            @foreach($Shippeds->OrderDetail as $detail)
                                <div class="order-details d-flex mb-3">
                                    <img class="img-thumbnails"
                                         src="{{ asset('storage/'.$detail->Product->mainMedia->media ?? '') }}"
                                         alt="Product Image">
                                    <div class="d-flex justify-content-between w-100 ms-4">
                                        <div class="w-50">
                                            <p class="m-0">{{ $detail->Product->name ?? ''}}</p>
                                            @foreach($detail->Product->productVariation as $variation)
                                                <p class="m-0">{{ $variation->variation_name ?? '' }}</p>
                                            @endforeach
                                            <p class="m-0">{{ $detail->product_quantity ?? ''}}</p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            @if(!isset($detail->Product->sale_price))
                                                <p class="text-danger mb-0 mb-0">{{ number_format($detail->Product->regular_price ?? '') }}
                                                    VND</p>
                                            @else
                                                <p class="text-secondary text-decoration-line-through mb-0">{{ number_format($detail->Product->regular_price ?? '') }}
                                                    VND</p>
                                                @isset($detail->Product->sale_price)
                                                    <p class="text-danger mb-0">{{ number_format((float)$detail->Product->sale_price) }}
                                                        VND</p>
                                                @endisset
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <hr>
                            <div class="d-flex justify-content-end">
                                <div class="order-actions mt-3">
                                    <p class="text-danger fw-bold">
                                        <strong class="text-black money">Thành tiền
                                            :</strong> {{ number_format($Shippeds->OrderDetail->sum('product_price')) }}
                                        VND
                                    </p>
                                    <button class="btn btn-secondary" disabled>chờ</button>
                                    <button class="btn btn-light border" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal" onclick="setOrderId({{ $Shippeds->id }})">
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
                                <svg width="17" height="16" class="me-2" viewBox="0 0 17 16"><title>Shop Icon</title>
                                    <path
                                        d="M1.95 6.6c.156.804.7 1.867 1.357 1.867.654 0 1.43 0 1.43-.933h.932s0 .933 1.155.933c1.176 0 1.15-.933 1.15-.933h.984s-.027.933 1.148.933c1.157 0 1.15-.933 1.15-.933h.94s0 .933 1.43.933c1.368 0 1.356-1.867 1.356-1.867H1.95zm11.49-4.666H3.493L2.248 5.667h12.437L13.44 1.934zM2.853 14.066h11.22l-.01-4.782c-.148.02-.295.042-.465.042-.7 0-1.436-.324-1.866-.86-.376.53-.88.86-1.622.86-.667 0-1.255-.417-1.64-.86-.39.443-.976.86-1.643.86-.74 0-1.246-.33-1.623-.86-.43.536-1.195.86-1.895.86-.152 0-.297-.02-.436-.05l-.018 4.79zM14.996 12.2v.933L14.984 15H1.94l-.002-1.867V8.84C1.355 8.306 1.003 7.456 1 6.6L2.87 1h11.193l1.866 5.6c0 .943-.225 1.876-.934 2.39v3.21z"
                                        stroke-width=".3" stroke="#333" fill="#333" fill-rule="evenodd"></path>
                                </svg>
                                <span>{{ $order->shop->name ?? ''}}</span>
                                <a class="btn btn-outline-secondary btn-sm ms-2 text-black text-decoration-none"
                                   href="{{ route('shop',['id' => $order->shop_id]) }}">Xem Shop</a>
                                <span class="badge ms-auto text-danger float-end stop">{{ $order->status }}</span>
                            </div>
                            <hr>
                            @foreach($order->OrderDetail as $detail)
                                <div class="order-details d-flex mb-3">
                                    <img class="img-thumbnails"
                                         src="{{ asset('storage/'.$detail->Product->mainMedia->media ?? '') }}"
                                         alt="Product Image">
                                    <div class="d-flex justify-content-between w-100 ms-4">
                                        <div class="w-50">
                                            <p class="m-0">{{ $detail->Product->name ?? ''}}</p>
                                            @foreach($detail->Product->productVariation as $variation)
                                                <p class="m-0">{{ $variation->variation_name ?? '' }}</p>
                                            @endforeach
                                            <p class="m-0">{{ $detail->product_quantity ?? ''}}</p>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center gap-2">
                                            @if(!isset($detail->Product->sale_price))
                                                <p class="text-danger mb-0 mb-0">{{ number_format($detail->Product->regular_price ?? '') }}
                                                    VND</p>
                                            @else
                                                <p class="text-secondary text-decoration-line-through mb-0">{{ number_format($detail->Product->regular_price ?? '') }}
                                                    VND</p>
                                                @isset($detail->Product->sale_price)
                                                    <p class="text-danger mb-0">{{ number_format((float)$detail->Product->sale_price) }}
                                                        VND</p>
                                                @endisset
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <hr>
                            <div class="d-flex justify-content-end">
                                <div class="order-actions mt-3">
                                    <p class="text-danger fw-bold">
                                        <strong class="text-black money">Thành tiền
                                            :</strong> {{ number_format($order->OrderDetail->sum('product_price')) }}
                                        VND
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
                            <svg width="17" height="16" class="me-2" viewBox="0 0 17 16"><title>Shop Icon</title>
                                <path
                                    d="M1.95 6.6c.156.804.7 1.867 1.357 1.867.654 0 1.43 0 1.43-.933h.932s0 .933 1.155.933c1.176 0 1.15-.933 1.15-.933h.984s-.027.933 1.148.933c1.157 0 1.15-.933 1.15-.933h.94s0 .933 1.43.933c1.368 0 1.356-1.867 1.356-1.867H1.95zm11.49-4.666H3.493L2.248 5.667h12.437L13.44 1.934zM2.853 14.066h11.22l-.01-4.782c-.148.02-.295.042-.465.042-.7 0-1.436-.324-1.866-.86-.376.53-.88.86-1.622.86-.667 0-1.255-.417-1.64-.86-.39.443-.976.86-1.643.86-.74 0-1.246-.33-1.623-.86-.43.536-1.195.86-1.895.86-.152 0-.297-.02-.436-.05l-.018 4.79zM14.996 12.2v.933L14.984 15H1.94l-.002-1.867V8.84C1.355 8.306 1.003 7.456 1 6.6L2.87 1h11.193l1.866 5.6c0 .943-.225 1.876-.934 2.39v3.21z"
                                    stroke-width=".3" stroke="#333" fill="#333" fill-rule="evenodd"></path>
                            </svg>
                            <span>{{ $order->shop->name ?? ''}}</span>
                            <a class="btn btn-outline-secondary btn-sm ms-2 text-black text-decoration-none"
                               href="{{ route('shop',['id' => $order->shop_id]) }}">Xem Shop</a>
                            <span class="badge ms-auto text-danger float-end stop">{{ $order->status }}</span>
                        </div>
                        <hr>
                        @foreach($order->OrderDetail as $detail)
                            <div class="order-details d-flex mb-3">
                                <img class="img-thumbnails"
                                     src="{{ asset('storage/'.$detail->Product->mainMedia->media ?? '') }}"
                                     alt="Product Image">
                                <div class="d-flex justify-content-between w-100 ms-4">
                                    <div class="w-50">
                                        <p class="m-0">{{ $detail->Product->name ?? ''}}</p>
                                        @foreach($detail->Product->productVariation as $variation)
                                            <p class="m-0">{{ $variation->variation_name ?? '' }}</p>
                                        @endforeach
                                        <p class="m-0">{{ $detail->product_quantity ?? ''}}</p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center gap-2">
                                        @if(!isset($detail->Product->sale_price))
                                            <p class="text-danger mb-0 mb-0">{{ number_format($detail->Product->regular_price ?? '') }}
                                                VND</p>
                                        @else
                                            <p class="text-secondary text-decoration-line-through mb-0">{{ number_format($detail->Product->regular_price ?? '') }}
                                                VND</p>
                                            @isset($detail->Product->sale_price)
                                                <p class="text-danger mb-0">{{ number_format((float)$detail->Product->sale_price) }}
                                                    VND</p>
                                            @endisset
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <hr>
                        <div class="d-flex justify-content-end">
                            <div class="order-actions mt-3">
                                <p class="text-danger fw-bold">
                                    <strong class="text-black money">Thành tiền
                                        :</strong> {{ number_format($order->OrderDetail->sum('product_price')) }} VND
                                </p>
                                <button class="btn btn-primary">Mua lại</button>
                                <a class="btn btn-light border"
                                   href="{{ route('canceledOrder', ['id' => $order->id]) }} ">
                                    Xem Chi Tiết Huỷ Đơn
                                </a>
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
