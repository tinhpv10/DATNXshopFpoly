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
            <div class="post-detail">
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-8 offset-lg-2">
                        <h2>{{ $detail->title }}</h2>
                        <div class="meta-description">
                            {{ $detail->meta_description }}
                        </div>
                        <div class="d-flex mb-3">
                            <div class=" me-3"><i class="bi bi-person-circle me-1 text-primary"></i>Admin</div>
                            <div class=""><i
                                    class="bi bi-calendar3 me-1 text-primary"></i>{{ \Carbon\Carbon::parse($detail->created_at)->format('d/m/Y') }}
                            </div>
                        </div>
                        {{--                        <div class="box-img my-3">--}}
                        {{--                            <img src="../storage/{{ $detail->thumbnail }}" alt="" class="rounded">--}}
                        {{--                        </div>--}}

                        <div class="content">

                            {!! $detail->content !!}
                        </div>
                    </div>

                </div>

            </div>

            <div class="post-like">
                <h4>Bạn có thể thích</h4>
                <div class="row">
                    <div class="you-may-like">

                        @foreach($relatedPosts as $item)
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="card post-item h-100">
                                    <div class="box-img">
                                        <a href="{{ route('detailPost', $item->id) }}"> <img
                                                src="../storage/{{ $item->thumbnail }}" class="card-img-top" alt="..."></a>
                                    </div>

                                    <div class="card-body content">
                                        <h5 class="card-title post-title"><a class="text-black text-decoration-none"
                                                                             href="{{ route('detailPost', $item->id) }}">{{ $item->title }}</a>
                                        </h5>
                                        <div class="card-text"><small
                                                class="text-body-secondary me-2">{{ $item->user->name }}</small><small
                                                class="text-body-secondary">{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y') }}</small>
                                        </div>
                                        <div class="card-text meta-description">{{ $item->meta_description }}
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>

                </div>

            </div>

            @include('includes.category-post')
        </div>


    </div>
@endsection
@extends('index')
