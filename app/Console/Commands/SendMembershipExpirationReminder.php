<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\MembershipExpirationReminder;
use App\Models\Membership;
use Carbon\Carbon;

class SendMembershipExpirationReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membership:expiration-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to users whose memberships are about to expire';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get memberships that expire within the next 3 days
        $expiringMemberships = Membership::whereDate('end_date', '<=', now()->addDays(3))
                                         ->whereDate('end_date', '>=', now())
                                         ->get();
    
        if ($expiringMemberships->isEmpty()) {
            $this->info('No memberships expiring within the next 3 days.');
            return;
        }
    
        foreach ($expiringMemberships as $membership) {
            Mail::to($membership->user->email)->send(new MembershipExpirationReminder($membership));
            $this->info('Reminder sent to: ' . $membership->user->email);
        }
    
        $this->info('Reminder emails sent successfully.');
    }     
}
