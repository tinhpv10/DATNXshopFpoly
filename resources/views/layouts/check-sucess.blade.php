@extends('index')
@section('main')
    <main class="container">
        <div class="card-body">

            <figure class="mt-4 mx-auto text-center" style="max-width:600px">
                <svg width="96px" height="96px" viewBox="0 0 96 96" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                        <g id="round-check">
                            <circle id="Oval" fill="#D3FFD9" cx="48" cy="48" r="48"></circle>
                            <circle id="Oval-Copy" fill="#87FF96" cx="48" cy="48" r="36"></circle>
                            <polyline id="Line" stroke="#04B800" stroke-width="4" stroke-linecap="round" points="34.188562 49.6867496 44 59.3734993 63.1968462 40.3594229"></polyline>
                        </g>
                    </g>
                </svg>
                <figcaption class="my-3">
                    <h3 class="text-success">Đặt hàng thành công</h3>
                    <h6 class="text-center">Cảm ơn bạn đã đặt hàng</h6>
                </figcaption>
            </figure>
            <br>

        </div>
    </main>

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
