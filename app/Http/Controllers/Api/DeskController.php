<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Desk;
use Illuminate\Http\Request;

class DeskController extends Controller
{
    /**
     * Get all desks.
     */
    public function index()
    {
        return response()->json(Desk::all(), 200);
    }

    /**
     * Get a single desk by ID.
     */
    public function show(Desk $desk)
    {
        return response()->json($desk, 200);
    }

    /**
     * Store a new desk.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'status' => 'required|in:available,occupied,maintenance',
        ]);

        $desk = Desk::create($request->all());

        return response()->json(['message' => 'Desk created successfully', 'desk' => $desk], 201);
    }

    /**
     * Update an existing desk.
     */
    public function update(Request $request, Desk $desk)
    {
        $request->validate([
            'name' => 'string|max:255',
            'location' => 'string|max:255',
            'status' => 'in:available,occupied,maintenance',
        ]);

        $desk->update($request->all());

        return response()->json(['message' => 'Desk updated successfully', 'desk' => $desk], 200);
    }

    /**
     * Delete a desk.
     */
    public function destroy(Desk $desk)
    {
        $desk->delete();

        return response()->json(['message' => 'Desk deleted successfully'], 200);
    }
}
