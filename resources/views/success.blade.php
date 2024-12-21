@extends('layout.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y d-flex justify-content-center align-items-center mt-5">
        <div class="card shadow-lg p-5" style="width: 50%; max-width: 700px;">
            <div class="success-container text-center">
                <h2>Order Placed Successfully!</h2>
                <p>Thank you <strong>{{ Auth::user()->name }}</strong>! for shopping with us. Your order has been confirmed!</p>

                <h4>Your Order Details:</h4>

                <div class="d-flex justify-content-center align-items-center">
                    <table class="table table-bordered table-hover w-100">
                        <thead>
                            <tr>
                                <th class="fw-bold text-capitalize">Product Name</th> 
                                <th class="fw-bold text-capitalize">Quantity</th>
                                <th class="fw-bold text-capitalize">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cart->cartItems as $item)
                                <tr>
                                    <td>{{ $item->product->p_name }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>${{ number_format($item->p_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="order-details mt-5">
                    <p><strong>Total Amount: </strong><span class="text-danger fw-bold">${{ number_format($cart->t_amount, 2) }}</span></p>
                    <p><strong>Payment Method: </strong><span class="text-info fw-bold">{{ $paymentMethod }}</span></p>
                </div>

                <div class="btn-group mt-4">
                    <a href="{{ route('website') }}" class="btn btn-dark">Go Back to Shopping</a>
                </div>

                <p class="mt-4">If you have any questions, please contact our <a href="#">support team</a>.</p>
            </div>
        </div>
    </div>
@endsection
