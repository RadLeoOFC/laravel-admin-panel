<?php

namespace App\Http\Controllers;

use App\Models\Membership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $memberships = Membership::with(['user', 'desk'])->get();
        return view('memberships.index', compact('memberships'));
    }
    

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = \App\Models\User::all();
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
            'user_id' => 'required|exists:users,id',
            'desk_id' => 'required|exists:desks,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'membership_type' => 'required|in:daily,monthly,yearly',
            'price' => 'nullable|numeric|min:0',
        ]);
    
        // Check if the selected desk is already booked in the requested period
        $existing = Membership::where('desk_id', $request->desk_id)
            ->whereDate('start_date', '<=', $request->end_date)
            ->whereDate('end_date', '>=', $request->start_date)
            ->get(); // Retrieve all conflicting bookings
    
        if ($existing->count() > 0) {
            return back()->withErrors(['desk_id' => 'Desk is already booked in this period.']);
        }
    
        // Create a new membership record if the desk is available
        Membership::create($request->all());
    
        return redirect()->route('memberships.index')->with('success', 'Membership created successfully!');
    }
    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Membership $membership)
    {
        $users = \App\Models\User::all();
        $desks = \App\Models\Desk::all();
        return view('memberships.edit', compact('membership', 'users', 'desks'));
    }
    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Membership $membership)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'desk_id' => 'required|exists:desks,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'membership_type' => 'required|in:daily,monthly,yearly',
            'price' => 'nullable|numeric|min:0',
        ]);
    
        $membership->update($request->all());
    
        return redirect()->route('memberships.index')->with('success', 'Membership updated successfully!');
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Membership $membership)
    {
        $membership->delete();
        return redirect()->route('memberships.index')->with('success', 'Membership deleted successfully!');
    }    
}
