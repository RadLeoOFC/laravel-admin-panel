<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use Illuminate\Http\Request;

class MembershipController extends Controller
{
    /**
     * Get all memberships.
     */
    public function index()
    {
        return response()->json(Membership::with(['user', 'desk'])->get(), 200);
    }

    /**
     * Get a single membership by ID.
     */
    public function show(Membership $membership)
    {
        return response()->json($membership->load(['user', 'desk']), 200);
    }

    /**
     * Store a new membership.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'desk_id' => 'required|exists:desks,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'membership_type' => 'required|in:daily,monthly,yearly',
            'price' => 'nullable|numeric|min:0',
        ]);

        // Ensure the desk is not already booked for the requested period
        $existing = Membership::where('desk_id', $request->desk_id)
            ->whereDate('start_date', '<=', $request->end_date)
            ->whereDate('end_date', '>=', $request->start_date)
            ->exists();

        if ($existing) {
            return response()->json(['error' => 'Desk is already booked in this period'], 400);
        }

        $membership = Membership::create($request->all());

        return response()->json(['message' => 'Membership created successfully', 'membership' => $membership], 201);
    }

    /**
     * Update an existing membership.
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

        // Check for booking conflicts
        $existing = Membership::where('desk_id', $request->desk_id)
            ->where('id', '!=', $membership->id) // Ignore the current membership
            ->whereDate('start_date', '<=', $request->end_date)
            ->whereDate('end_date', '>=', $request->start_date)
            ->exists();

        if ($existing) {
            return response()->json(['error' => 'Desk is already booked in this period'], 400);
        }

        $membership->update($request->all());

        return response()->json(['message' => 'Membership updated successfully', 'membership' => $membership], 200);
    }

    /**
     * Delete a membership.
     */
    public function destroy(Membership $membership)
    {
        $membership->delete();

        return response()->json(['message' => 'Membership deleted successfully'], 200);
    }
}
