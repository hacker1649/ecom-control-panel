@extends('layout.app')

@section('content')
    <style>
        .product-gallery {
            position: relative;
            width: 100%;
            max-width: 600px;
            height: 500px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .product-gallery img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .small-image {
            width: 90px;
            height: auto;
            object-fit: cover;
            margin-right: 10px;
            cursor: pointer;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .product-card {
            border: 1px solid #f1f1f1;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
            padding: 20px;
            background-color: #fff;
            transition: all 0.3s ease;
        }

        .price {
            color: red;
            font-weight: bold;
        }

        .small-images-container {
            display: flex;
            overflow-x: auto;
            gap: 15px;
            padding: 10px 0;
        }

        .small-images-container {
            display: flex;
            overflow-x: auto;
            gap: 15px;
            padding: 10px 0;
        }

        /* Basic styling for the price */
        .product-price {
            margin-top: 15px;
            font-size: 20px;
            font-weight: bold;
            color: #333;
            /* dark color for contrast */
        }

        /* Price in USD, can use font size adjustments for different devices */
        .price {
            color: #e74c3c;
            /* Red color for attention */
        }
    </style>

    <div class="container" style="margin-top: 30px;">
        <div class="row">
            <!-- Product Main Image -->
            <div class="col-md-6 d-flex justify-content-center mb-4 mb-md-0">
                <div class="product-gallery">
                    <img id="main-image"
                        src="{{ url(str_replace('D:\\warzan\\first-app\\public\\', '', $mainImage->f_path)) }}"
                        class="main-image" alt="Product Image">
                </div>
            </div>

            <!-- Product Details Section -->
            <div class="col-md-6">
                <div class="product-card">
                    <h3 class="fw-bold mb-4">{{ $product->p_name }}</h3>
                    <h3 class="price fs-5 fw-bold">${{ number_format($product->p_price, 2) }}</h3>
                    <p><strong>Category:</strong> {{ $product->category->c_name }}</p>
                    <p><strong>Popularity:</strong> {{ $product->popularity == 1 ? 'Featured' : 'Non-Featured' }}</p>
                    <p><strong>Description:</strong> {!! nl2br(e($product->p_desc)) !!}</p>
                </div>

                <div class="mt-4">
                    <h5 class="m-1">Other Images</h5>
                    <div class="small-images-container">
                        <!-- Add the main image as a clickable thumbnail -->
                        <img src="{{ url(str_replace('D:\\warzan\\first-app\\public\\', '', $mainImage->f_path)) }}"
                            class="small-image selected" alt="Main Image Thumbnail"
                            data-full-size="{{ url($mainImage->f_path) }}"
                            onclick="changeMainImage('{{ str_replace('D:\\warzan\\first-app\\public\\', '', $mainImage->f_path) }}')">

                        @foreach ($otherImages as $image)
                            <img src="{{ url(str_replace('D:\\warzan\\first-app\\public\\', '', $image->f_path)) }}"
                                class="small-image" alt="Product Thumbnail" data-full-size="{{ url($image->f_path) }}"
                                onclick="changeMainImage('{{ str_replace('D:\\warzan\\first-app\\public\\', '', $image->f_path) }}')">
                        @endforeach
                    </div>
                </div>

                <form action="{{ route('cart.add', ['product_id' => $product->p_id]) }}" method="POST" id="add-to-cart-form">
                    @csrf
                    <button type="submit" class="btn btn-primary mt-3 w-50">
                        Add to Cart
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Function to change the main image when an additional image is clicked
        function changeMainImage(imagePath) {
            var mainImage = document.getElementById("main-image");
            mainImage.src = "../../" + imagePath;
        }
    </script>
@endsection
