<div id="productColumn" class="product-column">
    @foreach ($products as $product)
        <div class="product-card mb-3 product-item"
             data-category="{{ $product->category_id }}"
             data-brand="{{ $product->brand_id }}"
             data-price="{{ $product->price }}">
            <div class="row">
                <div class="col-12 col-lg-3">
                    <div class="product-img">
                        @if ($product->main_image)
                            <img src="{{ asset('storage/' . $product->main_image) }}" alt="Product Image">
                        @else
                            <img src="https://thudaumot.binhduong.gov.vn/Portals/0/images/default.jpg"
                                 alt="Default Image">
                        @endif
                    </div>
                </div>
                <div class="col-12 col-lg-9 product-info">
                    <div class="product-info">
                        @if($product->sale_price != 0)
                            <div class="sale-off fw-bolder">
                                -{{ round(100 - ($product->sale_price * 100 / $product->regular_price)) }}%
                            </div>
                        @endif
                        <div class="product-title-item">
                            <p class="product-title">{{ $product->name }}</p>
                        </div>
                        <div class="price-item">
                            <div class="product-price">
                                {{ $product->formattedDisplayedPrice ? $product->formattedDisplayedPrice : 'Price not available' }}
                                @if($product->sale_price)
                                    <div class="product-price-discounted">
                                        {{ $product->formattedRegularPrice ? $product->formattedRegularPrice : '' }}
                                    </div>
                                @endif
                            </div>
                            <div class="info">
                                <div class="rating1">
                                        <?php
                                        $fullStars = floor($product->rating);
                                        $halfStar = ($product->rating - $fullStars) >= 0.1 ? 1 : 0;
                                        ?>
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $fullStars)
                                            <i class="bi bi-star-fill" style="color: gold;"></i>
                                        @elseif ($i == $fullStars + 1 && $halfStar)
                                            <i class="bi bi-star-half" style="color: gold;"></i>
                                        @else
                                            <i class="bi bi-star" style="color: gold;"></i>
                                        @endif
                                    @endfor
                                </div>
                                <div class="number-star">
                                    {{ number_format($product->rating, 1) }}
                                </div>
                                <div class="order">
                                    @if($product->sold_count > 0)
                                        {{ $product->sold_count }} lượt bán
                                    @else
                                        Chưa có lượt bán
                                    @endif
                                </div>
                                <div class="shipping">
                                    Miễn phí giao hàng
                                </div>
                            </div>
                        </div>
                        <p class="product-description">{{ $product->description }} </p>
                        <div class="view-detail">
                            <a href="{{ route('product.detail', ['id' => $product->id]) }}">Xem chi tiết</a>
                        </div>
                        <div class="product-favorite">
                            @if(Auth::check())
                                <a onclick="insertWishlist({{ $product->id }}, '{{ addslashes($product->name) }}')"
                                   id="wishlist-{{ $product->id }}">
                                    <i class="{{ in_array($product->id, $wishlistItems) ? 'fas fa-heart' : 'far fa-heart' }}"></i>
                                </a>
                            @else
                                <a onclick="insertWishlist({{ $product->id }}, '{{ addslashes($product->name) }}')">
                                    <i class="far fa-heart"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
