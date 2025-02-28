<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PaymentSetting;
use Illuminate\Http\Request;

class PaymentSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payment_settings = PaymentSetting::all();
        return view('payment_settings.index', compact('payment_settings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('payment_settings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'period' => 'required',
            'cost_of_membership_for_this_period_of_time' => 'required',
        ]);

        PaymentSetting::create($request->all());

        return redirect()->route('payment_settings.index')->with('success', 'Payment setting created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaymentSetting $payment_setting)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentSetting $payment_setting)
    {
        return view('payment_settings.edit', compact('payment_setting'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PaymentSetting $payment_setting)
    {
        $request->validate([
            'period' => 'required',
            'cost_of_membership_for_this_period_of_time' => 'required',
        ]);

        $payment_setting->update($request->all());

        return redirect()->route('payment_settings.index')->with('success', 'Payment settings updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentSetting $payment_settings)
    {
        $payment_settings->delete();
        return redirect()->route('payment_settings.index')->with('success', 'Payment setting deleted successfully.');
    }
}
