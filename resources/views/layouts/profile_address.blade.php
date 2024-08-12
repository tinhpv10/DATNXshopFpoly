@extends('layouts.profile')
@section('main-profile')
    <div class="col-sm-9 h-500" style="background-color: white;">
        <div class="row" style="height: 100%;">
            <div class="col-md-12 border-bottom gap-5 d-flex align-items-center" style="height: 15%;">
                <h5 class="card-title mt-0 ms-3 w-50 border-0">Địa chỉ của tôi</h5>
                <button type="button" class="btn btn-primary col-3 ms-auto" data-bs-toggle="modal"
                        data-bs-target="#addressModal" onclick="openModal()">
                    <i class="bi bi-plus-lg"></i> Thêm địa chỉ mới
                </button>
            </div>
            <div class="list-group list-group-flush container address-list address">
                @foreach($addresses as $address)
                    <div class="list-group-item item1 border-0">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6>{{ $address->name ?? '' }}</h6>
                                <p>{{ $address->phone ?? '' }}</p>
                            </div>
                            <div>
                                <a class="btn btn-link p-0 delete-button" data-bs-toggle="modal"
                                   data-bs-target="#addressModal" href="#" onclick="openModal({{ $address }})">Cập
                                    nhật</a>
                                <form action="{{ route('addresses.delete', $address->id) }}" method="POST"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0 delete-button">Xóa</button>
                                </form>
                            </div>
                        </div>
                        <p>{{ $address->address_specific ?? '' }}<br>{{ $address->ward->name ?? '' }}
                            , {{ $address->district->name ?? '' }}, {{ $address->province->name ?? '' }}</p>

                        <div class="d-flex justify-content-between">
                            @if($address->is_default == 0)
                                <form action="{{ route('addresses.setDefault', $address->id) }}" method="POST"
                                      class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 text-primary">Thiết lập mặc định
                                    </button>
                                </form>
                            @else
                                @if($address->is_default)
                                    <span class="badge bg-primary text-white">Mặc định</span>
                                @endif
                            @endif
                        </div>
                    </div>
                    <hr>
                @endforeach
            </div>
        </div>
    </div>
    </div>

    <!-- Modal for Create/Update -->
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addressModalLabel">Địa chỉ mới</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addressForm" method="POST" action="{{ route('addresses.store') }}">
                        @csrf
                        <input type="hidden" name="_method" value="POST">
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Họ và tên</label>
                            <input type="text" class="form-control" name="name" id="fullName">
                        </div>
                        <div class="mb-3">
                            <label for="phoneNumber" class="form-label">Số điện thoại</label>
                            <input type="number" class="form-control" name="phone" id="phoneNumber">
                        </div>
                        <div class="mb-3 dropdown">
                            <label for="fullAddress" class="form-label">Địa chỉ</label>
                            <input type="text" class="form-control" id="fullAddress" data-bs-toggle="dropdown">
                            <div class="dropdown-menu">
                                <div class="address-select">
                                    <select class="form-select" name="province_id" id="province">
                                        <option value="">Tỉnh/Thành phố</option>
                                        @foreach($province as $provinces)
                                            <option value="{{ $provinces->id }}">{{ $provinces->name ?? '' }}</option>
                                        @endforeach
                                    </select>
                                    <select class="form-select" name="district_id" id="district">
                                        <option value="">Quận/Huyện</option>
                                        @foreach($districts as $district)
                                            <option value="{{ $district->id }}">{{ $district->name ?? '' }}</option>
                                        @endforeach
                                    </select>
                                    <select class="form-select" name="ward_id" id="ward">
                                        <option value="">Phường/Xã</option>
                                        @foreach($wards as $ward)
                                            <option value="{{ $ward->id }}">{{ $ward->name ?? '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="addressSpecific" class="form-label">Địa chỉ cụ thể</label>
                            <input type="text" class="form-control" name="address_specific" id="addressSpecific">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Trở Lại</button>
                            <button type="submit" class="btn btn-primary">Hoàn thành</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- AJAX to handle dynamic selection -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function openModal(address = null) {
            $('#addressModalLabel').text(address ? 'Cập nhật địa chỉ' : 'Địa chỉ mới');
            $('#addressForm').attr('action', address ? `/profile/address/${address.id}` : '{{ route('addresses.store') }}');
            $('#addressForm input[name="_method"]').val(address ? 'PUT' : 'POST');
            $('#fullName').val(address ? address.name : '');
            $('#phoneNumber').val(address ? address.phone : '');
            $('#addressSpecific').val(address ? address.address_specific : '');
            $('#province').val(address ? address.province_id : '').change();
            $('#district').val(address ? address.district_id : '').change();
            $('#ward').val(address ? address.ward_id : '').change();
            // $('#fullAddress').val(address ? `${address.province.name}, ${address.district.name}, ${address.ward.name}` : '');

            if (address) {
                // Load districts
                $.ajax({
                    url: `/profile/address/districts/${address.province_id}`,
                    type: 'GET',
                    success: function (data) {
                        $('#district').empty().append('<option value="">Quận/Huyện</option>');
                        $.each(data, function (index, district) {
                            $('#district').append('<option value="' + district.id + '">' + district.name + '</option>');
                        });
                        $('#district').val(address.district_id);

                        // Load wards
                        $.ajax({
                            url: `/profile/address/wards/${address.district_id}`,
                            type: 'GET',
                            success: function (data) {
                                $('#ward').empty().append('<option value="">Phường/Xã</option>');
                                $.each(data, function (index, ward) {
                                    $('#ward').append('<option value="' + ward.id + '">' + ward.name + '</option>');
                                });
                                $('#ward').val(address.ward_id);
                            }
                        });
                    }
                });
            }
        }

        $(document).ready(function () {
            $('#province').change(function () {
                var provinceName = $("#province option:selected").text();
                updateFullAddress('province', provinceName);
                var provinceId = $(this).val();
                $.ajax({
                    url: '/profile/address/districts/' + provinceId,
                    type: 'GET',
                    success: function (data) {
                        $('#district').empty().append('<option value="">Quận/Huyện</option>');
                        $.each(data, function (index, district) {
                            $('#district').append('<option value="' + district.id + '">' + district.name + '</option>');
                        });
                        $('#ward').empty().append('<option value="">Phường/Xã</option>');
                    }
                });
            });

            $('#district').change(function () {
                var districtName = $("#district option:selected").text();
                updateFullAddress('district', districtName);
                var districtId = $(this).val();
                $.ajax({
                    url: '/profile/address/wards/' + districtId,
                    type: 'GET',
                    success: function (data) {
                        $('#ward').empty().append('<option value="">Phường/Xã</option>');
                        $.each(data, function (index, ward) {
                            $('#ward').append('<option value="' + ward.id + '">' + ward.name + '</option>');
                        });
                    }
                });
            });

            $('#ward').change(function () {
                var wardName = $("#ward option:selected").text();
                updateFullAddress('ward', wardName);
            });

            function updateFullAddress(type, text) {
                var currentAddress = $('#fullAddress').val();
                var addressParts = currentAddress.split(', ');

                switch (type) {
                    case 'province':
                        addressParts[0] = text;
                        addressParts[1] = addressParts[1] || '';
                        addressParts[2] = addressParts[2] || '';
                        break;
                    case 'district':
                        addressParts[1] = text;
                        addressParts[2] = addressParts[2] || '';
                        break;
                    case 'ward':
                        addressParts[2] = text;
                        break;
                }

                $('#fullAddress').val(addressParts.filter(Boolean).join(', '));
            }

            $('.delete-button').click(function (event) {
                event.preventDefault();
                var form = $(this).closest('form');
                Swal.fire({
                    title: 'Bạn có chắc chắn?',
                    text: "Bạn sẽ không thể khôi phục lại dữ liệu này!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Vâng, xóa nó!',
                    cancelButtonText: 'Không'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                })
            });
        });

    </script>

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

    @if($errors->any())
        <script>
            Swal.fire({
                title: 'Lỗi!',
                text: '{{ $errors->first() }}',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
@endsection
