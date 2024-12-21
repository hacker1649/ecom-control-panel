@extends('admin.layout.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y p-0">
        <div class="row">
            <div class="col mb-4 order-0">
                <div class="d-flex align-items-end row">
                    <div class="col">
                        <div class="card mb-4 mx-auto">
                            <div class="card-header mb-3 d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Add Product Form</h3>
                            </div>
                            <div class="card-body">
                                <!-- Form -->
                                <form id="dataForm" method="post" action="{{ route('product.store') }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-5">
                                        <label for="p_name" class="form-label fw-bold text-uppercase">Product Name</label>
                                        <span class="error">*</span>
                                        <input type="text" class="form-control" id="p_name" name="p_name"
                                            value="{{ old('p_name') }}" placeholder="Enter product name" />
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
                                        <textarea class="form-control" id="p_desc" name="p_desc" rows="3" placeholder="Enter product description">{{ old('p_desc') }}</textarea>
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
                                            value="{{ old('p_price') }}" placeholder="Enter product price" />
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
                                            <option value="" {{ old('category') == '' ? 'selected' : '' }}>Choose a
                                                Category</option>
                                            <option value="Electronics"
                                                {{ old('category') == 'Electronics' ? 'selected' : '' }}>
                                                Electronics</option>
                                            <option value="Books" {{ old('category') == 'Books' ? 'selected' : '' }}>Books
                                            </option>
                                            <option value="Furniture"
                                                {{ old('category') == 'Furniture' ? 'selected' : '' }}>
                                                Furniture</option>
                                            <option value="Clothing" {{ old('category') == 'Clothing' ? 'selected' : '' }}>
                                                Clothing</option>
                                            <option value="Toys" {{ old('category') == 'Toys' ? 'selected' : '' }}>Toys
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
                                            <option value="" {{ old('popularity') == '' ? 'selected' : '' }}>Choose
                                                Popularity</option>
                                            <option value="Featured"
                                                {{ old('popularity') == 'Featured' ? 'selected' : '' }}>Featured</option>
                                            <option value="Non-Featured"
                                                {{ old('popularity') == 'Non-Featured' ? 'selected' : '' }}>Non-Featured
                                            </option>
                                        </select>
                                        <span class="error">
                                            @error('popularity')
                                                {{ $message }}
                                            @enderror
                                        </span>
                                    </div>

                                    <div class="mb-5">
                                        <label for="images" class="form-label fw-bold text-uppercase">Upload Product
                                            Image</label>
                                        <span class="error">*</span>
                                        <input class="form-control" type="file" id="images" name="images">
                                        <small class="form-text text-muted">You can upload only one image.<br></small>
                                        <span class="error">
                                            @error('images')
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
