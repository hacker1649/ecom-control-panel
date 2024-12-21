@extends('admin.layout.app')

@section('content')
    <style>
        .truncate {
            max-width: 250px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y p-0">
        <div class="row">
            <div class="col mb-4 order-0">
                <div class="card">

                    @if (session('message'))
                        <div id="message" class="alert alert-success">{{ session('message') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li style="list-style: none;">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="d-flex align-items-end row">
                        <div class="col">
                            <div class="card-header mb-3 d-flex justify-content-between align-items-center">
                                <h3 class="mb-0">Products Info</h3>
                                <a href="{{ route('product.create') }}"><button type="button" class="btn btn-primary">Add
                                        Product</button></a>
                            </div>
                        </div>
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <form action="{{ route('product.index') }}" method="get" class="d-flex">
                                        <!-- Updated to single 'name' field for searching full name -->
                                        <input type="text" name="product_name"
                                            value="{{ old('product_name', $productNameSearch) }}" placeholder="Product Name"
                                            class="form-control me-2">
                                        <input type="text" name="category_name"
                                            value="{{ old('category_name', $categoryNameSearch) }}"
                                            placeholder="Category Name" class="form-control me-2">
                                        <input type="text" name="price" value="{{ old('price', $priceSearch) }}"
                                            placeholder="Product Price" class="form-control me-2">
                                        <button type="submit" class="btn btn-dark">Search</button>
                                        <button type="button" class="btn btn-outline-dark ms-2"><a
                                                href="{{ route('product.index') }}"
                                                class="text-decoration-none text-reset">Reset</a></button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive mar">
                                <table class="table table-striped table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Product Name</th>
                                            <th>Product Description</th>
                                            <th>Product Price</th>
                                            <th>Category</th>
                                            <th>Uploads</th>
                                            <th>Created On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse($products as $product)
                                            <tr>
                                                <td>{{ $product->p_id }}</td>
                                                <td class="truncate">{{ $product->p_name }}</td>
                                                <td class="truncate">{{ $product->p_desc }}</td>
                                                <td>${{ $product->p_price }}</td>
                                                <td>{{ $product->category->c_name }}</td>
                                                <td>{{ $product->images_count }} image(s)</td>
                                                <td>{{ \Carbon\Carbon::parse($product->created_on)->format('d-m-Y H:i:s') }}
                                                </td>
                                                <td>
                                                    <a
                                                        href="{{ route('product.edit', $product->encrypted_id) }}"class="btn btn-warning btn-sm">Edit</a>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="confirmDelete({{ $product->p_id }})">
                                                        Block
                                                    </button>
                                                    <button class="btn btn-secondary btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#uploadImagesModal"
                                                        onclick="setUploadId({{ $product->p_id }})">
                                                        Upload
                                                    </button>

                                                </td>
                                            </tr>

                                            <!-- Modal for Confirmation -->
                                            <div class="modal fade" id="confirmDeleteModal" tabindex="-1"
                                                aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="confirmDeleteModalLabel">Warning?
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            Are you sure you want to block this product?
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary"
                                                                data-bs-dismiss="modal">Cancel</button>
                                                            <form id="deleteForm" method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">Block</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Upload Images Modal -->
                                            <div class="modal fade" id="uploadImagesModal" tabindex="-1"
                                                aria-labelledby="uploadImagesModalLabel" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="uploadImagesModalLabel">Upload
                                                                Images Form</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <form id="uploadImagesForm"
                                                                action="{{ route('product.uploadImages') }}" method="POST"
                                                                enctype="multipart/form-data">
                                                                @csrf
                                                                <!-- Hidden input to store the product_id dynamically -->
                                                                <input type='hidden' id="product_id" name="product_id">

                                                                <div class="modal-body p-0">
                                                                    <label for="images" class="form-label">Upload
                                                                        Images</label>
                                                                    <input class="form-control" type="file"
                                                                        name="images[]" multiple accept="image/*">
                                                                    <small class="form-text text-muted">You can upload a
                                                                        maximum of 4 images.</small>
                                                                </div>
                                                                <div class="modal-footer p-0">
                                                                    <button type="submit"
                                                                        class="btn btn-primary">Upload</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        @empty
                                            <tr>
                                                <td colspan="8">No users found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination Links -->
                            <nav class="d-flex justify-content-end mt-5 mb-5">
                                <ul class="pagination">
                                    @if ($products->onFirstPage())
                                        <li class="page-item disabled"><span class="page-link"
                                                style="border-radius: 5px;">Previous</span></li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $products->previousPageUrl() }}"
                                                style="border-radius: 5px;">Previous</a>
                                        </li>
                                    @endif

                                    <!-- Pagination Numbers -->
                                    @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                                        <li class="page-item {{ $products->currentPage() == $page ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $url }}"
                                                style="border-radius: 5px;">{{ $page }}</a>
                                        </li>
                                    @endforeach

                                    @if ($products->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $products->nextPageUrl() }}"
                                                style="border-radius: 5px;">Next</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled"><span class="page-link"
                                                style="border-radius: 5px;">Next</span></li>
                                    @endif
                                </ul>
                            </nav>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(productId) {
            // Set the form's action URL dynamically
            const form = document.getElementById('deleteForm');
            form.action = "{{ route('product.destroy', '') }}/" + productId;

            // Show the modal
            const deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            deleteModal.show();
        }
    </script>

    <script>
        function setUploadId(productId) {
            document.getElementById('product_id').value = productId;
        }
    </script>
@endsection
