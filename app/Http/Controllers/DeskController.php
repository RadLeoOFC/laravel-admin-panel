<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use App\Models\Desk;
use App\Models\Membership;
use Illuminate\Http\Request;

class DeskController extends Controller
{
    /**
     * Ограничиваем доступ к CRUD-операциям только для администраторов.
     */
    public function __construct()
    {
        $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Отображение списка столов.
     */
    public function index()
    {
        $desks = Desk::all();
        return view('desks.index', compact('desks'));
    }

    public function map()
    {
        $userId = auth()->id();
    
        $desks = Desk::all()->map(function ($desk) use ($userId) {
            $desk->user_booked = $desk->memberships()
                ->where('user_id', $userId)
                ->whereDate('end_date', '>=', now())
                ->exists();
            return $desk;
        });
    
        return view('desks.map', [
            'desks' => $desks,
            'maxX' => $desks->max('coordinates_x'),
            'maxY' => $desks->max('coordinates_y'),
        ]);
    }       

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('desks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'location' => 'required',
            'status' => 'required|in:available,occupied,maintenance',
            'coordinates_x' => 'nullable|integer',
            'coordinates_y' => 'nullable|integer',
        ]);
    
        Desk::create($request->all());
    
        return redirect()->route('desks.index')->with('success', 'Desk created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Desk $desk)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Desk $desk)
    {
        return view('desks.edit', compact('desk'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Desk $desk)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'location' => 'sometimes|string|max:255',
            'status' => 'sometimes|in:available,occupied,maintenance',
            'coordinates_x' => 'sometimes|integer',
            'coordinates_y' => 'sometimes|integer',
        ]);        
    
        $desk->update($request->all());
    
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }
    
        return redirect()->route('desks.index')->with('success', 'Desk updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Desk $desk)
    {
        $desk->delete();
        return redirect()->route('desks.index')->with('success', 'Desk deleted successfully.');
    }
}