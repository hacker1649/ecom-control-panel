@extends('layout.app')

@section('content')
    <!-- including paypal javascript sdk -->
    <script
        src="https://www.paypal.com/sdk/js?client-id=AfF2yhhqglEvXPHhsVgly66Ma4u3CkFtpcp2G3ZS2OVhEdhU7A2e4oTwV-jh8Jw_2-ZWCTy5KyKEGnIV&currency=USD">
    </script>

    <div class="container d-flex justify-content-center align-items-center" style="margin-top: 180px;">
        <div id="paymentForm" style="display: block; margin-top: 20px;" class="col-md-6 mb-5">
            <div class="card shadow-lg">
                <div class="card-header text-white">
                    <h4 class="m-0">Payment Form</h4>
                </div>
                <div class="card-body">
                    <p class="card-text">Hello, <strong>{{ Auth::user()->name }}</strong>! Please review your payment details
                        carefully before proceeding. Make sure to use a secure and valid payment method to complete your
                        transaction smoothly.</p>
                    <p class="fw-bold">Total Amount: <span id="grand-total" class="text-danger">${{ $totalAmount }}</span>
                    </p>
                    <form method="POST" action="">
                        @csrf
                        <!-- Payment Method Field -->
                        <div class="mb-3">
                            <label for="paymentMethod" class="form-label fw-bold">Payment Method</label>
                            <select class="form-select" id="paymentMethod" name="payment_method"
                                onchange="togglePayPalButton()">
                                <option value="" selected>Choose Payment Method</option>
                                <option value="cash_on_delivery"
                                    {{ old('payment_method') == 'cash_on_delivery' ? 'selected' : '' }}>Cash on Delivery
                                </option>
                                <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal
                                </option>
                            </select>
                            <div id="paymentMethodError" class="text-danger"></div>
                        </div>

                        <!-- PayPal Button -->
                        <div id="paypal-button-container" class="mt-5 mb-5" style="display: none;"></div>

                        <!-- Confirm Modal -->
                        <div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="confirmModalLabel">Confirmation</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        Are you sure you want to confirm this order?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary me-5" data-bs-dismiss="modal"
                                            aria-label="Close">Cancel</button>
                                        <button type="button" class="btn btn-primary"
                                            id="confirmCashOnDelivery">Confirm</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('paymentMethod').addEventListener('change', function() {
            if (this.value === 'cash_on_delivery') {
                // Trigger the modal
                let modal = new bootstrap.Modal(document.getElementById('confirmModal'));
                modal.show();
            }
        });
    </script>

    <script>
        document.getElementById('confirmCashOnDelivery').addEventListener('click', function() {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route('order.success.cash') }}';
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        });
    </script>

    <script>
        // Function to toggle PayPal button visibility
        function togglePayPalButton() {
            const paymentMethod = document.getElementById("paymentMethod").value;
            const paypalButtonContainer = document.getElementById("paypal-button-container");

            if (paymentMethod === "paypal") {
                paypalButtonContainer.style.display = "block"; // Show PayPal button
            } else {
                paypalButtonContainer.style.display = "none"; // Hide PayPal button
            }
        }

        // Fetch PayPal order details from the backend
        async function fetchPaypalOrder() {
            try {
                const response = await fetch("{{ route('paypal.order.create') }}", {
                    method: "GET",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });

                const data = await response.json();

                if (data.status === 'success') {
                    return data.order; // PayPal order structure
                } else {
                    console.error('Error:', data.message);
                    alert("Failed to fetch PayPal order: " + data.message);
                    return null;
                }
            } catch (error) {
                console.error('Fetch Error:', error);
                alert("An error occurred while fetching the order details.");
                return null;
            }
        }

        paypal.Buttons({
            createOrder: async function(data, actions) {
                const orderData = await fetchPaypalOrder();

                if (orderData) {
                    return actions.order.create(orderData);
                } else {
                    alert("Unable to create PayPal order. Please try again.");
                    return null;
                }
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    // Send transaction details to the backend
                    fetch("{{ route('order.success.paypal') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                transaction_details: details
                            })
                        }).then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = "{{ route('order.success') }}";
                            } else {
                                alert('Failed to save transaction.');
                            }
                        }).catch(error => {
                            console.error('Error:', error);
                            alert('An error occurred while processing the transaction.');
                        });
                });
            },
            onCancel: function(data) {
                alert('Transaction was cancelled.');
            },
            onError: function(err) {
                console.error('PayPal Error:', err);
                alert('An error occurred during the transaction.');
            }
        }).render('#paypal-button-container');
    </script>
@endsection
