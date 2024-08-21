
<footer class="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-3">
                    <div class="box-img">
                        <img src="{{ asset('image/logo-doanvth-pro 1.png') }}" alt="">
                    </div>
                    <div class="my-4"><i class="bi bi-geo-alt-fill"></i> 468/29 Hẻm 468 Đồng Khởi, Tân Hiệp, Biên Hòa,
                        Đồng Nai.
                    </div>
                    <div class="my-4"><i class="bi bi-telephone-fill"></i> 0762345979</div>
                    <div class="my-4"><i class="bi bi-envelope-fill"></i> donashop@gmail.com</div>
                    <div class="icon-contact d-flex">
                        <div class="icon">
                            <i class="fa-brands fa-facebook-f"></i>
                        </div>
                        <div class="icon">
                            <i class="fa-brands fa-twitter"></i>
                        </div>
                        <div class="icon">
                            <i class="fa-brands fa-linkedin-in"></i>
                        </div>
                        <div class="icon">
                            <i class="fa-brands fa-instagram"></i>
                        </div>
                        <div class="icon">
                            <i class="fa-brands fa-youtube"></i>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-9">
                    <div class="d-flex">
                        <div class="menu-footer">
                            <h6 class="footer-title">THÔNG TIN</h6>
                            <ul>
                                <li><a href="" class="text-decoration-none">Hướng dẫn</a></li>
                                <li><a href="" class="text-decoration-none">Chính sách</a></li>
                                <li><a href="{{ url('/user-agreement') }}" class="text-decoration-none">Thỏa thuận người
                                        dùng</a></li>
                                <li><a href="{{ url('/condition') }}" class="text-decoration-none">Điều khoản & điều
                                        kiện</a></li>
                            </ul>
                        </div>
                        <div class="menu-footer">
                            <h6 class="footer-title">KHÁCH HÀNG</h6>
                            <ul>
                                <li><a href="" class="text-decoration-none">Thương hiệu</a></li>
                                <li><a href="" class="text-decoration-none">Phiếu quà tặng</a></li>
                                <li><a href="" class="text-decoration-none">Khuyến mãi</a></li>
                                <li><a href="" class="text-decoration-none">Đổi trả hàng</a></li>
                            </ul>
                        </div>
                        <div class="menu-footer">
                            <h6 class="footer-title">Get App</h6>
                            <img class="mb-1" src="{{ asset('image/market-button-1.png') }}" alt="">
                            <img src="{{ asset('image/market-button.png') }}" alt="">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class=" ">© 2024 Donna.Store</div>
                <div class=" ">
                    <div class="btn-group dropup">
                        <button type="button" class="dropdown-toggle" data-bs-toggle="dropdown"
                                aria-expanded="false">
                            <img src="../public/image/AE@2x.png" alt="" class="me-1"> English
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><img src="../public/image/AE@2x.png" alt=""
                                                                       class="me-1">Menu item</a></li>
                            <li><a class="dropdown-item" href="#"><img src="../public/image/AE@2x.png" alt=""
                                                                       class="me-1">Menu item</a></li>
                            <li><a class="dropdown-item" href="#"><img src="../public/image/AE@2x.png" alt=""
                                                                       class="me-1">Menu item</a></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</footer>


<script type="text/javascript" src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script type="text/javascript" src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="{{ asset('assets/slick-master/slick/slick.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://kit.fontawesome.com/8ea8a81b6f.js" crossorigin="anonymous"></script>

<script src="{{ asset('assets/js/script.js') }}"></script>
<script src="{{ asset('assets/js/slick.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Plugins JS File -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/15.5.0/nouislider.min.js"></script>

{{--update--}}
<script src="{{ asset('assets/js/wishlist.js') }}"></script>
<!-- Dropzone JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.0/dropzone.js"></script>
<script src='https://cdn.jsdelivr.net/npm/jquery@3.6.2/dist/jquery.min.js'></script>

{{-- fancybox --}}
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    Fancybox.bind('[data-fancybox]', {
//
    });
</script>
@stack('script')
