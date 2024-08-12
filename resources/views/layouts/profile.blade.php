@extends('index')
@section('main')
    <main class="profile-user container">
        <div class="container mt-5">
            <div class="row">
                <div class="col-sm-3 border-0">
                    <ul class="list-group user-profile">
                        <li class="list-group-item item1 border-0 d-flex align-items-start">
                            <img class="avatar-first col-4" src="{{ asset('storage/'.$user->avatar) }}" alt="">
                            <div class="col-8">
                                <p class="name-user ms-2">{{ $user->name }}</p>
                                <a href="{{ url('profile/edit') }}" role="button"
                                   aria-controls="collapseExample"><i
                                        class="bi bi-pencil-fill"></i> Sửa hồ sơ</a>
                            </div>
                        </li>
                        <div class="collapse show" id="collapseExample">
                            <div class="card card-body border-0" style="background-color: #f5f5f5;">
                                <ul class="list-group mt-0 pt-0">
                                    <li class="list-group-item item1 border-0"><a href="">Ngân Hàng</a></li>
                                    <li class="list-group-item item1 border-0">
                                        <i class="bi bi-buildings"></i> <a href="{{ route('profile.address') }}">Địa
                                            Chỉ</a>
                                    </li>
                                    <li class="list-group-item item1 border-0">
                                        <i class="bi bi-pencil-square"></i> <a href="{{ route('change_password') }}">Đổi
                                            Mật Khẩu</a>
                                    </li>
                                    <li class="list-group-item item1 border-0">
                                        <i class="bi bi-receipt icon1"></i> <a href="{{ route('myorder') }}">Đơn mua</a>
                                    </li>
                                    <li class="list-group-item item1 border-0">
                                        <i class="bi bi-bell icon1"></i> <a href="">Thông báo</a>
                                    </li>
                                    <li class="list-group-item item1 border-0">
                                        <i class="bi bi-cash-coin icon1"></i> <a href="">Mã voucher</a>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </ul>
                </div>
                @yield('main-profile')
            </div>
        </div>
    </main>
    <script>
        function showEmailInput() {
            document.getElementById('maskedEmail').classList.add('d-none');
            document.querySelector('#exampleInputEmail1').classList.remove('d-none');
            document.querySelector('.clo49Q').classList.add('d-none');
        }

        function showPhoneInput() {
            document.getElementById('maskedPhone').classList.add('d-none');
            document.querySelector('#phone').classList.remove('d-none');
            document.querySelector('.clo49QQ').classList.add('d-none');
        }

        function displaySelectedImage(event, elementId) {
            var reader = new FileReader();
            reader.onload = function () {
                var output = document.getElementById(elementId);
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
@endsection
