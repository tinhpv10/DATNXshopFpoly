@extends('index')
@section('main')
    <div class="main">
        <div class="container">
            <nav style=""
                 aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 py-3">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href=""
                                                                              class="text-decoration-none">Chi tiết</a>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="container">
            <div class="row border rounded-2 py-3 bg-white">
                <div class="col-12 col-md-4 col-lg-4">
                    <div class="box-left">
                        <div class="box-img border rounded-2">
                            <img src="{{ asset('storage/' . $product->main_image) }}" alt="Product Image"
                                 id="change_image">
                        </div>
                        <div class="img-detail">
                            @foreach ($productMedia as $media)
                                <div class="img-item">
                                    <img src="{{ asset('storage/' . $media->media) }}" alt="img-detail"
                                         onclick="changeImg('{{ asset('storage/' . $media->media) }}')">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-5 col-lg-5">
                    <div class="box-center">
                        <div class="text-success"><i class="bi bi-check2 me-2"></i>Còn hàng</div>
                        <h5>{{ $product->name }}</h5>
                        <div class="rating d-flex">
                            <div class="star text-warning pe-3 me-3 border-end">
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-fill"></i>
                                <i class="bi bi-star-half"></i>
                                <span class="ms-2">66</span>
                            </div>
                            <div class="text-body-tertiary pe-3 me-3 border-end">
                                <i class="bi bi-chat-left-dots"></i> <span class="ms-2">32 reviews</span>
                            </div>
                            <div class="text-body-tertiary ">
                                <i class="bi bi-bag-check"></i> <span class="ms-2">1.8k đã bán</span>
                            </div>
                        </div>
                        <div class="trade-price d-flex bg-warning-subtle  p-3 mt-3 ">
                            <div class="border-end pe-5 me-3">
                                <div class="price fw-bold text-danger">{{ $formattedRegularPrice }} VNĐ</div>
                            </div>
                            <div>
                                <div class="price fw-bold"><s>{{ $formattedSalePrice }} VNĐ</s></div>
                            </div>
                        </div>
                        <div class="short-info mt-3">
                            @foreach($productVariations as $productVariation)
                                <div class="pb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="short-title text-body-tertiary">
                                            {{ $productVariation->variation_name }}:
                                        </div>
                                        <div class="short-content text-secondary ms-2"
                                             id="variation-value-{{ $productVariation->id }}">
                                            @foreach($productVariation->productVariationValue as $variationValue)
                                                {{ $variationValue->variation_value_name }}
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="short-color d-flex flex-wrap mt-2">
                                        <!-- Các nút ví dụ cho màu sắc hoặc các thuộc tính khác có thể ở đây -->
                                        @foreach($productVariation->productVariationValue as $variationValue)
                                            @if($productVariation->variation_name == 'Color')
                                                <button type="button" class="btn btn-outline-secondary me-2"
                                                        style="background-color: {{ $variationValue->color }};"
                                                        title="{{ $variationValue->variation_value_name }}"
                                                        onclick="updateVariationValue({{ $productVariation->id }}, '{{ $variationValue->variation_value_name }}')">
                                                    {{ $variationValue->variation_value_name }}
                                                </button>
                                            @elseif($productVariation->variation_name == 'Size')
                                                <button type="button" class="btn btn-outline-secondary me-2"
                                                        onclick="updateVariationValue({{ $productVariation->id }}, '{{ $variationValue->variation_value_name }}')">
                                                    {{ $variationValue->variation_value_name }}
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-outline-secondary me-2"
                                                        onclick="updateVariationValue({{ $productVariation->id }}, '{{ $variationValue->variation_value_name }}')">
                                                    {{ $variationValue->variation_value_name }}
                                                </button>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                            <script>
                                function updateVariationValue(variationId, variationValueName) {
                                    var variationValueElement = document.getElementById('variation-value-' + variationId);
                                    variationValueElement.textContent = variationValueName;
                                }
                            </script>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-3 col-lg-3">
                    <div class="box-right border rounded-2 p-3">
                        <div class="d-flex">
                            <div class="avatar-img">
                                <img
                                    src="https://didongviet.vn/dchannel/wp-content/uploads/2023/08/hinh-nen-3d-hinh-nen-iphone-dep-3d-didongviet@2x-576x1024.jpg"
                                    alt="">
                            </div>
                            <div class="ms-2">
                                Supplier <br> Guanjoi Trading LLC
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex mb-2">
                            <div class="flags">
                                <img
                                    src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/21/Flag_of_Vietnam.svg/640px-Flag_of_Vietnam.svg.png"
                                    alt="flags-img">
                            </div>
                            <div class="text-body-tertiary ms-2">Việt Nam</div>
                        </div>
                        <div class="d-flex mb-2">
                            <div class="icon text-body-tertiary">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <div class="text-body-tertiary ms-2">Verified Seller</div>
                        </div>
                        <div class="d-flex mb-2">
                            <div class="icon text-body-tertiary">
                                <i class="bi bi-globe"></i>
                            </div>
                            <div class="text-body-tertiary ms-2">Worldwide shipping</div>
                        </div>
                        <div class="d-grid gap-2 mt-4">
                            <form action="{{ route('product.addToCart') }}" method="POST">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <button class="btn btn-primary add-to-cart-btn w-100" type="submit">Thêm vào giỏ hàng
                                </button>
                            </form>
                            <button class="btn btn-light text-primary" type="button">Seller’s profile</button>
                        </div>
                    </div>

                    <div class="text-center text-primary fw-medium mt-5">
                        <i class="bi bi-suit-heart "></i><span class="ms-2">Save for later</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="block-detail">
                <div class="row">
                    <div class="col-12 col-md-8 col-lg-8">
                        <div class="bg-white border rounded-2 px-3">
                            <ul class="nav  nav-underline border-bottom" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                            data-bs-target="#description-tab-pane" type="button" role="tab"
                                            aria-controls="description-tab-pane" aria-selected="true">Description
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link " id="profile-tab" data-bs-toggle="tab"
                                            data-bs-target="#reviews-tab-pane" type="button" role="tab"
                                            aria-controls="reviews-tab-pane" aria-selected="false">Reviews
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link " id="contact-tab" data-bs-toggle="tab"
                                            data-bs-target="#shipping-tab-pane" type="button" role="tab"
                                            aria-controls="shipping-tab-pane" aria-selected="false">Shipping
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link " id="contact-tab" data-bs-toggle="tab"
                                            data-bs-target="#about-tab-pane" type="button" role="tab"
                                            aria-controls="about-tab-pane" aria-selected="false">About seller
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="description-tab-pane" role="tabpanel"
                                     aria-labelledby="home-tab" tabindex="0">
                                    <div class="py-3">
                                        <p>{{ $product->description}}</p>
                                        <div class="table-responsive w-50 my-4">
                                            <table class="table table-bordered">
                                                <tbody>
                                                @foreach($productVariations as $productVariation)
                                                    <tr>
                                                        <td>{{ $productVariation->variation_name }}</td>
                                                        <td>
                                                            <span>
                                                                @if(isset($variationValueNames[$productVariation->id]))
                                                                    {{ $variationValueNames[$productVariation->id] }}
                                                                @else
                                                                    {{--Xử lý trường hợp không tìm thấy giá trị biến thể --}}
                                                                @endif
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="reviews-tab-pane" role="tabpanel"
                                     aria-labelledby="profile-tab" tabindex="0">
                                    <div class="py-3">
                                        tab review
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="shipping-tab-pane" role="tabpanel"
                                     aria-labelledby="contact-tab" tabindex="0">
                                    <div class="py-3">
                                        tab shipping
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="about-tab-pane" role="tabpanel"
                                     aria-labelledby="about-tab" tabindex="0">
                                    <div class="py-3">
                                        tab about
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-4 mb-3">
                        <div class="bg-white rounded-2 border p-3">
                            <div class="fw-bold mb-3">You may like</div>
                            @foreach($favoriteProducts as $favoriteProduct)
                                <div class="border d-flex align-items-center p-2 my-2">
                                    <div class="avatar-img">
                                        <img src="{{ $favoriteProduct->main_image }}" width="50px" height="70px" alt="">
                                    </div>
                                    <div class="ms-3 flex-grow-1 d-flex align-items-center">
                                        <div class="d-flex flex-column">
                                            <div class="fw-bold font-weight-bold">{{ $favoriteProduct->name }}</div>
                                            <div class="price">{{ $favoriteProduct->formattedRegularPrice }}
                                                - {{ $favoriteProduct->formattedSalePrice }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    </div>


    </div>
    <!-- Sign in / Register Modal -->
    <div class="modal fade" id="signin-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="icon-close"></i></span>
                    </button>

                    <div class="form-box">
                        <div class="form-tab">
                            <ul class="nav nav-pills nav-fill nav-border-anim" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="signin-tab" data-toggle="tab" href="#signin"
                                       role="tab" aria-controls="signin" aria-selected="true">Sign In</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab"
                                       aria-controls="register" aria-selected="false">Register</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="tab-content-5">
                                <div class="tab-pane fade show active" id="signin" role="tabpanel"
                                     aria-labelledby="signin-tab">
                                    <form action="#">
                                        <div class="form-group">
                                            <label for="singin-email">Username or email address *</label>
                                            <input type="text" class="form-control" id="singin-email"
                                                   name="singin-email" required>
                                        </div><!-- End .form-group -->

                                        <div class="form-group">
                                            <label for="singin-password">Password *</label>
                                            <input type="password" class="form-control" id="singin-password"
                                                   name="singin-password" required>
                                        </div><!-- End .form-group -->

                                        <div class="form-footer">
                                            <button type="submit" class="btn btn-outline-primary-2">
                                                <span>LOG IN</span>
                                                <i class="icon-long-arrow-right"></i>
                                            </button>

                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input"
                                                       id="signin-remember">
                                                <label class="custom-control-label" for="signin-remember">Remember
                                                    Me</label>
                                            </div><!-- End .custom-checkbox -->

                                            <a href="#" class="forgot-link">Forgot Your Password?</a>
                                        </div><!-- End .form-footer -->
                                    </form>
                                    <div class="form-choice">
                                        <p class="text-center">or sign in with</p>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <a href="#" class="btn btn-login btn-g">
                                                    <i class="icon-google"></i>
                                                    Login With Google
                                                </a>
                                            </div><!-- End .col-6 -->
                                            <div class="col-sm-6">
                                                <a href="#" class="btn btn-login btn-f">
                                                    <i class="icon-facebook-f"></i>
                                                    Login With Facebook
                                                </a>
                                            </div><!-- End .col-6 -->
                                        </div><!-- End .row -->
                                    </div><!-- End .form-choice -->
                                </div><!-- .End .tab-pane -->
                                <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">
                                    <form action="#">
                                        <div class="form-group">
                                            <label for="register-email">Your email address *</label>
                                            <input type="email" class="form-control" id="register-email"
                                                   name="register-email" required>
                                        </div><!-- End .form-group -->

                                        <div class="form-group">
                                            <label for="register-password">Password *</label>
                                            <input type="password" class="form-control" id="register-password"
                                                   name="register-password" required>
                                        </div><!-- End .form-group -->

                                        <div class="form-footer">
                                            <button type="submit" class="btn btn-outline-primary-2">
                                                <span>SIGN UP</span>
                                                <i class="icon-long-arrow-right"></i>
                                            </button>

                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="register-policy"
                                                       required>
                                                <label class="custom-control-label" for="register-policy">I agree to the
                                                    <a href="#">privacy policy</a> *</label>
                                            </div><!-- End .custom-checkbox -->
                                        </div><!-- End .form-footer -->
                                    </form>
                                    <div class="form-choice">
                                        <p class="text-center">or sign in with</p>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <a href="#" class="btn btn-login btn-g">
                                                    <i class="icon-google"></i>
                                                    Login With Google
                                                </a>
                                            </div><!-- End .col-6 -->
                                            <div class="col-sm-6">
                                                <a href="#" class="btn btn-login  btn-f">
                                                    <i class="icon-facebook-f"></i>
                                                    Login With Facebook
                                                </a>
                                            </div><!-- End .col-6 -->
                                        </div><!-- End .row -->
                                    </div><!-- End .form-choice -->
                                </div><!-- .End .tab-pane -->
                            </div><!-- End .tab-content -->
                        </div><!-- End .form-tab -->
                    </div><!-- End .form-box -->
                </div><!-- End .modal-body -->
            </div><!-- End .modal-content -->
        </div><!-- End .modal-dialog -->
    </div><!-- End .modal -->

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Bắt sự kiện click vào nút Thêm vào giỏ hàng
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                button.addEventListener('click', function () {
                    // Lấy ID của sản phẩm từ thuộc tính data-product-id
                    const productId = this.getAttribute('data-product-id');
                    // Gửi yêu cầu AJAX để thêm sản phẩm vào giỏ hàng
                    addToCart(productId);
                });
            });

            // Hàm gửi yêu cầu AJAX để thêm sản phẩm vào giỏ hàng
            function addToCart(productId) {
                // Tạo yêu cầu AJAX
                const xhr = new XMLHttpRequest();
                // Định nghĩa phương thức và đường dẫn
                xhr.open('POST', '/cart/add/' + productId, true);
                // Thiết lập header để xác định yêu cầu AJAX
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                // Xử lý kết quả khi yêu cầu thành công
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        // Thành công, cập nhật giao diện hoặc hiển thị thông báo
                        alert('Sản phẩm đã được thêm vào giỏ hàng!');
                    } else {
                        // Xảy ra lỗi, hiển thị thông báo lỗi
                        alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng!');
                    }
                };
                // Gửi yêu cầu
                xhr.send();
            }
        });
    </script>

@endsection
{{--update--}}
