@extends('admin.layout.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y p-0">
        <div class="row">
            <div class="col mb-4 order-0">
                <div class="d-flex align-items-end row">
                    <div class="col">
                        <div class="card mb-4 mx-auto">
                            <div class="card-header mb-3 d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Edit Product Form</h3>
                            </div>
                            <div class="card-body">
                                <!-- Form -->
                                <form id="dataForm" method="post"
                                    action="{{ route('product.update', $product->p_id) }}">
                                    @csrf
                                    @method('PUT') <!-- Use PUT or PATCH as required by your route -->
                                    <div class="mb-5">
                                        <label for="p_name" class="form-label fw-bold text-uppercase">Product Name</label>
                                        <span class="error">*</span>
                                        <input type="text" class="form-control" id="p_name" name="p_name"
                                            value="{{ $product->p_name }}" placeholder="Enter product name" />
                                        <span class="error">
                                            @error('p_name')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="mb-5">
                                        <label for="p_desc" class="form-label fw-bold text-uppercase">Product
                                            Description</label>
                                        <span class="error">*</span>
                                        <textarea class="form-control" id="p_desc" name="p_desc" rows="3" placeholder="Enter product description">{{ $product->p_desc }}</textarea>
                                        <span class="error">
                                            @error('p_desc')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="mb-5">
                                        <label for="p_price" class="form-label fw-bold text-uppercase">Product
                                            Price</label>
                                        <span class="error">*</span>
                                        <input type="text" class="form-control" id="p_price" name="p_price"
                                            value="{{ $product->p_price }}" placeholder="Enter product price" />
                                        <span class="error">
                                            @error('p_price')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="mb-5">
                                        <label for="category" class="form-label fw-bold text-uppercase">Category</label>
                                        <span class="error">*</span>
                                        <select class="form-select" id="category" name="category">
                                            <option value="" {{ $product->category->c_name == '' ? 'selected' : '' }}>Choose a
                                                Category</option>
                                            <option value="Electronics"
                                                {{ $product->category->c_name == 'Electronics' ? 'selected' : '' }}>
                                                Electronics</option>
                                            <option value="Books" {{ $product->category->c_name == 'Books' ? 'selected' : '' }}>Books
                                            </option>
                                            <option value="Furniture"
                                                {{ $product->category->c_name == 'Furniture' ? 'selected' : '' }}>
                                                Furniture</option>
                                            <option value="Clothing" {{ $product->category->c_name == 'Clothing' ? 'selected' : '' }}>
                                                Clothing</option>
                                            <option value="Toys" {{ $product->category->c_name == 'Toys' ? 'selected' : '' }}>Toys
                                            </option>
                                        </select>
                                        <span class="error">
                                            @error('category')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="mb-5">
                                        <label for="popularity" class="form-label fw-bold text-uppercase">Product
                                            Popularity</label>
                                        <span class="error">*</span>
                                        <select class="form-select" id="popularity" name="popularity">
                                            <option value="" {{ ($product->popularity == 1 ? 'Featured' : 'Non-Featured') == '' ? 'selected' : '' }}>Choose
                                                Popularity</option>
                                            <option value="Featured"
                                            {{ ($product->popularity == 1 ? 'Featured' : 'Non-Featured') == 'Featured' ? 'selected' : '' }}>Featured</option>
                                            <option value="Non-Featured"
                                            {{ ($product->popularity == 1 ? 'Featured' : 'Non-Featured') == 'Non-Featured' ? 'selected' : '' }}>Non-Featured
                                            </option>
                                        </select>
                                        <span class="error">
                                            @error('popularity')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <!-- Additional Fields here with similar pattern -->

                                    <button type="submit" class="btn btn-primary">Save</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
