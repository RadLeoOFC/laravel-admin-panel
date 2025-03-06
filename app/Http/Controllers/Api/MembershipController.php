<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MembershipController extends Controller
{
    /**
     * Get all memberships.
     * Admins see all memberships, users see only their own.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            $memberships = Membership::with(['user', 'desk'])->get();
        } else {
            $memberships = Membership::with(['user', 'desk'])->where('user_id', $user->id)->get();
        }

        return response()->json($memberships, 200);
    }

    /**
     * Get a single membership by ID.
     * Admins can view all memberships, users can only view their own.
     */
    public function show(Membership $membership)
    {
        $user = Auth::user();

        if ($user->role !== 'admin' && $membership->user_id !== $user->id) {
            return response()->json(['error' => 'You can only view your own memberships'], 403);
        }

        return response()->json($membership->load(['user', 'desk']), 200);
    }

    /**
     * Store a new membership.
     * - Admins can create memberships for any user.
     * - Users can create memberships only for themselves.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'desk_id' => 'required|exists:desks,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'membership_type' => 'required|in:daily,monthly,yearly',
            'price' => 'nullable|numeric|min:0',
        ]);

        // Обычный пользователь может создавать членства только для себя
        if ($user->role !== 'admin' && $request->user_id != $user->id) {
            return response()->json(['error' => 'You can only create your own memberships'], 403);
        }

        // Проверяем, свободно ли место
        $existing = Membership::where('desk_id', $request->desk_id)
            ->whereDate('start_date', '<=', $request->end_date)
            ->whereDate('end_date', '>=', $request->start_date)
            ->exists();

        if ($existing) {
            return response()->json(['error' => 'Desk is already booked in this period'], 400);
        }

        $membership = Membership::create($validatedData);

        return response()->json(['message' => 'Membership created successfully', 'membership' => $membership], 201);
    }

    /**
     * Update an existing membership.
     * - Admins can update any membership.
     * - Users can update only their own memberships.
     */
    public function update(Request $request, Membership $membership)
    {
        $user = Auth::user();

        // Обычные пользователи могут редактировать только свои членства
        if ($user->role !== 'admin' && $membership->user_id !== $user->id) {
            return response()->json(['error' => 'You can only update your own memberships'], 403);
        }

        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'desk_id' => 'required|exists:desks,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'membership_type' => 'required|in:daily,monthly,yearly',
            'price' => 'nullable|numeric|min:0',
        ]);

        // Проверяем, свободно ли место
        $existing = Membership::where('desk_id', $request->desk_id)
            ->where('id', '!=', $membership->id) // Игнорируем текущую запись
            ->whereDate('start_date', '<=', $request->end_date)
            ->whereDate('end_date', '>=', $request->start_date)
            ->exists();

        if ($existing) {
            return response()->json(['error' => 'Desk is already booked in this period'], 400);
        }

        $membership->update($validatedData);

        return response()->json(['message' => 'Membership updated successfully', 'membership' => $membership], 200);
    }

    /**
     * Delete a membership.
     * - Admins can delete any membership.
     * - Users can delete only their own **unpaid** memberships.
     * - "Unpaid" means `amount_paid = 0` and `payment_status = 'pending'`.
     */
    public function destroy(Membership $membership)
    {
        $user = Auth::user();
    
        // Обычные пользователи могут удалять только свои **неоплаченные** членства
        if ($user->role !== 'admin') {
            if ($membership->user_id !== $user->id) {
                return response()->json(['error' => 'You can only delete your own memberships'], 403);
            }
    
            if ($membership->amount_paid > 0 || $membership->payment_status === 'paid') {
                return response()->json(['error' => 'You cannot delete a paid membership'], 403);
            }
        }
    
        $membership->delete();
    
        // **Исправляем статус с 200 на 204**
        return response()->noContent(); // Laravel автоматически вернет 204
    }
    
    public function pay(Request $request, Membership $membership)
    {
        $user = Auth::user();

        // Пользователь может оплатить только свое членство
        if ($user->role !== 'admin' && $membership->user_id !== $user->id) {
            return response()->json(['error' => 'You can only pay for your own memberships'], 403);
        }

        $validatedData = $request->validate([
            'amount_paid' => 'required|numeric|min:0',
            'payment_status' => 'required|in:pending,paid,failed',
            'payment_method' => 'required|string',
            'transaction_reference' => 'required|string',
        ]);

        // Обновляем членство с новой информацией об оплате
        $membership->update($validatedData);

        return response()->json([
            'message' => 'Membership payment updated successfully',
            'membership' => $membership,
        ], 200);
    }

}
