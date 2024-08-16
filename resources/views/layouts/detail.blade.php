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
                        <div class="rating1 d-flex">
                            <div class="star text-warning pe-3 me-3 border-end">
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
                                <span class="ms-2">{{ number_format($product->rating, 1) }}</span>
                            </div>
                            <div class="text-body-tertiary pe-3 me-3 border-end">
                                <i class="bi bi-chat-left-dots"></i> <span class="ms-2">{{ $product->view_count }} Lượt xem</span>
                            </div>
                            <div class="text-body-tertiary">
                                <i class="bi bi-bag-check"></i> <span
                                    class="ms-2">@if($product->sold_count > 0)
                                        {{ $product->sold_count }} lượt bán
                                    @else
                                        Chưa có lượt bán
                                    @endif</span>
                            </div>
                        </div>
                        <div class="trade-price d-flex bg-warning-subtle p-3 mt-3">
                            <div class="border-end pe-5 me-3">
                                <div class="price fw-bold text-danger" id="retail-price">{{ $formattedRegularPrice }}
                                    VNĐ
                                </div>
                            </div>
                            <div>
                                <div class="price fw-bold"><s>{{ $formattedSalePrice }} VNĐ</s></div>
                            </div>
                        </div>
                        <div class="short-info mt-3">
                            @foreach($productVariations as $productVariation)
                                <div class="pb-3 variation-container" data-variation-id="{{ $productVariation->id }}">
                                    <div class="d-flex align-items-center">
                                        <div class="short-title text-body-tertiary">
                                            {{ $productVariation->variation_name }}:
                                        </div>
                                    </div>
                                    <div class="short-color d-flex flex-wrap mt-2">
                                        @foreach($productVariation->appProductVariationValue as $variationValue)
                                            <button type="button"
                                                    class="btn btn-outline-secondary me-2 variation-button"
                                                    style="{{ $productVariation->variation_name == 'Color' ? 'background-color: ' . $variationValue->color . ';' : '' }}"
                                                    title="{{ $variationValue->variation_value_name }}"
                                                    onclick="updateVariationValue({{ $productVariation->id }}, '{{ $variationValue->variation_value_name }}')"
                                                    id="variation-{{ $productVariation->id }}-{{ $variationValue->variation_value_name }}">
                                                {{ $variationValue->variation_value_name }}
                                                <i class="bi bi-check-lg check-icon" style="display: none;"></i>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                <script>
                    let selectedVariations = {};

                    function updateVariationValue(variationId, variationValueName) {
                        // Remove 'selected' class and hide check icon from all buttons of the same variation
                        document.querySelectorAll(`[id^='variation-${variationId}-']`).forEach(button => {
                            button.classList.remove('selected');
                            button.querySelector('.check-icon').style.display = 'none';
                        });

                        // Add 'selected' class and show check icon to the clicked button
                        const selectedButton = document.getElementById(`variation-${variationId}-${variationValueName}`);
                        selectedButton.classList.add('selected');
                        selectedButton.querySelector('.check-icon').style.display = 'inline';

                        selectedVariations[variationId] = variationValueName;

                        // Update hidden fields in the form
                        updateCartForm();

                        // Fetch retail price and image
                        fetch('/get-retail-price', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                selectedVariations: selectedVariations
                            })
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    console.error('Error:', data.error);
                                } else {
                                    document.getElementById('retail-price').textContent = data.retailPriceFormatted + ' VNĐ';
                                    if (data.media) {
                                        document.getElementById('change_image').src = '{{ asset('storage/') }}' + '/' + data.media;
                                        document.getElementById('product_image').value = data.media;
                                    }
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                            });
                    }

                    function updateCartForm() {
                        const variationIds = Object.keys(selectedVariations);
                        let variationsHTML = '';

                        variationIds.forEach(id => {
                            const variationValue = selectedVariations[id];
                            // Cập nhật dữ liệu biến thể trong form
                            variationsHTML += `<input type="hidden" name="variations[${id}][value]" value="${variationValue}">`;
                        });

                        document.getElementById('variations-container').innerHTML = variationsHTML;
                    }


                </script>


                <div class="col-12 col-md-3 col-lg-3">
                    <div class="box-right border rounded-2 p-3">
                        <div class="d-flex">
                            <div class="avatar-img">
                                <img src="{{ asset('storage/' . $shop->avatar)}}" alt="{{ $shop->name }}">
                            </div>
                            <div class="ms-2">
                                <h5 class="fs-5">Nhà cung cấp</h5>
                                <span>{{ $shop->name }}</span>
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
                            <div class="text-body-tertiary ms-2">Người bán đã được xác minh</div>
                        </div>
                        <div class="d-flex mb-2">
                            <div class="icon text-body-tertiary">
                                <i class="bi bi-globe"></i>
                            </div>
                            <div class="text-body-tertiary ms-2">Giao hàng trên toàn thế giới</div>
                        </div>
                        <div class="d-grid gap-2 mt-4">
                            <form id="add-to-cart-form" method="POST" action="{{ route('product.addToCart') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="product_name" value="{{ $product->name }}">
                                <input type="hidden" name="product_price" value="{{ $product->sale_price }}">
                                <input type="hidden" name="product_image" id="product_image"
                                       value="{{ $product->main_image }}">
                                <div id="variations-container"></div>
                                <button class="btn btn-primary add-to-cart-btn w-100" type="submit">Thêm vào giỏ hàng
                                </button>
                            </form>
                            <a href="{{ route('shop', $product->shop_id) }}"
                               class="btn btn-light text-primary text-decoration-none">Xem cửa hàng</a>
                        </div>
                    </div>

                    <div class="text-center text-primary fw-medium mt-5">
                        <form action="{{ route('wishlist.toggle', $product->id) }}" method="POST"
                              style="display: inline;">
                            @csrf
                            <ul class="featured__item__pic__hover d-flex">
                                <li>
                                    <button class="d-flex justify-content-center" type="submit"
                                            style="border: none; background: none;">
                                        <i class="fa fa-heart {{ $isFavorite ? 'text-white' : '' }}"></i>
                                        <span
                                            class="my-2 ms-2">{{ $isFavorite ? 'Đã yêu thích' : 'Yêu thích' }} </span>
                                    </button>
                                </li>
                            </ul>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="container p-0">
            <div class="block-detail mb-3">
                <div class="row">
                    <div class="col-12 col-md-8 col-lg-8">
                        <div class="bg-white border rounded-2 px-3">
                            <ul class="nav  nav-underline border-bottom" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                            data-bs-target="#description-tab-pane" type="button" role="tab"
                                            aria-controls="description-tab-pane" aria-selected="true">Đánh giá
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link " id="profile-tab" data-bs-toggle="tab"
                                            data-bs-target="#reviews-tab-pane" type="button" role="tab"
                                            aria-controls="reviews-tab-pane" aria-selected="false">Mô tả chi tiết
                                    </button>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="description-tab-pane" role="tabpanel"
                                     aria-labelledby="home-tab" tabindex="0">
                                    <div class="py-3">
                                        {{-- form bình luận --}}
                                        <div class="mb-3">
                                            <h5>VIẾT ĐÁNH GIÁ CỦA BẠN: </h5>
                                            @if(session('success'))
                                                <div class="alert alert-success" role="alert">
                                                    {{ session('success')  }}
                                                </div>
                                            @endif
                                            <form action="{{ route('uploadComment') }}" method="POST"
                                                  enctype="multipart/form-data" id="commentForm">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="">Đánh giá của bạn <span
                                                                    class="text-danger">*</span></label>
                                                            <div class="d-flex rating-form" id="rating-container">
                                                                <input type="radio" name="rating" id="rating1"
                                                                       value="1">
                                                                <label
                                                                    class="label-custom rating1 form-check-label mx-1"
                                                                    for="rating1">
                                                                    <i class="bi bi-star-fill"></i>
                                                                </label>
                                                                <input type="radio" name="rating" id="rating2"
                                                                       value="2">
                                                                <label
                                                                    class="label-custom rating2 form-check-label mx-1"
                                                                    for="rating2">
                                                                    <i class="bi bi-star-fill"></i>
                                                                </label>
                                                                <input type="radio" name="rating" id="rating3"
                                                                       value="3">
                                                                <label
                                                                    class="label-custom rating3 form-check-label mx-1"
                                                                    for="rating3">
                                                                    <i class="bi bi-star-fill"></i>
                                                                </label>
                                                                <input type="radio" name="rating" id="rating4"
                                                                       value="4">
                                                                <label
                                                                    class="label-custom rating4 form-check-label mx-1"
                                                                    for="rating4">
                                                                    <i class="bi bi-star-fill"></i>
                                                                </label>
                                                                <input type="radio" name="rating" id="rating5"
                                                                       value="5">
                                                                <label
                                                                    class="label-custom rating5 form-check-label mx-1"
                                                                    for="rating5">
                                                                    <i class="bi bi-star-fill"></i>
                                                                </label>
                                                            </div>
                                                            @error('rating')
                                                            <small class="text-danger">{{$message}}</small>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    @if(!Auth::check())
                                                        <div class="col-4">
                                                            <div class="mb-3">
                                                                <label for="user_name" class="form-label">Tên <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" name="user_name" id="user_name"
                                                                       class="form-control"
                                                                       value="{{old('user_name')}}">
                                                                @error('user_name')
                                                                <small class="text-danger">{{$message}}</small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="mb-3">
                                                                <label for="email" class="form-label">Email <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="text" name="email" id="email"
                                                                       class="form-control" value="{{old('email')}}">
                                                                @error('email')
                                                                <small class="text-danger">{{$message}}</small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="mb-3">
                                                                <label for="password" class="form-label">Mật khẩu <span
                                                                        class="text-danger">*</span></label>
                                                                <input type="password" name="password"
                                                                       class="form-control" id="password">
                                                                @error('password')
                                                                <small class="text-danger">{{$message}}</small>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    @else
                                                        <input type="hidden" name="user_name"
                                                               value="aaaaaaaaaaaaaaaaaaaaa">
                                                        <input type="hidden" name="email" value="aaaaaaaa@gmail.com">
                                                        <input type="hidden" name="password" value="111ooooo">
                                                    @endif
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="content" class="form-label">Nội dung <span
                                                                    class="text-danger">*</span></label>
                                                            <textarea name="comment_content" class="form-control"
                                                                      rows="3"
                                                                      id="content">{{ old('comment_content') }}</textarea>
                                                            @error('comment_content')
                                                            <small class="text-danger">{{$message}}</small>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12">
                                                        <div class="mb-3">
                                                            <label for="" class="form-label">Hình ảnh</label>
                                                            <div class="dropzone" id="dropzone">
                                                                <div class="dz-message needsclick">
                                                                    <!--begin::Icon-->
                                                                    <i class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>
                                                                    <!--end::Icon-->
                                                                    <!--begin::Info-->
                                                                    <div class="ms-3">
                                                                        <h3 class="fs-6 fw-bolder mb-1">Thả tập tin vào
                                                                            đây hoặc bấm vào để tải lên.</h3>
                                                                        <span class="fw-medium text-primary opacity-75">Tải lên tối đa 5 tệp</span>
                                                                    </div>
                                                                    <!--end::Info-->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                    <div class="col-12">
                                                        <button id="submit-btn" class="btn btn-primary" type="submit">
                                                            Gửi đánh giá
                                                        </button>

                                                        <button id="loading-btn"
                                                                class="btn btn-primary disabled d-none">
                                                            <span class="spinner-border spinner-border-sm text-light"
                                                                  role="status"> </span> Đang tải ảnh
                                                        </button>

                                                        <button id="upload-btn" class="btn btn-primary disabled d-none">
                                                            <span class="spinner-border spinner-border-sm text-light"
                                                                  role="status"> </span> Đang gửi yêu cầu
                                                        </button>

                                                    </div>

                                                </div>
                                            </form>
                                        </div>
                                        <hr>
                                        {{-- hiển thị bình luận của người dùng --}}
                                        @if(count($listComment) > 0)
                                            @foreach($listComment as $itemComment)
                                                <div class="d-flex show-comment mb-3">
                                                    <div class="avatar-comment me-2">
                                                        <img class="rounded-circle"
                                                             src="{{ $itemComment->user->avatar ?? 'https://t4.ftcdn.net/jpg/00/64/67/27/240_F_64672736_U5kpdGs9keUll8CRQ3p3YaEv2M6qkVY5.jpg' }}"
                                                             alt="">
                                                    </div>
                                                    <div class="info-comment">
                                                        <div class="fw-medium">{{ $itemComment->user->name }}</div>
                                                        <div class="rating">
                                                            @for ($i = 1; $i <= 5; $i++)
                                                                <i class="bi bi-star{{ $i <= $itemComment->rating ? '-fill' : '' }}"></i>
                                                            @endfor
                                                        </div>
                                                        <div class="time text-body-tertiary">
                                                            {{ \Carbon\Carbon::parse($itemComment->created_at)->format('d/m/Y H:i')  }}
                                                        </div>
                                                        <div class="content mb-3">
                                                            <div class="excerpt">
                                                                {{ $itemComment->content }}
                                                            </div>
                                                        </div>
                                                        <div class="img-comment d-flex flex-wrap">
                                                            @foreach($itemComment->reviewMedia as $image)
                                                                <div class="box-img">
                                                                    <a data-fancybox="gallery"
                                                                       data-src="{{ $image->review_media }}">
                                                                        <img src="{{ $image->review_media }}" alt=""/>
                                                                    </a>
                                                                </div>
                                                            @endforeach

                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                                <div class="pagination-menu">
                                                    {{ $listComment->links() }}
                                                </div>
                                        @else
                                            <h5>CHƯA CÓ ĐÁNH GIÁ</h5>
                                        @endif
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="reviews-tab-pane" role="tabpanel"
                                     aria-labelledby="profile-tab" tabindex="0">
                                    <div class="py-3">
                                        <p>{!!$product->description!!}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 col-lg-4 mb-3">
                        <div class="bg-white rounded-2 border p-3">
                            <div class="fw-bold mb-3">Bạn có thể thích</div>
                            @foreach ($favoriteProducts as $favoriteProduct)
                                <div class="border d-flex align-items-center p-2 my-2">
                                    <div class="avatar-img">
                                        <img src="{{ asset('storage/' .$favoriteProduct->main_image )}}" width="50px"
                                             height="70px" alt="{{ $favoriteProduct->name }}">
                                    </div>
                                    <div class="ms-3 flex-grow-1 d-flex align-items-center">
                                        <div class="d-flex flex-column">
                                            <div class="fw-bold font-weight-bold">{{ $favoriteProduct->name }}</div>
                                            <div class="price">{{ $favoriteProduct->formattedRegularPrice }}
                                                VNĐ-{{ $favoriteProduct->formattedSalePrice }}VNĐ
                                            </div>
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


    <!-- Sign in / Register Modal -->
    {{--    <div class="modal fade" id="signin-modal" tabindex="-1" role="dialog" aria-hidden="true">--}}
    {{--        <div class="modal-dialog modal-dialog-centered" role="document">--}}
    {{--            <div class="modal-content">--}}
    {{--                <div class="modal-body">--}}
    {{--                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
    {{--                        <span aria-hidden="true"><i class="icon-close"></i></span>--}}
    {{--                    </button>--}}

    {{--                    <div class="form-box">--}}
    {{--                        <div class="form-tab">--}}
    {{--                            <ul class="nav nav-pills nav-fill nav-border-anim" role="tablist">--}}
    {{--                                <li class="nav-item">--}}
    {{--                                    <a class="nav-link active" id="signin-tab" data-toggle="tab" href="#signin"--}}
    {{--                                       role="tab" aria-controls="signin" aria-selected="true">Sign In</a>--}}
    {{--                                </li>--}}
    {{--                                <li class="nav-item">--}}
    {{--                                    <a class="nav-link" id="register-tab" data-toggle="tab" href="#register" role="tab"--}}
    {{--                                       aria-controls="register" aria-selected="false">Register</a>--}}
    {{--                                </li>--}}
    {{--                            </ul>--}}
    {{--                            <div class="tab-content" id="tab-content-5">--}}
    {{--                                <div class="tab-pane fade show active" id="signin" role="tabpanel"--}}
    {{--                                     aria-labelledby="signin-tab">--}}
    {{--                                    <form action="#">--}}
    {{--                                        <div class="form-group">--}}
    {{--                                            <label for="singin-email">Username or email address *</label>--}}
    {{--                                            <input type="text" class="form-control" id="singin-email"--}}
    {{--                                                   name="singin-email" required>--}}
    {{--                                        </div><!-- End .form-group -->--}}

    {{--                                        <div class="form-group">--}}
    {{--                                            <label for="singin-password">Password *</label>--}}
    {{--                                            <input type="password" class="form-control" id="singin-password"--}}
    {{--                                                   name="singin-password" required>--}}
    {{--                                        </div><!-- End .form-group -->--}}

    {{--                                        <div class="form-footer">--}}
    {{--                                            <button type="submit" class="btn btn-outline-primary-2">--}}
    {{--                                                <span>LOG IN</span>--}}
    {{--                                                <i class="icon-long-arrow-right"></i>--}}
    {{--                                            </button>--}}

    {{--                                            <div class="custom-control custom-checkbox">--}}
    {{--                                                <input type="checkbox" class="custom-control-input"--}}
    {{--                                                       id="signin-remember">--}}
    {{--                                                <label class="custom-control-label" for="signin-remember">Remember--}}
    {{--                                                    Me</label>--}}
    {{--                                            </div><!-- End .custom-checkbox -->--}}

    {{--                                            <a href="#" class="forgot-link">Forgot Your Password?</a>--}}
    {{--                                        </div><!-- End .form-footer -->--}}
    {{--                                    </form>--}}
    {{--                                    <div class="form-choice">--}}
    {{--                                        <p class="text-center">or sign in with</p>--}}
    {{--                                        <div class="row">--}}
    {{--                                            <div class="col-sm-6">--}}
    {{--                                                <a href="#" class="btn btn-login btn-g">--}}
    {{--                                                    <i class="icon-google"></i>--}}
    {{--                                                    Login With Google--}}
    {{--                                                </a>--}}
    {{--                                            </div><!-- End .col-6 -->--}}
    {{--                                            <div class="col-sm-6">--}}
    {{--                                                <a href="#" class="btn btn-login btn-f">--}}
    {{--                                                    <i class="icon-facebook-f"></i>--}}
    {{--                                                    Login With Facebook--}}
    {{--                                                </a>--}}
    {{--                                            </div><!-- End .col-6 -->--}}
    {{--                                        </div><!-- End .row -->--}}
    {{--                                    </div><!-- End .form-choice -->--}}
    {{--                                </div><!-- .End .tab-pane -->--}}
    {{--                                <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">--}}
    {{--                                    <form action="#">--}}
    {{--                                        <div class="form-group">--}}
    {{--                                            <label for="register-email">Your email address *</label>--}}
    {{--                                            <input type="email" class="form-control" id="register-email"--}}
    {{--                                                   name="register-email" required>--}}
    {{--                                        </div><!-- End .form-group -->--}}

    {{--                                        <div class="form-group">--}}
    {{--                                            <label for="register-password">Password *</label>--}}
    {{--                                            <input type="password" class="form-control" id="register-password"--}}
    {{--                                                   name="register-password" required>--}}
    {{--                                        </div><!-- End .form-group -->--}}

    {{--                                        <div class="form-footer">--}}
    {{--                                            <button type="submit" class="btn btn-outline-primary-2">--}}
    {{--                                                <span>SIGN UP</span>--}}
    {{--                                                <i class="icon-long-arrow-right"></i>--}}
    {{--                                            </button>--}}

    {{--                                            <div class="custom-control custom-checkbox">--}}
    {{--                                                <input type="checkbox" class="custom-control-input" id="register-policy"--}}
    {{--                                                       required>--}}
    {{--                                                <label class="custom-control-label" for="register-policy">I agree to the--}}
    {{--                                                    <a href="#">privacy policy</a> *</label>--}}
    {{--                                            </div><!-- End .custom-checkbox -->--}}
    {{--                                        </div><!-- End .form-footer -->--}}
    {{--                                    </form>--}}
    {{--                                    <div class="form-choice">--}}
    {{--                                        <p class="text-center">or sign in with</p>--}}
    {{--                                        <div class="row">--}}
    {{--                                            <div class="col-sm-6">--}}
    {{--                                                <a href="#" class="btn btn-login btn-g">--}}
    {{--                                                    <i class="icon-google"></i>--}}
    {{--                                                    Login With Google--}}
    {{--                                                </a>--}}
    {{--                                            </div><!-- End .col-6 -->--}}
    {{--                                            <div class="col-sm-6">--}}
    {{--                                                <a href="#" class="btn btn-login  btn-f">--}}
    {{--                                                    <i class="icon-facebook-f"></i>--}}
    {{--                                                    Login With Facebook--}}
    {{--                                                </a>--}}
    {{--                                            </div><!-- End .col-6 -->--}}
    {{--                                        </div><!-- End .row -->--}}
    {{--                                    </div><!-- End .form-choice -->--}}
    {{--                                </div><!-- .End .tab-pane -->--}}
    {{--                            </div><!-- End .tab-content -->--}}
    {{--                        </div><!-- End .form-tab -->--}}
    {{--                    </div><!-- End .form-box -->--}}
    {{--                </div><!-- End .modal-body -->--}}
    {{--            </div><!-- End .modal-content -->--}}
    {{--        </div><!-- End .modal-dialog -->--}}
    {{--    </div><!-- End .modal -->--}}

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelector('.add-to-cart-btn').addEventListener('click', function (event) {
                event.preventDefault();

                const form = document.getElementById('add-to-cart-form');
                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Sản phẩm đã được thêm vào giỏ hàng!');
                        } else {
                            alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng!');
                        }
                    })
                    .catch(error => {
                        console.error('Lỗi:', error);
                        alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng!');
                    });
            });
        });

        function addToCart(productId, productName, productPrice, productImage, variations) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/add-to-cart', true);
            xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    alert('Sản phẩm đã được thêm vào giỏ hàng!');
                } else {
                    alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng!');
                }
            };
            xhr.send(JSON.stringify({
                product_id: productId,
                product_name: productName,
                product_price: productPrice,
                product_image: productImage,
                variations: variations
            }));
        }
        })
        ;

    </script>

@endsection
{{--update--}}

@push('script')
    <script>
        var link = window.location.href; // Lấy URL hiện tại
        //----- drop zone
        Dropzone.options.dropzone = {
            url: link,
            maxFiles: 5,
            maxFilesize: 12,
            renameFile: function (file) {
                var dt = new Date();
                var time = dt.getTime();
                var fileName = file.name;
                var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
                return time + '' + fileName;
            },
            acceptedFiles: '.jpeg,.jpg,.png,.gif',
            addRemoveLinks: true,
            timeout: 5000,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            init: function () {
                this.on("addedfile", function () {
                    document.getElementById('submit-btn').classList.add('d-none');
                    document.getElementById('loading-btn').classList.remove('d-none');
                });
                this.on("success", function (file, response) {
                    $.ajax({
                        type: 'POST',
                        url: '/uploadImage',
                        data: {
                            filename: file.upload.filename,
                            filepath: file.dataURL,
                        },
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        success: function (data) {
                            console.log(data);
                            document.getElementById('submit-btn').classList.remove('d-none');
                            document.getElementById('loading-btn').classList.add('d-none');
                        },
                        error: function (data) {
                            console.log('Error:', data);
                            document.getElementById('submit-btn').classList.remove('d-none');
                            document.getElementById('loading-btn').classList.add('d-none');
                        }
                    });
                });
                this.on("error", function (file, response) {
                    return false;
                });
                this.on("maxfilesexceeded", function (file) {
                    this.removeFile(file); // Remove the exceeded file
                    alert("Bạn chỉ được tải lên tối đa 5 hình ảnh."); // Show an alert or customize as needed
                });
                this.on("removedfile", function (file) {
                    var serverFilename = file.upload.filename; // Retrieve the uploaded file ID or name
                    $.ajax({
                        type: 'POST',
                        url: '/deleteImage',
                        data: {
                            filename: serverFilename,
                        },
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        success: function (data) {
                            console.log(data);
                            if (file.previewElement != null) {
                                if (file.previewElement.parentNode != null) {
                                    file.previewElement.parentNode.removeChild(file.previewElement);
                                }
                            }
                            document.getElementById('submit-btn').classList.remove('d-none');
                            document.getElementById('loading-btn').classList.add('d-none');
                        },
                        error: function (data) {
                            console.log('Error:', data);
                        }
                    });
                });
            }
        };
        // Xử lý khi submit form
        document.getElementById('commentForm').addEventListener('submit', function () {
            document.getElementById('submit-btn').classList.add('d-none');
            document.getElementById('loading-btn').classList.add('d-none');
            document.getElementById('upload-btn').classList.remove('d-none');
        });

        //----- Đánh giá
        (function () {
            function StarRating(containerId, valueContainerId) {
                const container = document.getElementById(containerId);
                const radios = container.querySelectorAll('input[type="radio"]');
                const labels = container.querySelectorAll('.label-custom');
                const ratingValue = document.getElementById(valueContainerId);
                let selectedRating = 0;

                radios.forEach(radio => {
                    radio.addEventListener('change', function () {
                        selectedRating = this.value;
                        // ratingValue.textContent = `Đánh giá của bạn: ${selectedRating} sao`;
                        updateStars(selectedRating);
                    });
                });

                labels.forEach((label, index) => {
                    label.addEventListener('mouseover', function () {
                        updateStars(index + 1);
                    });

                    label.addEventListener('mouseout', function () {
                        updateStars(selectedRating);
                    });
                });

                function updateStars(rating) {
                    labels.forEach((label, index) => {
                        if (index < rating) {
                            label.classList.add('selected_rating');
                        } else {
                            label.classList.remove('selected_rating');
                        }
                    });
                }
            }

            // Khởi tạo hệ thống đánh giá sao
            document.addEventListener('DOMContentLoaded', function () {
                new StarRating('rating-container', 'rating-value');
            });
        })();
    </script>
@endpush
