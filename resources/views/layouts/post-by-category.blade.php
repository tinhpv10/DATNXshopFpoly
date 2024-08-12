@section('main')
    <div class="main">
        <div class="container">
            <nav style=""
                 aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 py-3">
                    <li class="breadcrumb-item"><a href="{{ url('/')}}" class="text-decoration-none">Trang chủ</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/post') }}"
                                                                              class="text-decoration-none">Bài viết</a>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="container-lg">
            <div class="post-by-category">
                <div class="category-banner">
                    <div class="category-name fw-bold">{{ $category->name }}</div>
                    <img src="{{ asset('image/image112.png') }}" alt="BANNER IMG">
                </div>

                <div class="post-featured">
                    <h4>Bài viết nổi bậc</h4>
                    <div class="border rounded bg-white">
                        <div class="row category-post-featured">

                            @foreach($postByCategory as $item)
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="post-info">
                                        <h3 class="info-title"><a class="text-black text-decoration-none"
                                                                  href="{{ route('detailPost', $item->id) }}">{{ $item->title }}</a>
                                        </h3>
                                        <div class="meta-description">
                                            {{ $item->meta_description }}
                                        </div>

                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-6">
                                    <div class="post-img">
                                        <a href="{{ route('detailPost', $item->id) }}"><img
                                                src="../storage/{{ $item->thumbnail }}" alt=""></a>

                                    </div>
                                </div>

                                @break
                            @endforeach
                        </div>
                    </div>

                </div>


            </div>

            <div class="list-post">
                <h4>Bài mới mỗi ngày</h4>
                <div class="row gy-3">
                    @foreach($postByCategory->skip(1) as $item)
                        @include('includes.list-post')
                    @endforeach


                </div>
            </div>

            @include('includes.category-post')
        </div>


    </div>
@endsection
@extends('index')
