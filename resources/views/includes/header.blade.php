<header>
    <div class="header-wrapper">
        <div class="logo"><img class="mobile-menu" src="." alt="menu"><a aria-current="page" class="active"
                                                                         href="{{ url('/') }}"><img width="100%"
                                                                                                        height="46px"
                                                                                                        src="{{ asset('image/logo-doanvth-pro 1.png') }}"
                                                                                                        alt="brand"></a>
        </div>
        <form id="search-form" method="GET" action="{{ route('product.index') }}" enctype="multipart/form-data"
              class="search-form">
            <input class="input" type="text" placeholder="Tìm kiếm sản phẩm hoặc shop" id="search-input" name="query"
                   aria-label="Tìm kiếm sản phẩm hoặc shop">

            <input type="file" name="image" accept="image/*" id="image-input" aria-label="Tìm kiếm bằng hình ảnh"
                   style="display: none;">

            <button class="search-button" type="submit" aria-label="Tìm kiếm"><i class="bi bi-search"></i></button>
        </form>


        <div class="icons mt-3">
            <div class="desktop-icons">
                <img width="20px" height="19px"
                     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAATCAYAAACQjC21AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAE8SURBVHgBvZNPToNAFMbfm9Y2Ji44AkfgCPQEujXGWJbEuKSJK2DVpInRjWFJG2NcegTxBhyhR2BnRJjPGVdqhj+txN/ywfzmwfseUwvL5MEd0yhkCQdMFhFnADaLy7N10xluenCTPIUSMjKfElHgn8a9hav7xzkzpdRGLWfB1Xn2uyyMtzBfUBejUWgqC/PbcKkTODsIqaBurB2EnFMnnPUXcm2c4HfA2PQWBr6aHmSjVMUpXvjmLDbmULNKVHygJw6XQQWE/hV1/HXhf9HYYZSm1uHbVHUGCyRtXRPgogLy96Mqjzyv6BSqDXFYiGMC5uozbWonV/v9WlN5d+172x/CZZLaB5jcguiE9kCt6fqDyliLWcvGmLyouk1/Y1txORNK9jyATKMam6Y6hw4NBlxBA6OE5hXaBz2cT0/HciFVJBOhAAAAAElFTkSuQmCC"
                     alt="icon">
                @php
                    use Illuminate\Support\Str;
                @endphp

                @auth
                    <p class="nav-link dropdown-toggle"
                       data-bs-toggle="dropdown">{{ Str::title(Str::limit(Auth::user()->name, 10)) }}</p>
                    <ul class="dropdown-menu user-menu">
                        <li><a class="dropdown-item" style="text-decoration: none" href="{{ url('profile/edit') }}">Xem
                                hồ sơ</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" style="text-decoration: none"
                               href="{{ route('logout') }}">Thoát</a></li>
                    </ul>
                @endauth

                @guest
                    <p class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Tài khoản</p>
                    <ul class="dropdown-menu user-menu">
                        <li><a class="dropdown-item" style="text-decoration: none" href="{{ route('login') }}">Đăng
                                nhập</a></li>
                        <li><a class="dropdown-item" style="text-decoration: none" href="{{ route('register') }}">Đăng
                                ký</a></li>
                    </ul>
                @endguest
            </div>
            <div class="desktop-icons">
                <a href="{{ route('wishlist') }}" id="wishlist-link" style="text-decoration: none">
                    <img width="20px" height="19px"
                         src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAASCAYAAABfJS4tAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAFhSURBVHgBrZQ/TsMwFMa/5/6Ze4QcAY6QE0BHilCVqYoQogMFdWunSoghC6qYgAFYewPSE8ARcoTMbWLjF3ASNU0pIb8ltmP/9Ok92YQcs/mj1UB7SIqOAGWRQgiBxRqr6dh1At5ze/9yIAQuIXGsCB29FBDBz+9hyAzuHl77iJT3s7kIiQmRDJWEh+2E1KDh1aD3nIo5aUu2P0ql+xNGtDrk5IJnTdWa1CBlOlxKHojvOde0HnRf+jlxLWmRdxlxiPoIMzGpJWpCKSwzMZSHmiApvVQ8cs98veTjn+iL8jS6YFdWY0REjv4EqE6whpiaSSoeuyeBrk8XFRrJV5/PsqMgZq7PTz91kbr4I3GTnORsDrG5ietNkHslT5JS7NwMeovCv7JD/IrpZryj5PKwVAL2ZtLSxAY+oOtmY3tDg13SnYkNs/mb1VSSk1tGGpGw842qJDbyBmTyasUQ3m9S5gu/n4bL9wQCCQAAAABJRU5ErkJggg=="
                         alt="icon">
                    <p>Yêu thích <span class="badge rounded-pill text-bg-primary" id="count-wishlist">0</span></p>
                </a>
            </div>
            <div class="desktop-icons">
                <a class="" href="/cart" style="text-decoration: none">
                    <img width="20px" height="19px"
                         src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAVCAYAAABCIB6VAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAGqSURBVHgBrZVPTsJAFMbfG/645QbiDfAG9QSyFWJCN5qyggQwrICFIREXuhASN8UobsET0CPUG9Qb4FJo+5wBGwsdoFB+STuZmZdvvk7ee8VO730MRArMwYdqMVeGA8C4aPp/SqV2T0/DAUDxEmJxOtK5sIIEE0KcwJ4gklHR8ip6C53eqwLExhAdq1rMnzBvVtUuDX6eAREhhJYY2dIqOi2IhlXT8v2AcFTXntuA8Jy9XaPhuZUKL1yDBTtiI6r+OZMF+T8pDIjQr2sX1lZhcCG8KM/7GbCAkTjIHTQ8J47rfsEmYWCjVbdS4U73rcSHNH8sUUGwJxLH7Jzfsqh18+5pUICQOGxq1DXVWitM5HwiMoUAsvwqshDWoZs0+XC6VjgxtZv2USLFnR/DLqD7sTWmqeup2+dBBkIg4kR84JzVhTbvcgmXDXkui2Az/jM9K5dVaRu97w6G4srmQjEsVK5zL95eII/jFGv8iQoys2RSes+izXqiAnKo6d+X9Ar32z9d1/QpZi+v80LZKGxDTOSxtfiTwONNMTcCCbUr1QRyvYozufulnP8FPr+YxTzBgRcAAAAASUVORK5CYII="
                         alt="icon">
                    <p style="text-decoration: none;">Giỏ hàng<span class="number">0</span></p>
                </a>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg border-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">Trang chủ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/product') }}">Sản phẩm</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/post') }}">Tin tức</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/contact') }}">Liên hệ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>
{{--update--}}
