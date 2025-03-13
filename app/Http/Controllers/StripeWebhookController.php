<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use App\Models\Membership;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Event;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Retrieve JSON data from Stripe
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            // Verify the authenticity of the Webhook
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\Exception $e) {
            Log::error("Webhook verification error: " . $e->getMessage());
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Log the event for debugging
        Log::info("Webhook received: " . $event->type);

        // Handle different types of events
        switch ($event->type) {
            case 'payment_intent.succeeded':  
                $this->handleSuccessfulPayment($event->data->object);
                break;
            case 'payment_intent.payment_failed':  
                $this->handleFailedPayment($event->data->object);
                break;
        }

        return response()->json(['status' => 'success'], 200);
    }

    private function handleSuccessfulPayment($paymentIntent)
    {
        Log::info("Successful payment: " . json_encode($paymentIntent));

        // Find the payment by transaction_reference
        $payment = Payment::where('transaction_reference', $paymentIntent->id)->first();
        if ($payment) {
            // Update the payment status
            $payment->status = 'paid';
            $payment->save();

            // Update amount_paid in memberships
            $membership = Membership::find($payment->membership_id);
            if ($membership) {
                $membership->amount_paid += $payment->amount;
                if ($membership->amount_paid >= $membership->price) {
                    $membership->payment_status = 'paid';
                }
                $membership->save();
            }
        }
    }

    private function handleFailedPayment($paymentIntent)
    {
        Log::info("Failed payment: " . json_encode($paymentIntent));

        // Find the payment by transaction_reference
        $payment = Payment::where('transaction_reference', $paymentIntent->id)->first();
        if ($payment) {
            // Update status to failed
            $payment->status = 'failed';
            $payment->save();
        }
    }
}
