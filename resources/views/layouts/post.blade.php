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
            <div class="post-banner">
                <img src="{{ asset('image/bannerPost.png') }}" alt="BANNER IMG">
            </div>

            <div class="post-featured">
                <h4>Bài viết nổi bậc</h4>
                <div class="row">
                    @php $count = 0 @endphp
                    @while($count < count($postNew))
                        <div class="col-12 col-md-12 col-lg-7">
                            <div class="featured-left border rounded bg-white p-3">
                                <div class="box-img">
                                    <a href="{{ route('detailPost', $postNew[$count]->id) }}">
                                        <img src="storage/{{ $postNew[$count]->thumbnail }}" alt="POST IMG">
                                    </a>
                                </div>
                                <div class="content">
                                    <h3><a class="text-black text-decoration-none"
                                           href="{{ route('detailPost', $postNew[$count]->id) }}">{{ $postNew[$count]->title }}</a>
                                    </h3>
                                    <div class=" d-flex mb-2">
                                        <div class=" text-body-tertiary me-2">{{ $postNew[$count]->user->name }}</div>
                                        <div
                                            class=" text-body-tertiary">{{ $postNew[$count]->created_at->format('d/m/Y') }}</div>
                                    </div>
                                    <div class="meta-description">
                                        {{ $postNew[$count]->meta_description }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @php $count++ @endphp
                        @break
                    @endwhile


                    <div class="col-12 col-md-12 col-lg-5">
                        <div class="featured-right border rounded bg-white">
                            @while($count < count($postNew))
                                <div class="row mb-3">
                                    <div class="col-4">
                                        <div class="box-img">
                                            <a href="{{ route('detailPost', $postNew[$count]->id) }}">
                                                <img class="rounded" src="storage/{{ $postNew[$count]->thumbnail }}"
                                                     alt="POST IMG">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-8">
                                        <h6 class="featured-title"><a class="text-black text-decoration-none"
                                                                      href="{{ route('detailPost', $postNew[$count]->id) }}">{{ $postNew[$count]->title }}</a>
                                        </h6>
                                        <div class=" d-flex">
                                            <div
                                                class=" text-body-tertiary me-2">{{ $postNew[$count]->user->name }}</div>
                                            <div
                                                class=" text-body-tertiary">{{ $postNew[$count]->created_at->format('d/m/Y') }}</div>
                                        </div>

                                    </div>
                                </div>
                                @php $count++ @endphp
                            @endwhile
                        </div>
                    </div>

                </div>
            </div>


            @foreach($categoryPost as $category)
                <div class="list-post">
                    <h4>{{ $category->name }}</h4>
                    <div class="row gy-3">
                        @foreach($category->post->take(4) as $item)
                            @include('includes.list-post')
                        @endforeach

                    </div>
                </div>
            @endforeach



            @include('includes.category-post')


        </div>

    </div>
@endsection
@extends('index')
