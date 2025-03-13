<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

use App\Models\Desk;
use Illuminate\Http\Request;

class DeskController extends Controller
{
        /**
     * DeskController handles operations related to desks.
     * This constructor ensures that only admin users can perform create, update, and delete actions.
     */
    public function __construct()
    {
        // Apply the 'admin' middleware to restrict access
        // Only admin users can create, store, edit, update, or delete desks
        $this->middleware('admin')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    /**
     * Display a listing of the resource
     */
    public function index()
    {
        $desks = Desk::all();
        return view('desks.index', compact('desks'));
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
            'status' => 'required',
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
            'name' => 'required',
            'location' => 'required',
            'status' => 'required',
        ]);

        $desk->update($request->all());

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
