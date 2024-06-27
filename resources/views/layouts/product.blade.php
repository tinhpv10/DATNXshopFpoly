@extends('index')
@section('main')
    <div class="main">
        <div class="container pb-5">
            <nav aria-label="breadcrumb" class="breadcrumb-nav">
                <div class="container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="#">Cửa hàng</a></li>
                    </ol>
                </div>
            </nav>
            <div class="container-fluid">
                <div class="row">

                    <div class="col-md-3 sidebar">
                        <div class="accordion accordion-flush" id="accordionFlush">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#flush-collapseOne" aria-expanded="false"
                                            aria-controls="flush-collapseOne">
                                        Danh mục sản phẩm
                                    </button>
                                </h2>
                                <div id="flush-collapseOne" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionFlush1">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            @php
                                                $displayedCategories = 0;
                                            @endphp
                                            @foreach($Categories as $category)
                                                <li class="category-item" data-category="{{ $category->id }}"
                                                    style="{{ $displayedCategories < 5 ? '' : 'display:none;' }}">
                                                    <a href="#" class="category-link">{{ $category->name }}</a>
                                                </li>
                                                @php
                                                    $displayedCategories++;
                                                @endphp
                                            @endforeach
                                        </ul>
                                        <button class="seeAllButton" id="seeAllButtonCategories">Xem Tất Cả</button>
                                        <button class="seeAllButton" id="backToTopCategories">Quay lại</button>
                                    </div>
                                </div>

                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#flush-collapseTwo" aria-expanded="false"
                                            aria-controls="flush-collapseTwo">
                                        Thương hiệu
                                    </button>
                                </h2>
                                <div id="flush-collapseTwo" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionFlush2">
                                    <div class="accordion-body">
                                        @foreach($Brands as $Brand)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                       id="samsungCheckbox">
                                                <label class="form-check-label" for="samsungCheckbox">
                                                    {{ $Brand->name }}
                                                </label>
                                            </div>
                                            <div class="form-check"
                                                 style="{{ $loop->index < 5 ? '' : 'display:none;' }}">
                                                <input class="form-check-input brand-checkbox" type="checkbox"
                                                       value="{{ $Brand->id }}" id="brand{{ $Brand->id }}">
                                                <label class="form-check-label" for="brand{{ $Brand->id }}">
                                                    {{ $Brand->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                        <button class="seeAllButton" id="seeAllButtonBrands">Xem Tất Cả</button>
                                        <button class="seeAllButton" id="backToTopBrands">Quay lại</button>

                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#flush-collapsePrice" aria-expanded="false"
                                            aria-controls="flush-collapsePrice">
                                        Giá
                                    </button>
                                </h2>
                                <div id="flush-collapsePrice" class="accordion-collapse collapse"
                                     data-bs-parent="#accordioncollapsePrice">
                                    <div class="accordion-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                   id="metallicCheckbox">
                                            <label class="form-check-label" for="metallicCheckbox">
                                                Metallic
                                            </label>
                                        <div class="range-slider">
                                            <label for="customRange2"></label><input type="range" class="form-range"
                                                                                     min="0" max="1000" value="0"
                                                                                     step="1"
                                                                                     id="customRange2">
                                        </div>
                                        <div class="range-inputs">
                                            <label for="minRangeInput"></label><input type="number"
                                                                                      class="form-control small-input"
                                                                                      id="minRangeInput"
                                                                                      placeholder="0">
                                            <label for="maxRangeInput"></label><input type="number"
                                                                                      class="form-control small-input"
                                                                                      id="maxRangeInput"
                                                                                      placeholder="1000">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                       id="plasticCheckbox">
                                                <label class="form-check-label" for="plasticCheckbox">
                                                    Plastic cover
                                                </label>
                                        </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                       id="ramCheckbox">
                                                <label class="form-check-label" for="ramCheckbox">
                                                    8GB Ram
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                       id="powerCheckbox">
                                                <label class="form-check-label" for="powerCheckbox">
                                                    Super power
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                       id="memoryCheckbox">
                                                <label class="form-check-label" for="memoryCheckbox">
                                                    Large Memory
                                                </label>
                                            </div>
                                            <button class="seeAllButton" id="seeAllButtonFeatures">See All</button>
                                        <button id="applyButton" class="apply-button">Apply</button>
                                    </div>
                                </div>

                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#flush-collapseFeatures" aria-expanded="false"
                                            aria-controls="flush-collapseFeatures">
                                        Đặt trưng
                                    </button>
                                </h2>
                                <div id="flush-collapseFeatures" class="accordion-collapse collapse"
                                     data-bs-parent="#accordionFlushFeatures">
                                    <div class="accordion-body">
                                        @foreach($productVariations as $productVariation)
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value=""
                                                       id="metallicCheckbox">
                                                <label class="form-check-label" for="metallicCheckbox">
                                                    {{ $productVariation->variation_name }}
                                                </label>
                                            </div>
                                        @endforeach
                                        <button class="seeAllButton" id="seeAllButtonFeatures">See All</button>

                                            <div class="range-slider">
                                                <input type="range" class="form-range" min="0" max="5" value="0"
                                                       step="1"
                                                       id="customRange2">
                                            </div>
                                            <div class="range-inputs">
                                                <input type="number" class="form-control small-input" id="minRangeInput"
                                                       placeholder="0">
                                                <input type="number" class="form-control small-input" id="maxRangeInput"
                                                       placeholder="999999999 ">
                                            </div>
                                            <button class="apply-button">Apply</button>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#flush-collapseCondition" aria-expanded="false"
                                            aria-controls="flush-collapseCondition">
                                        Tình trạng
                                    </button>
                                </h2>
                                <div id="flush-collapseCondition" class="accordion-collapse collapse"
                                     data-bs-parent="#accordioncollapseCondition">
                                    <div class="accordion-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault"
                                                   id="flexRadioDefault1">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                                Any
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault"
                                                   id="flexRadioDefault2" checked>
                                            <label class="form-check-label" for="flexRadioDefault2">
                                                Refurbished
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault"
                                                   id="flexRadioDefault3">
                                            <label class="form-check-label" for="flexRadioDefault3">
                                                Brand new
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault"
                                                   id="flexRadioDefault4">
                                            <label class="form-check-label" for="flexRadioDefault4">
                                                Old items
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#flush-collapseRating" aria-expanded="false"
                                            aria-controls="flush-collapseRating">
                                        Xếp hạng
                                    </button>
                                </h2>
                                <div id="flush-collapseRating" class="accordion-collapse collapse"
                                     data-bs-parent="#accordioncollapseRating">
                                    <div class="accordion-body">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="5" id="star5">
                                            <label class="form-check-label" for="star5">
                                                <i class="fa fa-star star"></i>
                                                <i class="fa fa-star star"></i>
                                                <i class="fa fa-star star"></i>
                                                <i class="fa fa-star star"></i>
                                                <i class="fa fa-star star"></i>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="4" id="star4">
                                            <label class="form-check-label" for="star4">
                                                <i class="fa fa-star star"></i>
                                                <i class="fa fa-star star"></i>
                                                <i class="fa fa-star star"></i>
                                                <i class="fa fa-star star"></i>
                                                <i class="fa fa-star star-gray"></i>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="3" id="star3">
                                            <label class="form-check-label" for="star3">
                                                <i class="fa fa-star star"></i>
                                                <i class="fa fa-star star"></i>
                                                <i class="fa fa-star star"></i>
                                                <i class="fa fa-star star-gray"></i>
                                                <i class="fa fa-star star-gray"></i>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="2" id="star2">
                                            <label class="form-check-label" for="star2">
                                                <i class="fa fa-star star"></i>
                                                <i class="fa fa-star star"></i>
                                                <i class="fa fa-star star-gray"></i>
                                                <i class="fa fa-star star-gray"></i>
                                                <i class="fa fa-star star-gray"></i>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="1" id="star1">
                                            <label class="form-check-label" for="star1">
                                                <i class="fa fa-star star"></i>
                                                <i class="fa fa-star star-gray"></i>
                                                <i class="fa fa-star star-gray"></i>
                                                <i class="fa fa-star star-gray"></i>
                                                <i class="fa fa-star star-gray"></i>
                                            </label>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-9 content">
                        <div class="content-top">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center"> 12,911 items in&nbsp; <strong> Mobile
                                            accessory</strong></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" value=""
                                                   id="verifiedCheckbox">
                                            <label class="form-check-label" for="verifiedCheckbox">
                                                Chọn
                                            </label>
                                        </div>
                                        <div class="form-group me-3">
                                            <select class="form-select" id="featuredSelect">
                                                <option>Đặt trưng</option>
                                                <option>Không đặt trưng</option>
                                            </select>
                                        </div>
                                        <div class="btn-group me-3 switchView">
                                            <div id="gridButton" onclick="switchView('grid')"><i
                                                    class="bi bi-grid-3x3-gap-fill"></i></div>
                                            <div id="columnButton" onclick="switchView('column')"><i
                                                    class="bi bi-list"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="filters-applied">
                            <button class="seeAllButton" id="Clearallfilter">Xóa tất cả bộ lọc</button>
                        </div>
                        <div class="product">

                            <div id="productGrid" class="product-grid">
                                <div class="row">
                                    @foreach ($products as $product)
                                        <div class="col-md-4 pb-4 product-item"
                                             data-category="{{ $product->category_id }}"
                                             data-brand="{{ $product->brand_id }}"
                                             data-price="{{ $product->price }}">
                                            <div class="product-card h-100">
                                                <div class="product-img">
                                                    <img src="{{ asset('storage/' . $product->main_image) }}"
                                                         alt="Product Image">
                                                </div>
                                                <div class="product-info">
                                                    <div class="product-price-item">
                                                        <div class="price-item">
                                                            <div
                                                                class="product-price">{{ $product->regular_price ? number_format($product->regular_price, 2) : 'Price not available' }}
                                                                <div
                                                                    class="product-price-discounted">{{ $product->sale_price ? number_format($product->sale_price, 2) : '' }}</div>
                                                            </div>
                                                            <div class="rating">
                                                                <div class="star">
                                                                    <i class="bi bi-star-fill"></i>
                                                                    <i class="bi bi-star-fill"></i>
                                                                    <i class="bi bi-star-fill"></i>
                                                                    <i class="bi bi-star-fill"></i>
                                                                    <i class="bi bi-star-fill"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="product-favorite d-flex align-items-center">
                                                            <a href=""><i class="far fa-heart"></i></a>
                                                        </div>

                                                    </div>
                                                    <p class="product-title">{{ $product->name }}</p>
                                                    <p class="product-description">{{ $product->description }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4 mb-4 ">
                                            <a href="{{ route('product.detail', ['id' => $product->id]) }}"
                                               class="product-link text-decoration-none text-black">
                                                <div class="product-card">
                                                    <div class="product-img">
                                                        <img src="{{ asset('storage/' . $product->main_image) }}"
                                                             alt="Product Image">
                                                    </div>
                                                    <div class="product-info">
                                                        <div class="product-price-item">
                                                            <div class="price-item">
                                                                <div
                                                                    class="product-price">{{ $product->formattedRegularPrice }}
                                                                    đ
                                                                    @if($product->sale_price)
                                                                        <div
                                                                            class="product-price-discounted">{{ $product->formattedSalePrice }}
                                                                            đ
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="rating">
                                                                    <div class="star">
                                                                        <i class="bi bi-star-fill"></i>
                                                                        <i class="bi bi-star-fill"></i>
                                                                        <i class="bi bi-star-fill"></i>
                                                                        <i class="bi bi-star-fill"></i>
                                                                        <i class="bi bi-star-fill"></i>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="product-favorite d-flex align-items-center">
                                                                <a href="{{ route('product.detail', ['id' => $product->id]) }}"><i
                                                                        class="far fa-heart"></i></a>
                                                            </div>
                                                        </div>
                                                        <p class="product-title">{{ $product->name }}</p>
                                                        <p class="product-title">{{ $product->description }}</p>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>

                                    @endforeach
                                </div>
                            </div>

                            <div id="productColumn" class="product-column">
                                @foreach ($products as $product)
                                    <div class="product-card mb-3">
                                        <div class="row">
                                            <div class="col-12 col-lg-3">
                                                <div class="product-img"><a href="index.html"><img
                                                            src="{{ asset('storage/' . $product->main_image) }}"
                                                            alt="Product 1 Image"></a>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-9">
                                                <div class="product-info">
                                                    <div class="product-title-item">
                                                        <p class="product-title">{{ $product->name }}</p>
                                                        <div class="product-favorite d-flex align-items-center">
                                                            <a href=""><i class="far fa-heart"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="price-item">
                                                        <div class="product-price">{{ $product->formattedRegularPrice }}
                                                            đ
                                                            @if($product->sale_price)
                                                                <div
                                                                    class="product-price-discounted">{{ $product->formattedSalePrice }}
                                                                    đ
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="rating">
                                                            <div class="star">
                                                                <i class="bi bi-star-fill"></i>
                                                                <i class="bi bi-star-fill"></i>
                                                                <i class="bi bi-star-fill"></i>
                                                                <i class="bi bi-star-fill"></i>
                                                                <i class="bi bi-star-fill"></i>
                                                            </div>
                                                            <div class="number-star">
                                                                7.5
                                                            </div>
                                                            <div class="order">
                                                                145 Orders
                                                            </div>
                                                            <div class="shipping">
                                                                Free Ship
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p class="product-description">{{ $product->description }} </p>
                                                    <div class="view-detail">
                                                        <a href="">View Detail</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="product-card mb-3 product-item"
                                         data-category="{{ $product->category_id }}"
                                         data-brand="{{ $product->brand_id }}"
                                         data-price="{{ $product->price }}">
                                        <div class="row">
                                            <div class="col-12 col-lg-3">
                                                <div class="product-img"><a href="checkout.blade.php"><img
                                                            src="{{ asset('storage/' . $product->main_image) }}"
                                                            alt="Product 1 Image"></a>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-9">
                                                <div class="product-info">
                                                    <div class="product-title-item">
                                                        <p class="product-title">{{ $product->name }}</p>
                                                        <div class="product-favorite d-flex align-items-center">
                                                            <a href=""><i class="far fa-heart"></i></a>
                                                        </div>
                                                    </div>
                                                    <div class="price-item">
                                                        <div class="product-price">{{ $product->regular_price }}
                                                            <div
                                                                class="product-price-discounted">{{ $product->sale_price }}</div>
                                                        </div>
                                                        <div class="rating">
                                                            <div class="star">
                                                                <i class="bi bi-star-fill"></i>
                                                                <i class="bi bi-star-fill"></i>
                                                                <i class="bi bi-star-fill"></i>
                                                                <i class="bi bi-star-fill"></i>
                                                                <i class="bi bi-star-fill"></i>
                                                            </div>
                                                            <div class="number-star">
                                                                7.5
                                                            </div>
                                                            <div class="order">
                                                                145 Orders
                                                            </div>
                                                            <div class="shipping">
                                                                Free Ship
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p class="product-description">{{ $product->description }} </p>
                                                    <div class="view-detail">
                                                        <a href="">View Detail</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                            </div>

                            <div id="noProductMessage" class="no-product-message" style="display: none;">

                                <p><i class="fas fa-exclamation-circle"></i>Không có sản phẩm nào</p>
                            </div>
                            <div class="pagination-menu">
                                <div class="dropdown">
                                    <button class="btn dropdown-toggle" type="button" id="showDropdown2"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                        Show {{ $itemsPerPage }}
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="showDropdown">
                                        <li><a class="dropdown-item" href="#" data-value="9">Show 9</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="12">Show 12</a></li>
                                        <li><a class="dropdown-item" href="#" data-value="15">Show 15</a></li>
                                    </ul>
                                </div>
                                <nav aria-label="Page navigation example pb-4">
                                    <ul class="pagination">
                                        <li class="page-item {{ $products->currentPage() == 1 ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $products->previousPageUrl() }}"
                                               aria-label="Previous">
                                                <span aria-hidden="true">&laquo;</span>
                                            </a>
                                        </li>
                                        @php
                                            $startPage = max(1, $products->currentPage() - 1);
                                            $endPage = min($startPage + 2, $products->lastPage());
                                        @endphp
                                        @for ($i = $startPage; $i <= $endPage; $i++)
                                            <li class="page-item {{ $i == $products->currentPage() ? 'active' : '' }}">
                                                <a class="page-link" href="{{ $products->url($i) }}">{{ $i }}</a>
                                            </li>
                                        @endfor

                                        <li class="page-item {{ $products->currentPage() == $products->lastPage() ? 'disabled' : '' }}">
                                            <a class="page-link" href="{{ $products->nextPageUrl() }}"
                                               aria-label="Next">
                                                <span aria-hidden="true">&raquo;</span>
                                            </a>
                                        </li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
            <script>
                window.addEventListener('DOMContentLoaded', function () {
                    var gridButton = document.getElementById('gridButton');
                    var columnButton = document.getElementById('columnButton');
                    var productGrid = document.getElementById('productGrid');
                    var productColumn = document.getElementById('productColumn');

                    gridButton.disabled = true;
                    columnButton.disabled = false;
                    productGrid.style.display = 'block';
                    productColumn.style.display = 'none';

                    gridButton.addEventListener('click', function () {
                        gridButton.disabled = true;
                        columnButton.disabled = false;
                        productGrid.style.display = 'block';
                        productColumn.style.display = 'none';
                    });

                    columnButton.addEventListener('click', function () {
                        gridButton.disabled = false;
                        columnButton.disabled = true;
                        productGrid.style.display = 'none';
                        productColumn.style.display = 'block';
                    });
                });

                // Lắng nghe sự kiện click cho mỗi sản phẩm
                document.querySelectorAll('.product-link').forEach(item => {
                    item.addEventListener('click', event => {
                        // Chuyển hướng người dùng đến trang chi tiết tương ứng
                        window.location.href = item.getAttribute('href');
                    });
                });
            </script>
@endsection
{{--update--}}
