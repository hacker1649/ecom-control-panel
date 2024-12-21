@extends('layout.app')

@section('content')
    <style>
        .truncate {
            max-height: 200px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: lightgrey;
            opacity: 0.3;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            transition: background-color 0.3s ease;
        }

        .carousel-control-prev-icon:hover,
        .carousel-control-next-icon:hover {
            background-color: lightgrey;
            opacity: 0.3;

        }

        .card {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
    </style>

    <div class="container">
        <div class="mb-5">
            <!-- Categories Carousel View -->
            <div id="categoryCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($categories->chunk(1) as $chunk)
                        <div class="carousel-item @if ($loop->first) active @endif">
                            <div class="row">
                                @foreach ($chunk as $category)
                                    <div class="col-md-12">
                                        <a href="#{{ strtolower($category->c_name) }}Carousel">
                                            <img src="{{ $category->c_path }}" alt="{{ $category->c_name }}"
                                                style="width: 100%; height: 600px; object-fit: cover; cursor: pointer;">
                                            <div class="carousel-caption d-none d-md-block">
                                                <h5>{{ $category->c_name }}</h5>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination bars -->
                <div class="carousel-indicators">
                    @foreach ($categories as $index => $category)
                        <button type="button" data-bs-target="#carouselExample" data-bs-slide-to="{{ $index }}"
                            class="{{ $index == 0 ? 'active' : '' }}" aria-current="true"
                            aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#categoryCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#categoryCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>
        </div>


        <!-- Hot/Featured Products -->
        <div class="mb-5">
            <div class="py-3 pe-4">
                <h5 class="m-0">Featured Products</h5>
            </div>
            <div id="productCarousel" class="carousel slide product-carousel" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach ($hotProducts->chunk(3) as $chunk)
                        <div class="carousel-item @if ($loop->first) active @endif">
                            <div class="row">
                                @foreach ($chunk as $product)
                                    <div class="col-md-4 product-carousel-item">
                                        <div class="card">
                                            <!-- Badge for Featured/Hot Product -->
                                            <span
                                                class="badge bg-danger position-absolute top-0 start-0 m-2 fs-6 py-2 px-3">Hot</span>
                                            @if ($product->images->isNotEmpty())
                                                <!-- Display the first image of the product with H priority -->
                                                <img src="{{ url(str_replace('D:\\warzan\\first-app\\public\\', '', $product->images->first()->f_path)) }}"
                                                    class="card-img-top" alt="{{ $product->p_name }}"
                                                    style="height: 300px; object-fit: cover;">
                                            @else
                                                <!-- Display a default image if no images exist for the product -->
                                                <img src="{{ url('images/default-product.jpg') }}" class="card-img-top"
                                                    alt="{{ $product->p_name }}">
                                            @endif
                                            <div class="card-body">
                                                <h5 class="card-title text-dark text-decoration-underline">
                                                    {{ $product->p_name }}</h5>
                                                <p class="card-price fs-5 text-success fw-bold">${{ $product->p_price }}
                                                </p>
                                                <p class="card-text text-muted truncate">{{ $product->p_desc }}</p>
                                                <a href="{{ route('details', ['productId' => $product->p_id]) }}"
                                                    class="btn btn-primary w-100">Show Details</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>

    <!-- All Products -->
    <div class="container">
        @foreach (['electronics', 'books', 'furniture', 'clothing', 'toys'] as $category)
            <div class="mb-5">
                <div id="{{ $category }}Carousel" class="carousel slide product-carousel" data-bs-ride="carousel">
                    <div class="py-3 pe-4">
                        <h5 class="m-0">{{ ucfirst($category) }} Products</h5>
                    </div>
                    <div class="carousel-inner">
                        @foreach (${$category . 'Products'}->chunk(3) as $chunk)
                            <div class="carousel-item @if ($loop->first) active @endif">
                                <div class="row">
                                    @foreach ($chunk as $product)
                                        <div class="col-md-4 product-carousel-item">
                                            <div class="card">
                                                @php
                                                    // Get the first "H" priority image for the product
                                                    $image = $product->images->firstWhere('priority', '1');
                                                @endphp
                                                @if ($image)
                                                    <img src="{{ url(str_replace('D:\\warzan\\first-app\\public\\', '', $image->f_path)) }}"
                                                        class="card-img-top" alt="{{ $product->p_name }}"
                                                        style="height: 300px; object-fit: cover;">
                                                @else
                                                    <img src="{{ url('images/default-product.jpg') }}" class="card-img-top"
                                                        alt="{{ $product->p_name }}">
                                                @endif
                                                <div class="card-body">
                                                    <h5 class="card-title text-dark text-decoration-underline">
                                                        {{ $product->p_name }}</h5>
                                                    <p class="card-price fs-5 text-success fw-bold">
                                                        ${{ $product->p_price }}
                                                    </p>
                                                    <p class="card-text text-muted truncate">{{ $product->p_desc }}</p>
                                                    <a href="{{ route('details', ['productId' => $product->p_id]) }}"
                                                        class="btn btn-primary w-100">Show Details</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#{{ $category }}Carousel"
                        data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#{{ $category }}Carousel"
                        data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endsection
