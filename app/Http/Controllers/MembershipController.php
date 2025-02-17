<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\MembershipCreated;

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
    public function store(Request $request)
    {
        // Validate input data
        $request->validate([
            'user_id' => 'sometimes|required|exists:users,id', // Allow passing user_id only for admins
            'desk_id' => 'required|exists:desks,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'membership_type' => 'required|in:daily,monthly,yearly',
            'price' => 'nullable|numeric|min:0',
        ]);
    
        // If the user is not an admin, they can only create a membership for themselves
        if (auth()->user()->role !== 'admin') {
            $request->merge(['user_id' => auth()->id()]);
        }
    
        // Check if the selected desk is already booked in the requested period
        $existing = Membership::where('desk_id', $request->desk_id)
            ->whereDate('start_date', '<=', $request->end_date)
            ->whereDate('end_date', '>=', $request->start_date)
            ->exists();
    
        if ($existing) {
            return back()->withErrors(['desk_id' => 'Desk is already booked in this period.']);
        }
    
        // Create membership (admins can assign any user, regular users can only assign themselves)
        $membership = Membership::create([
            'user_id' => $request->user_id,
            'desk_id' => $request->desk_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'membership_type' => $request->membership_type,
            'price' => $request->price,
        ]);
    
        // Send email notification to the user
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
    
        $desks = \App\Models\Desk::all();
        return view('memberships.edit', compact('membership', 'users', 'desks'));
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Membership $membership)
    {
        // Ensure the user can only update their own memberships (except admins)
        if (auth()->user()->role !== 'admin' && auth()->id() !== $membership->user_id) {
            abort(403, 'You can only update your own memberships.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'desk_id' => 'required|exists:desks,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'membership_type' => 'required|in:daily,monthly,yearly',
            'price' => 'nullable|numeric|min:0',
        ]);

        $membership->update([
            'user_id' => $request->user_id,
            'desk_id' => $request->desk_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'membership_type' => $request->membership_type,
            'price' => $request->price,
        ]);

        return redirect()->route('memberships.index')->with('success', 'Membership updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Membership $membership)
    {
        // Ensure the user can only delete their own memberships (except admins)
        if (auth()->user()->role !== 'admin' && auth()->id() !== $membership->user_id) {
            abort(403, 'You can only delete your own memberships.');
        }

        $membership->delete();
        return redirect()->route('memberships.index')->with('success', 'Membership deleted successfully!');
    }

    /**
     * Extend a membership (Admin only).
     */
    public function extend(Request $request, $id)
    {
        $membership = Membership::findOrFail($id);
        $membership->end_date = \Carbon\Carbon::parse($membership->end_date)->addMonth();
        $membership->payment_status = 'pending'; // Mark payment as pending until paid
        $membership->save();

        return redirect()->back()->with('success', 'Membership extended successfully!');
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
