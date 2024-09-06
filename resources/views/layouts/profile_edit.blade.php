@extends('layouts.profile')
@section('main-profile')
    <div class="col-sm-9 h-500" style="background-color: white;">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row" style="height: 100%;">
                <div class="col-md-12 border-bottom gap-5" style="height: 15%;">
                    <h5 class="card-title mt-2 ms-3">Hồ Sơ Của Tôi</h5>
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item">Quản lý thông tin hồ sơ để bảo mật tài
                            khoản</a>
                    </div>
                </div>
                <div class="col-md-8 border-end mb-3">
                    <div class="list-group list-group-flush pt-4">
                        <div class="mb-3 d-flex gap-3 justify-content-between">
                            <label for="name" class="form-label">Họ Tên</label>
                            <input type="text" class="form-control w-75" id="name" name="name"
                                   value="{{ $user->name }}">
                        </div>
                        <div class="mb-3 d-flex gap-3 justify-content-between">
                            <label for="exampleInputEmail1" class="form-label">Email</label>
                            <div class="d-flex align-items-center w-75">
                                <div class="e_Vt__ flex-grow-1">
                                    <div class="PBfYlq"
                                         id="maskedEmail">{{ substr_replace($user->email, '*********', 2, strpos($user->email, '@')-4) }}</div>
                                </div>
                                <button type="button" class="btn btn-link clo49Q" onclick="showEmailInput()">Thay đổi
                                </button>
                            </div>
                            <input type="email" class="form-control w-75 d-none" id="exampleInputEmail1" name="email"
                                   aria-describedby="emailHelp" value="{{ $user->email }}">
                        </div>

                        <div class="mb-3 d-flex gap-3 justify-content-between">
                            <label for="phone" class="form-label">SDT:</label>
                            <div class="d-flex align-items-center w-75">
                                <div class="e_Vt__ flex-grow-1">
                                    <div class="PBfYlq"
                                         id="maskedPhone">{{ substr_replace($user->phone, '******', 3, -3) }}</div>
                                </div>
                                <button type="button" class="btn btn-link clo49QQ" onclick="showPhoneInput()">Thay đổi
                                </button>
                            </div>
                            <input type="number" class="form-control w-75 d-none" id="phone" name="phone"
                                   aria-describedby="phoneHelp" value="{{ $user->phone }}">
                        </div>
                        <div class="mb-3 gender d-flex gap-3">
                            <label for="gender" class="form-label">Giới tính:</label>
                            <div class="form-check form-check-inline ms-5 ps-5">
                                <input class="form-check-input" type="radio" name="gender" id="inlineRadio1"
                                       value="Nam" {{ $user->gender == 'Nam' ? 'checked' : '' }}>
                                <label class="form-check-label" for="inlineRadio1">Nam</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="inlineRadio2"
                                       value="Nữ" {{ $user->gender == 'Nữ' ? 'checked' : '' }}>
                                <label class="form-check-label" for="inlineRadio2">Nữ</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="gender" id="inlineRadio3"
                                       value="Khác" {{ $user->gender == 'Khác' ? 'checked' : '' }}>
                                <label class="form-check-label" for="inlineRadio3">Khác</label>
                            </div>
                        </div>

                        <div class="mb-3 d-flex gap-3 justify-content-between">
                            <label for="exampleInputEmail1" class="form-label">Ngày sinh:</label>
                            <input type="date" class="form-control w-75" id="exampleInputEmail1" name="birthday"
                                   aria-describedby="emailHelp" value="{{ $user->birthday }}">
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary float-end w-25">Lưu</button>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3 pt-5">
                    <div class="list-group list-group-flush">
                        <!--Avatar-->
                        <div>
                            <div class="d-flex justify-content-center mb-4">
                                <label for="customFile2">
                                    @if($user->avatar == '')
                                        <img id="selectedAvatar" src="{{ asset('storage/avatars/PXQ9arQvm1y8wfwSFhl8znusNxX43SRCX1dGli76.jpg') }}"
                                             class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;"
                                             alt="Bấm vào đây để chọn ảnh đại diện"/>
                                    @else
                                        <img id="selectedAvatar" src="{{ asset('storage/'.$user->avatar) }}"
                                             class="rounded-circle" style="width: 100px; height: 100px; object-fit: cover;"
                                             alt="Bấm vào đây để chọn ảnh đại diện"/>
                                    @endif
                                </label>
                            </div>
                            <div class="d-flex justify-content-center">
                                <input type="file" class="form-control d-none" id="customFile2" name="avatar"
                                       onchange="displaySelectedImage(event, 'selectedAvatar')"/>
                            </div>
                        </div>
                        <p class="d-flex justify-content-center mt-2 ms-4 text-secondary">
                            Dụng lượng file tối đa 1 MB<br>
                            Định dạng:.JPEG, .PNG
                        </p>
                    </div>
                </div>

            </div>
        </form>
    </div>
    @if(session('success'))
        <script>
            Swal.fire({
                title: 'Thành công!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                title: 'Lỗi!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
@endsection
