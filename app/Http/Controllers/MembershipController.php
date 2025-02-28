<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Membership;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MembershipCreated;
use Carbon\Carbon;

class MembershipController extends Controller
{
    /**
     * MembershipController manages user memberships.
     * This constructor ensures that only admin users can manage all memberships.
     */
    public function __construct()
    {
        // Only admin users can update payment statuses
        $this->middleware('admin')->only(['updatePaymentStatus']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Admins see all memberships, users see only their own
        $query = Membership::with(['user', 'desk']);
        if (auth()->user()->role !== 'admin') {
            $query->where('user_id', auth()->id());
        }
        $memberships = $query->get();
        
        return view('memberships.index', compact('memberships'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (auth()->user()->role === 'admin') {
            $users = \App\Models\User::all(); // Admin can select any user
        } else {
            $users = collect([auth()->user()]); // Regular users can only see themselves
        }
    
        $desks = \App\Models\Desk::all();
        return view('memberships.create', compact('users', 'desks'));
    }    

    /**
     * Store a newly created resource in storage.
     */
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Basic validation
        $request->validate([
            'user_id' => 'sometimes|required|exists:users,id', // Admin can specify a user, others cannot
            'desk_id' => 'required|exists:desks,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'membership_type' => 'required|in:daily,monthly,yearly',
        ]);
    
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
    
        // Check for a full calendar month
        if ($request->membership_type === 'monthly' && $start->diffInMonths($end) < 1) {
            return back()->withErrors(['membership_type' => 'Monthly membership must be at least one full month long.']);
        }
    
        // Check for a full calendar year
        if ($request->membership_type === 'yearly' && $start->diffInYears($end) < 1) {
            return back()->withErrors(['membership_type' => 'Yearly membership must be at least one full year long.']);
        }
    
        // If the user is not an admin, associate the membership with themselves
        if (auth()->user()->role !== 'admin') {
            $request->merge(['user_id' => auth()->id()]);
        }
    
        // Check if the desk is available for the selected period
        $existing = Membership::where('desk_id', $request->desk_id)
            ->whereDate('start_date', '<=', $request->end_date)
            ->whereDate('end_date', '>=', $request->start_date)
            ->exists();
    
        if ($existing) {
            return back()->withErrors(['desk_id' => 'Desk is already booked in this period.']);
        }
    
        // Automatically calculate the price
        $price = $this->calculateMembershipPrice($request->membership_type, $request->start_date, $request->end_date);
    
        // If the user is an admin, they can modify the price
        if (auth()->user()->role === 'admin' && $request->has('price')) {
            $price = $request->price;
        }
    
        // Create the membership
        $membership = Membership::create([
            'user_id' => $request->user_id,
            'desk_id' => $request->desk_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'membership_type' => $request->membership_type,
            'price' => $price,
        ]);
    
        // Send an email notification to the user
        Mail::to($membership->user->email)->send(new MembershipCreated($membership));
    
        return redirect()->route('memberships.index')->with('success', 'Membership created successfully and email sent!');
    }     
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Membership $membership)
    {
        // Only administrators can select a user
        if (auth()->user()->role === 'admin') {
            $users = \App\Models\User::all();
        } else {
            $users = collect([auth()->user()]); // Regular users can only see themselves
        }
    
        $users = auth()->user()->role === 'admin' ? \App\Models\User::all() : null;
        $desks = \App\Models\Desk::all();
        return view('memberships.edit', compact('membership', 'users', 'desks'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Membership $membership)
    {
        // A regular user can only edit their own membership
        if (auth()->user()->role !== 'admin' && auth()->id() !== $membership->user_id) {
            abort(403, 'You can only update your own memberships.');
        }
    
        // Define validation rules
        $rules = [
            'desk_id' => 'required|exists:desks,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'membership_type' => 'required|in:daily,monthly,yearly',
        ];
    
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
    
        // Check for a full calendar month
        if ($request->membership_type === 'monthly' && $start->diffInMonths($end) < 1) {
            return back()->withErrors(['membership_type' => 'Monthly membership must be at least one full month long.']);
        }
    
        // Check for a full calendar year
        if ($request->membership_type === 'yearly' && $start->diffInYears($end) < 1) {
            return back()->withErrors(['membership_type' => 'Yearly membership must be at least one full year long.']);
        }
    
        // Check if the desk is already booked by another user for the selected period
        $existing = Membership::where('desk_id', $request->desk_id)
            ->where('id', '!=', $membership->id) // Exclude the current record
            ->whereDate('start_date', '<=', $request->end_date)
            ->whereDate('end_date', '>=', $request->start_date)
            ->exists();
    
        if ($existing) {
            return back()->withErrors(['desk_id' => 'The desk is already booked for the selected period by another user.']);
        }
    
        // Admins can change the user
        if (auth()->user()->role === 'admin') {
            $rules['user_id'] = 'required|exists:users,id';
        }
    
        $request->validate($rules);
    
        // Automatically recalculate the price
        $price = $this->calculateMembershipPrice($request->membership_type, $request->start_date, $request->end_date);
    
        // If admin, allow manual price change
        if (auth()->user()->role === 'admin' && $request->has('price')) {
            $price = $request->price;
        }
    
        // Update the membership
        $membership->update([
            'user_id' => auth()->user()->role === 'admin' ? $request->user_id : $membership->user_id,
            'desk_id' => $request->desk_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'membership_type' => $request->membership_type,
            'price' => $price,
        ]);
    
        return redirect()->route('memberships.index')->with('success', 'Membership updated successfully!');
    }        
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Membership $membership)
    {
        // Ensure that a regular user can only delete unpaid memberships
        if (auth()->user()->role !== 'admin' && $membership->payment_status === 'paid') {
            return redirect()->back()->withErrors(['error' => 'You cannot delete a paid membership.']);
        }

        $membership->delete();

        return redirect()->route('memberships.index')->with('success', 'Membership deleted successfully.');
    }


    public function showExtendForm($id)
    {
        $membership = Membership::findOrFail($id);

        // Ensure that a regular user can only modify their own memberships
        if (auth()->user()->role !== 'admin' && auth()->id() !== $membership->user_id) {
            abort(403, 'You can only modify your own memberships.');
        }

        return view('memberships.extend', compact('membership'));
    }

    /**
     * Extend a membership
     */
    public function extend(Request $request, $id)
    {
        $membership = Membership::findOrFail($id);

        if (auth()->user()->role !== 'admin' && auth()->id() !== $membership->user_id) {
            abort(403, 'You can only modify your own memberships.');
        }

        // Set payment_status to pending BEFORE validation
        if ($membership->payment_status === 'paid') {
            $membership->payment_status = 'pending';
            $membership->save();
        }

        // Validate the date
        $request->validate([
            'new_end_date' => ['required', 'date', 'after:' . $membership->end_date],
        ]);

        $currentEndDate = Carbon::parse($membership->end_date);
        $newEndDate = Carbon::parse($request->new_end_date);

        // Check if the desk is already booked by another user
        $existing = Membership::where('desk_id', $membership->desk_id)
            ->where('id', '!=', $membership->id)
            ->whereDate('start_date', '<=', $newEndDate)
            ->whereDate('end_date', '>=', $currentEndDate)
            ->exists();

        if ($existing) {
            return redirect()->back()->withErrors(['desk_id' => 'The desk is already booked for the selected period.'])->withInput();
        }

        // Determine the new membership_type
        $totalMonths = Carbon::parse($membership->start_date)->diffInMonths($newEndDate);
        $totalYears = Carbon::parse($membership->start_date)->diffInYears($newEndDate);

        if ($totalYears >= 1) {
            $membership->membership_type = 'yearly';
        } elseif ($totalMonths >= 1) {
            $membership->membership_type = 'monthly';
        } else {
            $membership->membership_type = 'daily';
        }

        // Recalculate the membership price
        $newPrice = $this->calculateMembershipPrice($membership->membership_type, $membership->start_date, $newEndDate);

        // Update membership data
        $membership->end_date = $newEndDate;
        $membership->price = $newPrice;
        $membership->save();

        // Add a success message
        session()->flash('success', 'Membership extended successfully!');
        return redirect()->route('memberships.index');
    }


    private function calculateMembershipPrice($type, $startDate, $endDate)
    {
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Get membership prices from settings
        $dailySetting = PaymentSetting::where('period', 'daily')->first();
        $monthlySetting = PaymentSetting::where('period', 'monthly')->first();
        $yearlySetting = PaymentSetting::where('period', 'yearly')->first();

        if (!$dailySetting || !$monthlySetting || !$yearlySetting) {
            return 0;
        }

        $dailyRate = $dailySetting->cost_of_membership_for_this_period_of_time;
        $monthlyRate = $monthlySetting->cost_of_membership_for_this_period_of_time;
        $yearlyRate = $yearlySetting->cost_of_membership_for_this_period_of_time;

        // Determine the number of full years
        $years = (int) floor($start->diffInYears($end));
        $start->addYears($years);

        // Determine the number of full months
        $months = (int) floor($start->diffInMonths($end));
        $start->addMonths($months);

        // Remaining days
        $days = $start->diffInDays($end);

        // Calculate the price based on the membership type
        if ($type === 'monthly') {
            return round(($months * $monthlyRate) + ($days * $dailyRate), 2);
        }

        if ($type === 'yearly') {
            return round(($years * $yearlyRate) + ($months * $monthlyRate) + ($days * $dailyRate), 2);
        }

        if ($type === 'daily') {
            return round($days * $dailyRate, 2);
        }

        return 0;
    }                         
    
    
    /**
     * Update membership payment status (Admin only).
     */
    public function updatePaymentStatus(Request $request, $id)
    {
        $membership = Membership::findOrFail($id);
        $membership->amount_paid = $request->amount_paid;
        $membership->payment_status = $request->payment_status;
        $membership->payment_method = $request->payment_method;
        $membership->transaction_reference = $request->transaction_reference;
        $membership->save();

        return redirect()->back()->with('success', 'Payment details updated successfully!');
    }
}