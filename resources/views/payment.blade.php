<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pay for Membership</title>

    <!-- Include Stripe.js -->
    <script src="https://js.stripe.com/v3/"></script>

    <style>
        .container { max-width: 400px; margin: auto; } /* Center the content */
        .hidden { display: none; } /* Hide elements by default */
    </style>
</head>
<body>

@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="col-md-6">

        <!-- Page title with increased size and bold font -->
        <h2 class="fw-bold mb-3" style="font-size: 28px;">Membership Payment</h2>

        <!-- Display membership pricing details -->
        <p><strong>Total Price:</strong> ${{ number_format($membership->price, 2) }}</p>
        <p><strong>Amount Paid:</strong> ${{ number_format($membership->amount_paid, 2) }}</p>

        @php
            // Calculate the remaining amount to be paid
            $amount_due = max($membership->price - $membership->amount_paid, 0);
        @endphp

        <p><strong>Amount Due:</strong> ${{ number_format($amount_due, 2) }}</p>

        <!-- Show payment form only if there is an outstanding amount -->
        @if($amount_due > 0)
            <form id="payment-form">
                <!-- Stripe Card Element -->
                <div id="card-element" class="form-control mb-3"></div>
                
                <!-- Payment button -->
                <button id="submit-button" class="btn btn-primary">
                    Pay ${{ number_format($amount_due, 2) }}
                </button>
            </form>
        @else
            <!-- Display a message when membership is fully paid -->
            <p class="text-success">Membership is fully paid!</p>
        @endif

        <!-- Display payment messages -->
        <p id="payment-message" class="text-danger mt-3 hidden"></p>
    </div>
</div>

<!-- Include Stripe.js -->
<script src="https://js.stripe.com/v3/"></script>

<script>
    var stripe = Stripe("{{ env('STRIPE_KEY') }}"); // Initialize Stripe with the public key
    var elements = stripe.elements();
    var cardElement = elements.create("card"); // Create card input field
    cardElement.mount("#card-element"); // Mount card element in the form

    var form = document.getElementById("payment-form");
    var paymentMessage = document.getElementById("payment-message");

    if (form) {
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent default form submission
            
            // Create a Payment Method using the card details
            stripe.createPaymentMethod({
                type: "card",
                card: cardElement
            }).then(function(result) {
                if (result.error) {
                    // Display error message if payment fails
                    paymentMessage.textContent = result.error.message;
                    paymentMessage.classList.remove("hidden");
                } else {
                    // Send payment request to the backend
                    fetch("{{ route('payment.process') }}", {
                        method: "POST",
                        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                        body: JSON.stringify({ 
                            payment_method_id: result.paymentMethod.id, 
                            membership_id: {{ $membership->id }},
                            amount_due: {{ $amount_due }} // Pass amount to be paid
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Display success message
                            paymentMessage.textContent = "Payment successful!";
                            paymentMessage.classList.remove("hidden");
                        } else {
                            // Display failure message
                            paymentMessage.textContent = "Payment failed: " + data.message;
                            paymentMessage.classList.remove("hidden");
                        }
                    });
                }
            });
        });
    }
</script>

@endsection

</body>
</html>
