@extends('index')
@section('main')
    <div class="main">
        <div class="container">
            <nav aria-label="breadcrumb" class="breadcrumb-nav">
                <div class="container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="">Cửa hàng</a></li>
                    </ol>
                </div>
            </nav>
        </div>

        <div class="container-lg">
            <div class="section-information bg-white">
                <div class="shop-avatar rounded-circle">
                    <img src="{{ asset('storage/' . $informationShop->avatar) }}" alt="">
                </div>
                <div class="shop-info me-5">
                    <div class="shop-name">{{ $informationShop->name }}</div>
                    <div class="shop-more text-body-tertiary">
                        <div class="me-3 d-inline">
                            <i class="bi bi-people-fill"></i>
                            Người theo dõi: {{ $informationShop->follower }}
                        </div>
                        |
                        <div class="mx-3 d-inline">
                            <i class="bi bi-box-fill"></i>
                            Sản phẩm: {{ $informationShop->products_count }}
                        </div>
                    </div>
                </div>
                <div class="shop-follow">
                    <button class="btn btn-primary" data-shop-id="{{ $informationShop->id }}"
                            onclick="toggleFollow(this)">{{ Auth::user()->followerShop->contains($informationShop->id) ? 'Đang theo dõi' : 'Theo dõi' }}
                    </button>
                </div>
            </div>


            <div class="shop-all">
                <div class="row">
                    <div class="col-3" id="categoryShop">
                        <div class="category-all">
                            <h5 class="title-category">Danh mục sản phẩm</h5>
                            <ul class="list-group rounded-0">
                                @foreach($categoryShop as $item)
                                    <li class="list-group-item list-group-custom {{ request()->routeIs('shop.category') && request('categoryId') == $item->id ? 'active' : ''}}">
                                        <a href="{{ route('shop.category', ['shopId' => $informationShop->id, 'categoryId' => $item->id]) }}"
                                           class="text-decoration-none">{{ $item->name }}</a>
                                    </li>
                                @endforeach
                                    <li class="list-group-item list-group-custom {{ request()->routeIs('shop') ? 'active' : ''}}">
                                        <a href="{{ route('shop', ['id' => $informationShop->id]) }}"
                                           class="text-decoration-none">Tất cả sản phẩm</a>
                                    </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-9">
                        <div class="row g-3 align-items-center justify-content-md-end justify-content-sm-between mb-3"
                             id="filterMobile">
                            <div class="col-auto d-block d-md-none">
                                <a class="" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button"
                                   aria-controls="offcanvasExample">
                                    <i class="bi bi-list-task fs-2 text-black"></i>
                                </a>
                                <div class="offcanvas offcanvas-start offcanvas-custom" tabindex="-1"
                                     id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
                                    <div class="offcanvas-header">
                                        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Danh mục sản phẩm</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                                aria-label="Close"></button>
                                    </div>
                                    <div class="offcanvas-body">
                                        <ul class="list-group rounded-0">
                                            @foreach($categoryShop as $item)
                                                <li class="list-group-item list-group-custom {{ request()->routeIs('shop.category') && request('categoryId') == $item->id ? 'active' : ''}}">
                                                    <a href="{{ route('shop.category', ['shopId' => $informationShop->id, 'categoryId' => $item->id]) }}"
                                                       class="text-decoration-none text-black">{{ $item->name }}</a>
                                                </li>
                                            @endforeach
                                            <li class="list-group-item list-group-custom {{ request()->routeIs('shop') ? 'active' : ''}}">
                                                <a href="{{ route('shop', ['id' => $informationShop->id]) }}"
                                                   class="text-decoration-none text-black">Tất cả sản phẩm</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <select class="form-select select-price" name="orderPrice" id="orderPrice">
                                    <option value="default" {{ $orderBy === 'default' ? 'selected' : '' }}>Giá</option>
                                    <option value="price_desc" {{ $orderBy === 'price_desc' ? 'selected' : '' }}>Giá từ
                                        cao đến thấp
                                    </option>
                                    <option value="price_asc" {{ $orderBy === 'price_asc' ? 'selected' : '' }}>Giá từ
                                        thấp đến cao
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row product-container">
                            @foreach($products as $productItem)
                                <div class="col-sm-6 col-lg-4">
                                    @include('includes.productItem')
                                </div>
                            @endforeach

                            {{ $products->withQueryString()->links() }}
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
    {{--    --}}

    {{--    --}}
    <form id="formFilter" method="GET">
        <input type="hidden" name="page" id="page" value="{{ $page }}">
        <input type="hidden" name="orderBy" id="orderBy" value="{{ $orderBy }}">
    </form>
@endsection
@push('script')
    <script>
        $('#orderPrice').on('change', function () {
            $('#orderBy').val($('#orderPrice option:selected').val());
            $('#formFilter').submit();
        })

        function toggleFollow(button) {
            var shop_id = button.getAttribute('data-shop-id');
            $.ajax({
                type: 'POST',
                url: '/shop-follow',
                data: {
                    shop_id: shop_id,
                },
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                success: function (data) {
                    if (data.status === 200) {
                        if (button.textContent.trim() === 'Theo dõi') {
                            button.textContent = 'Đang theo dõi';
                        } else {
                            button.textContent = 'Theo dõi';
                        }

                    } else {
                        Swal.fire({
                            icon: "warning",
                            text: "Vui lòng đăng nhập để thực hiện hành động!",
                            footer: 'Bạn đã có tài khoản? <a href="/login" class="text-decoration-none fw-semibold text-primary">Đăng nhập</a>'
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Toast.fire({
                        icon: "error",
                        title: 'Đã có lỗi xảy ra!',
                    });
                    console.error('Lỗi insertWishlist: ' + xhr.status + ' - ' + xhr.statusText);
                },
            })
        }


    </script>

@endpush

