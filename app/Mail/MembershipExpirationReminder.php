<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Membership;

class MembershipExpirationReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $membership; // Holds membership data

    /**
     * Create a new message instance.
     */
    public function __construct(Membership $membership)
    {
        $this->membership = $membership; // Pass membership data to the email
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->from(config('mail.from.address')) // Use configured sender email
                    ->subject('Your Membership is About to Expire!') // Email subject
                    ->view('emails.membership_expiration_reminder') // Blade template for the email
                    ->with([
                        'membership' => $this->membership, // Pass membership data to the template
                    ]);
    }
}
