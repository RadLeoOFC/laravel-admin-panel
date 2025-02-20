<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Membership;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller {
    // Payment form
    public function showPaymentForm($membership_id) {
        $membership = Membership::findOrFail($membership_id);
        return view('payment', compact('membership'));
    }

    // Processing payment via Stripe Elements
    public function processPayment(Request $request) {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    
        try {
            // Retrieve membership data
            $membership = Membership::findOrFail($request->membership_id);
    
            // Calculate the amount due
            $amount_due = max($membership->price - $membership->amount_paid, 0);
    
            // Ensure the amount is greater than 0
            if ($amount_due <= 0) {
                return response()->json(['success' => false, 'message' => 'No payment required. Membership is already fully paid.']);
            }
    
            // Create a PaymentIntent with the required amount
            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount' => $amount_due * 100, // Amount in cents
                'currency' => 'usd',
                'payment_method' => $request->payment_method_id,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'confirm' => true,
            ]);
    
            // Store the payment in the database
            Payment::create([
                'user_id' => auth()->id(),
                'membership_id' => $membership->id,
                'amount' => $amount_due,
                'status' => 'paid',
                'payment_method' => 'stripe',
                'transaction_reference' => $paymentIntent->id,
                'response_data' => json_encode($paymentIntent),
            ]);
    
            // Update membership data
            $membership->update([
                'payment_status' => 'paid',
                'payment_method' => 'stripe',
                'transaction_reference' => $paymentIntent->id,
                'amount_paid' => $membership->amount_paid + $amount_due // Add the amount to the already paid amount
            ]);
    
            return response()->json(['success' => true, 'message' => 'Payment successful!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Payment failed: ' . $e->getMessage()]);
        }
    }       
}
