@extends('index')
@section('main')
    {{--    banner--}}
    @include('includes.banner')
    {{--Main body--}}
    <div class="main">
        <div class="container">
            <!-- section sale -->
            <div class="section-sale">
                <div class="d-grid sale-group">
                    <div class="group-left">
                        <div data-animation="fadeInLeft" data-delay="0.8s" id="countdown">
                            <div class="card border-0">
                                <div class="box-content">
                                    <h5 class="card-title mb-0">
                                        Sản phẩm khuyến mãi
                                    </h5>
                                    <div class="card-text">
                                        Chốt deal nhanh chóng
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="group-right">
                        <div class=" autoplay">
                            @foreach($products as $productss)
                                @php
                                    $regularPrice = $productss->regular_price;
                                    $salePrice = $productss->sale_price;

                                    if ($regularPrice > 0 && $regularPrice > $salePrice) {
                                        $discountPercentage = (($regularPrice - $salePrice) / $regularPrice) * 100;
                                        $discountPercentage = round($discountPercentage);
                                    } else {
                                        $discountPercentage = 0;
                                    }
                                @endphp
                                <div class="sale-item text-center">
                                    <a href="{{ route('product.detail', ['id' => $productss->id]) }}"
                                       class="product-link text-decoration-none text-black">
                                        <div class="box-img d-flex justify-content-center">
                                            @if ($productss->main_image)
                                                <img src="{{ asset('storage/' . $productss->main_image) }}"
                                                     alt="Product Image">
                                            @else
                                                <img
                                                    src="https://thudaumot.binhduong.gov.vn/Portals/0/images/default.jpg"
                                                    alt="Default Image">
                                            @endif
                                        </div>
                                        <div class="box-info ">
                                            <div class="title-product">{{$productss->name}}</div>
                                            <span class="badge  rounded-pill text-danger bg-danger-subtle">-{{ $discountPercentage }}%</span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>


            <!-- Block-items-group -->
            <div class="block-group card">
                <div class="d-grid group">
                    <div class="group-item-left">
                        <img src="{{ asset('image/image92.png') }}" alt="hình ảnh">
                        <div class="title">
                            <h2>Danh mục</h2>
                            <button class="btn bg-white  ">Source now</button>
                        </div>
                    </div>
                    <div class="autoplay-block ">
                        @foreach($categoryBanners as $categoriess)
                            <a href="{{ route('products.category', ['category' => $categoriess->id]) }}" class="product-link text-decoration-none text-black">
                            <div class="group-item p-2">
                                <div class="content-item">
                                    <h6>{{ $categoriess->name }}</h6>
                                </div>
                                <div class="d-flex image-item justify-content-end">
                                    <img src="{{ asset('storage/' . $categoriess->image) }}"
                                         alt="{{ $categoriess->name }}" class="img-fluid w-50">
                                </div>
                            </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Section recommended -->
            <div class="section-recommend">
                <h4 class="mb-3">Đề xuất</h4>
                <div class="list-product d-flex flex-wrap justify-content-between">
                    @foreach($recommendedProducts as $product)
                        <div class="product-item border rounded-2">
                            <a href="{{ route('product.detail', ['id' => $product->id]) }}"
                               class="product-link text-decoration-none text-black">
                                <div class="box-img">
                                    @if ($product->main_image)
                                        <img src="{{ asset('storage/' . $product->main_image) }}" alt="Product Image">
                                    @else
                                        <img src="https://thudaumot.binhduong.gov.vn/Portals/0/images/default.jpg"
                                             alt="Default Image">
                                    @endif
                                </div>
                                <div class="info-product">
                                    <div class="price-sale-container">
                                        <div class="salePrice">
                                            {{ $product->formattedSalePrice }} VND
                                        </div>
                                        <div class="product-price">
                                            {{ $product->formattedRegularPrice }} VND
                                        </div>
                                    </div>
                                    <div class="title-name fs-5">{{ $product->name }}</div>
                                    <div class="title-product">{{ $product->description }}</div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>


            </div>

        </div>

        <div class="section-subscribe d-flex align-items-center flex-wrap flex-column">
            <div class="subscribe-text mb-3 text-center">
                <a href="{{ route('product.index')}}" class="btn-xem-them">Xem thêm sản phẩm</a>
            </div>
        </div>

    </div>
@endsection
{{--update--}}
