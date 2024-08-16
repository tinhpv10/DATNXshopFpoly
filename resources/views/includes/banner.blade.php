<div class="main">
    <div class="container">
        <div class="section-banner border rounded-2">
            <div class="d-grid group">
                <div class="category">
                    <ul class="list-group">
                        @foreach($categoryBanners as $categoryBanner)
                            <li class="list-group-item">{{ $categoryBanner->name }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="image-banner">
                    <div class="box-img">
                        <img src="https://d3design.vn/uploads/%C3%A9dfjh30.jpg" alt="">
                    </div>
                </div>
                <div class="box">
                    <div class="box-1 mt-0 rounded-2">
                        <div class="d-flex mb-3">
                            <div class="avatar">
                                <img src="https://t4.ftcdn.net/jpg/05/11/55/91/240_F_511559113_UTxNAE1EP40z1qZ8hIzGNrB0LwqwjruK.jpg" alt="">
                            </div>
                            <div class="text">
                                Chào, bạn có muốn trở thành người bán
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button class="btn btn-primary" type="button">Đăng ký</button>
                            <button class="btn btn-primary" type="button">Tìm hiểu</button>
                        </div>
                        <!-- <button class="btn btn-primary mb-2">Join now</button>
                        <button class="btn bg-white">Login</button> -->
                    </div>
                    <div class="box-2 rounded-2">
                        <div> Được giảm 10% với nhà cung cấp mới</div>
                    </div>
                    <div class="box-3 rounded-2">
                        <div>Gửi báo giá với tùy chọn nhà cung cấp</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{--update--}}
