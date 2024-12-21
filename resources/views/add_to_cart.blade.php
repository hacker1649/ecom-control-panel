@extends('layout.app')

@section('content')
    <style>
        .cart-item {
            display: flex;
            align-items: center;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
        }

        .cart-item img {
            max-width: 150px;
            margin-right: 20px;
        }

        .cart-item-details {
            flex: 1;
        }

        #checkoutForm {
            display: none;
            margin-top: 50px;
        }

        .disabled {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>

    <div class="container" id="cart-section" style="margin-top: 40px; margin-bottom: 40px;">
        <input type="hidden" id="cart-status" value="{{ $cartItem->first()->cart->c_status ?? 0 }}">
        @if ($cartItem->count() > 0)
            <h4>Your Cart</h4>
            @foreach ($cartItem as $item)
                <div class="cart-item d-flex align-items-center justify-content-between mb-4">
                    <!-- Product Image -->
                    @php
                        $image = $item->product->images->firstWhere('priority', '1');
                    @endphp
                    <img src="{{ url(str_replace('D:\\warzan\\first-app\\public\\', '', $image->f_path)) }}"
                        alt="Product Image" class="cart-item-image rounded">

                    <!-- Product Details -->
                    <div class="cart-item-details flex-grow-1 mx-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold m-0">{{ $item->product->p_name }}</h5>
                            <!-- Remove Button -->
                            <form action="{{ route('cart.remove') }}" method="POST" class="ms-3">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="i_id" value="{{ $item->i_id }}">
                                <button type="submit" class="btn btn-danger remove-btn">Remove</button>
                            </form>
                        </div>
                        <p class="small">{{ $item->product->p_desc }}</p>
                        <p class="fw-bold">Price: ${{ number_format($item->product->p_price, 2) }}</p>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <!-- Quantity Controls -->
                            <div class="d-flex align-items-center">
                                <form action="{{ route('cart.update', $item->i_id) }}" method="POST" class="d-flex">
                                    @csrf
                                    @method('PATCH')

                                    <input type="hidden" name="cartItemId" value="{{ $item->i_id }}">

                                    <button type="submit" name="quantity" value="-1"
                                        class="btn btn-sm btn-primary quantity-btn">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" value="{{ $item->quantity }}" readonly
                                        class="form-control fw-bold text-center mx-2" style="width: 60px;">
                                    <button type="submit" name="quantity" value="1"
                                        class="btn btn-sm btn-primary quantity-btn">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </form>
                            </div>
                            <!-- Total Price -->
                            <div>
                                <p class="fw-bold m-0">Total Price: ${{ number_format($item->tp_price, 2) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Total Amount and Checkout -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <button id="proceedToCheckout" class="btn btn-dark">Proceed to Checkout</button>
                <h6 class="fw-bold">Grand Total: ${{ number_format($t_amount, 2) }}</h6>
            </div>

            <!-- Hidden Checkout Form -->
            <div id="checkoutForm" class="col-md-6 mx-auto">
                <div class="card shadow-lg">
                    <div class="card-header">
                        <h4 class="m-0">Checkout Form</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('update.user.profile') }}" method="POST">
                            @csrf

                            <!-- First Name and Last Name Fields -->
                            @php
                                $fullName = auth()->user()->name;
                                $nameParts = explode(' ', $fullName);
                                $f_name = $nameParts[0];
                                $l_name = isset($nameParts[1]) ? $nameParts[1] : ''; // Handle case where there might be only one name part
                            @endphp

                            <div class="row">
                                <div class="col-md-6 mb-5">
                                    <label for="firstName" class="form-label fw-bold">First Name</label>
                                    <input type="text" class="form-control" id="firstName" name="first_name"
                                        value="{{ $f_name }}" readonly />
                                </div>
                                <div class="col-md-6 mb-5">
                                    <label for="lastName" class="form-label fw-bold">Last Name</label>
                                    <input type="text" class="form-control" id="lastName" name="last_name"
                                        value="{{ $l_name }}" readonly />
                                </div>
                            </div>

                            <!-- Email Field -->
                            <div class="mb-5">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    value="{{ auth()->user()->email }}" readonly>
                            </div>

                            <!-- Address Field -->
                            <div class="mb-5">
                                <label for="address" class="form-label fw-bold">Address</label>
                                <input type="text" class="form-control" id="address" name="address"
                                    value="{{ auth()->user()->user_profile ? auth()->user()->user_profile->address : '' }}" placeholder="Enter your address">
                            </div>

                            <!-- Phone Number Field -->
                            <div class="mb-5">
                                <label for="phone" class="form-label fw-bold">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone"
                                    value="{{ auth()->user()->user_profile ? auth()->user()->user_profile->phone : '' }}"
                                    placeholder="Enter your phone number">
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-success w-100">Proceed to Payment</button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart -->
            <div class="d-flex flex-column justify-content-center align-items-center">
                <p>Your cart is empty.</p>
                <a href="/" class="btn btn-dark">Continue Shopping...</a>
            </div>
        @endif
    </div>

    <script>
        // Get the cart status from the hidden input or wherever you store it
        const cartStatus = parseInt(document.getElementById('cart-status').value);

        // If cart status is 2, disable the buttons and show the checkout form
        if (cartStatus === 2) {
            // Disable buttons
            document.querySelectorAll('.remove-btn, .quantity-btn, #proceedToCheckout').forEach(button => {
                button.classList.add('disabled');
            });
            // Show the checkout form
            document.getElementById('checkoutForm').style.display = 'block';
        } else {
            // Show the cart items with checkout button
            document.getElementById('checkoutForm').style.display = 'none';

            // Add event listener for the checkout button if cart status is not 2
            document.getElementById('proceedToCheckout').addEventListener('click', function() {
                // Disable buttons
                document.querySelectorAll('.remove-btn, .quantity-btn, #proceedToCheckout').forEach(button => {
                    button.classList.add('disabled');
                });
                // Optionally, update the cart status to processing or any other state (if needed)
                updateCartStatusToProcessing(); // Make sure this function is defined elsewhere in your code
                // Show the checkout form
                document.getElementById('checkoutForm').style.display = 'block';
            });
        }

        function updateCartStatusToProcessing() {
            $.ajax({
                url: '{{ route('update.cart.status') }}', // You'll need to create this route in your web.php
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    c_status: 2
                },
                success: function(response) {
                    if (response.status === 'success') {
                        console.log("Cart status updated to 2");
                    } else {
                        alert("Failed to update cart status.");
                    }
                },
                error: function() {
                    alert("An error occurred while updating the cart status.");
                }
            });
        }
    </script>
@endsection
