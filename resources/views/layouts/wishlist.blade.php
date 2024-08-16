@extends('index')
@section('main')
    <div class="main">
        <div class="container">
            <nav aria-label="breadcrumb" class="breadcrumb-nav">
                <div class="container">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="">Sản phẩm yêu thích</a></li>
                    </ol>
                </div>
            </nav>
        </div>

        <div class="container-lg">
            @if($products->isEmpty())
                <div class="row my-5">
                    <div class="col-12 col-md-6 offset-md-3">
                        <div class="card">
                            <h5 class="card-header">Sản phẩm yêu thích</h5>
                            <div class="card-body">
                                <div class="alert alert-warning" role="alert">
                                    Hiện chưa có sản phẩm yêu thích.
                                </div>
                                <a href="{{ route('product.index') }}"
                                   class="btn btn-primary text-white text-decoration-none">Xem sản phẩm</a>
                            </div>
                        </div>

                    </div>
                </div>
            @else
                <div class="row">
                    @foreach($products as $productItem)
                        <div class="col-12 col-md-4 col-lg-3">
                            @include('includes.productItem')
                        </div>
                    @endforeach

                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>

@endsection


