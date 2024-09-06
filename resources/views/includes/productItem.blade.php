<div class="box-product-item border rounded-2 mb-3">
    @if($productItem->sale_price != 0)
        <div class="sale-off fw-bolder">
            -{{ round(100 - ($productItem->sale_price * 100 / $productItem->regular_price))  }}%
        </div>
    @endif
    <div class="item-img">
        <a href="{{ route('product.detail', ['id' => $productItem->id]) }}">
            <img
                src="{{ $productItem->main_image !== null ? asset('storage/'.$productItem->main_image )  : 'https://thudaumot.binhduong.gov.vn/Portals/0/images/default.jpg' }}"
                alt="">
        </a>
    </div>
    <div class="item-info">
        <div class="row-box">
            <div class="item-price">
                <div class="price">
                    <span class="fw-bold">{{ $productItem->sale_price == 0 ? number_format($productItem->regular_price, 0, ',', '.') : number_format($productItem->sale_price, 0, ',', '.') }} VNĐ</span>
                    <span
                        class="price-discounted ms-1 text-body-tertiary text-decoration-line-through">{{ $productItem->sale_price != 0 ? number_format($productItem->regular_price, 0, ',', '.').' VNĐ' : '' }}</span>
                </div>
                <div class="mb-2 text-warning">
                    @for ($i = 1; $i <= 5; $i++)
                        <i class="bi bi-star{{ $i <= $productItem->rating ? '-fill' : '' }}"></i>
                    @endfor
                </div>
            </div>
            <div class="item-favorite border rounded">
                @if(Auth::check())
                    <a onclick="insertWishlist({{ $productItem->id }}, '{{ addslashes($productItem->name) }}')"
                       id="wishlist-{{ $productItem->id }}"><i
                            class="{{ in_array($productItem->id, $wishlistItems) ? 'fas fa-heart' : 'far fa-heart' }}"></i></a>
                @else
                    <a onclick="insertWishlist({{ $productItem->id }}, '{{ addslashes($productItem->name) }}')"><i
                            class="far fa-heart"></i></a>
                @endif
            </div>
        </div>
        <div class="item-title">
            <a href="{{ route('product.detail', ['id' => $productItem->id]) }}"
               class="text-black text-decoration-none">{{ $productItem->name }}</a>
        </div>
    </div>
</div>
