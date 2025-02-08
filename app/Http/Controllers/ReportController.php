<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Membership;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display a listing of the reports.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get the "month" parameter from the request
        $month = $request->input('month');

        // Base query for retrieving memberships
        $query = Membership::with(['user', 'desk']);

        // If a month is selected, filter by its range
        if (!empty($month)) { // Check if the month exists in the request
            try {
                // Use $startDate and $endDate instead of $startOfMonth and $endOfMonth
                $startDate = Carbon::parse($month)->startOfMonth();
                $endDate = Carbon::parse($month)->endOfMonth();

                $query->where(function ($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                      ->orWhereBetween('end_date', [$startDate, $endDate])
                      ->orWhere(function ($q) use ($startDate, $endDate) {
                          $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                      });
                });

            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['month' => 'Invalid month format']);
            }
        }

        // Retrieve all memberships if there is no filtering
        $memberships = $query->get();

        // Pass data to the view
        return view('reports.index', compact('memberships'));
    }
}
