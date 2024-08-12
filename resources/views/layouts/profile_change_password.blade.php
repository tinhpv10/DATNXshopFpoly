@extends('layouts.profile')
@section('main-profile')
    <div class="col-sm-9 h-500" style="background-color: white;">
        <div class="row" style="height: 100%;">
            <div class="col-md-12 border-bottom gap-5 d-flex align-items-center" style="height: 15%;">
                <h5 class="card-title mt-0 ms-3 w-50 border-0">Đổi mật khẩu</h5>
            </div>
            <div class="list-group list-group-flush password">
                <form method="post" action="{{ route('update_password') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Mật khẩu cũ</label>
                        <input type="password" name="old_password" class="form-control" id="old_password"
                               aria-describedby="emailHelp">
                        @if($errors->any('old_password'))
                            <div id="old_password" class="form-text text-danger">{{ $errors->first('old_password') }}.
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" name="new_password" id="new_password"
                               aria-describedby="emailHelp">
                        @if($errors->any('new_password'))
                            <div id="new_password" class="form-text text-danger">{{ $errors->first('new_password') }}.
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">Nhập lại mật khẩu mới</label>
                        <input type="password" class="form-control" name="confirm_password" id="confirm_password"
                               aria-describedby="emailHelp">
                        @if($errors->any('confirm_password'))
                            <div id="confirm_password"
                                 class="form-text text-danger">{{ $errors->first('confirm_password') }}.
                            </div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary">Lưu</button>
                </form>

            </div>
        </div>
    </div>
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
