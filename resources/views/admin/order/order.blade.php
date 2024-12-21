@extends('admin.layout.app')

@section('content')
    <style>
        .truncate {
            max-width: 250px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }

        .profile-row {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 5px;
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
                                <h3 class="mb-0">Orders Info</h3>
                            </div>
                        </div>
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <form action="{{ route('orders.index') }}" method="get" class="d-flex">
                                        <!-- Updated to single 'name' field for searching full name -->
                                        <input type="text" name="user_name"
                                            value="{{ old('user_name', $userNameSearch) }}" placeholder="Name"
                                            class="form-control me-2">
                                        <input type="text" name="user_email"
                                            value="{{ old('user_email', $userEmailSearch) }}" placeholder="Email"
                                            class="form-control me-2">
                                        <button type="submit" class="btn btn-dark">Search</button>
                                        <button type="button" class="btn btn-outline-dark ms-2"><a
                                                href="{{ route('orders.index') }}"
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
                                            <th>User Name</th>
                                            <th>User Email</th>
                                            <th>Total Amount</th>
                                            <th>Payment Mode</th>
                                            <th>Status</th>
                                            <th>Created On</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse($orders as $order)
                                            <tr>
                                                <td>{{ $order->c_id }}</td>
                                                <td>{{ $order->user->name }}</td>
                                                <td class="truncate">{{ $order->user->email }}</td>
                                                <td>${{ $order->t_amount }}</td>
                                                <td>{{ $order->p_mode ?? 'Not Selected' }}</td>
                                                <td>
                                                    @if ($order->c_status == 3)
                                                        <span class="bg-success text-white p-1 rounded-2">Completed</span>
                                                    @elseif ($order->c_status == 2)
                                                        <span class="bg-warning text-white p-1 rounded-2">Processing</span>
                                                    @elseif ($order->c_status == 1)
                                                        <span class="bg-danger text-white p-1 rounded-2">Pending</span>
                                                    @endif
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($order->created_on)->format('d-m-Y H:i:s') }}
                                                </td>
                                                <td><button type="button" class="btn btn-sm btn-info"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#detailsModal{{ $order->c_id }}">View</button>
                                                </td>
                                            </tr>

                                            <!-- Details Modal -->
                                            <div class="modal fade" id="detailsModal{{ $order->c_id }}" tabindex="-1"
                                                aria-labelledby="detailsModalLabel{{ $order->c_id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title"
                                                                id="detailsModalLabel{{ $order->c_id }}">Order Details
                                                            </h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                                aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                @if ($order->cartItems->isNotEmpty())
                                                                    @foreach ($order->cartItems as $item)
                                                                        <div class="container my-3">
                                                                            <div
                                                                                class="row align-items-center border p-3 rounded shadow-sm">
                                                                                <!-- Image Section -->
                                                                                @php
                                                                                    // Get the first "H" priority image for the product
                                                                                    $image = $item->product->images->firstWhere(
                                                                                        'priority',
                                                                                        '1',
                                                                                    );
                                                                                @endphp
                                                                                <div class="col-md-4 text-center">
                                                                                    <img src="{{ url(str_replace('D:\\warzan\\first-app\\public\\', '', $image->f_path)) }}"
                                                                                        alt="Product Image"
                                                                                        class="img-fluid rounded">
                                                                                </div>
                                                                                <!-- Details Section -->
                                                                                <div class="col-md-8">
                                                                                    <div class="row mb-1">
                                                                                        <div class="col-md-5 profile-row">
                                                                                            <strong>Product Name</strong>
                                                                                        </div>
                                                                                        <div class="col-md-7 profile-row">
                                                                                            {{ $item->product->p_name }}
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row mb-1">
                                                                                        <div class="col-md-5 profile-row">
                                                                                            <strong>Product Price</strong>
                                                                                        </div>
                                                                                        <div class="col-md-7 profile-row">
                                                                                            ${{ number_format($item->product->p_price, 2) }}
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="row mb-1">
                                                                                        <div class="col-md-5 profile-row">
                                                                                            <strong>Quantity</strong>
                                                                                        </div>
                                                                                        <div class="col-md-7 profile-row">
                                                                                            {{ $item->quantity }}</div>
                                                                                    </div>
                                                                                    <div class="row mb-1">
                                                                                        <div class="col-md-5 profile-row">
                                                                                            <strong>Total Product
                                                                                                Price</strong>
                                                                                        </div>
                                                                                        <div class="col-md-7 profile-row">
                                                                                            ${{ number_format($item->tp_price, 2) }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                    <div
                                                                        class="d-flex justify-content-end align-items-center">
                                                                        <p class="fw-bold">Total Amount:
                                                                            ${{ number_format($order->t_amount, 2) }}</p>
                                                                    </div>
                                                                @else
                                                                    <p class='text-muted'>No items found in the cart.</p>
                                                                @endif
                                                            </div>
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
                                    @if ($orders->onFirstPage())
                                        <li class="page-item disabled"><span class="page-link"
                                                style="border-radius: 5px;">Previous</span></li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $orders->previousPageUrl() }}"
                                                style="border-radius: 5px;">Previous</a>
                                        </li>
                                    @endif

                                    <!-- Pagination Numbers -->
                                    @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                                        <li class="page-item {{ $orders->currentPage() == $page ? 'active' : '' }}">
                                            <a class="page-link" href="{{ $url }}"
                                                style="border-radius: 5px;">{{ $page }}</a>
                                        </li>
                                    @endforeach

                                    @if ($orders->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $orders->nextPageUrl() }}"
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
@endsection
