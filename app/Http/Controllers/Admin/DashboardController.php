<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Membership;
use App\Models\Payment;
use App\Models\Desk;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get date range from request or set default to the current month
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Count total memberships
        $totalMemberships = Membership::count();

        // Count active memberships (memberships that are ongoing)
        $activeMemberships = Membership::where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                  ->orWhereBetween('end_date', [$startDate, $endDate])
                  ->orWhere(function ($q) use ($startDate, $endDate) {
                      $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                  });
        })
        ->count();
    

        // Get memberships active in the selected period
        $membershipIds = Membership::where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        })
        ->pluck('id'); // Get membership IDs

        // Calculate total revenue based on active memberships in the given period
        $totalRevenue = Payment::whereBetween('created_at', [$startDate, $endDate])->sum('amount');    
        

        // Desk occupancy calculation
        $totalDesks = Desk::count();
        $occupiedDesks = Membership::whereNotNull('desk_id')->distinct('desk_id')->count();

        // Desk occupancy calculation with date filtering
        $occupiedDesks = Membership::whereNotNull('desk_id')
        ->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        })
        ->distinct('desk_id')
        ->count();

        $availableDesks = $totalDesks - $occupiedDesks;

        // Count memberships by type
        $membershipTypes = Membership::selectRaw('membership_type, COUNT(*) as count')
        ->where(function ($query) use ($startDate, $endDate) {
            $query->whereBetween('start_date', [$startDate, $endDate])
                ->orWhereBetween('end_date', [$startDate, $endDate])
                ->orWhere(function ($q) use ($startDate, $endDate) {
                    $q->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $endDate);
                });
        })
        ->groupBy('membership_type')
        ->pluck('count', 'membership_type')
        ->toArray();

        // Pass data to the view
        return view('admin.dashboard', compact(
            'totalMemberships', 
            'activeMemberships', 
            'totalRevenue', 
            'totalDesks', 
            'occupiedDesks', 
            'availableDesks',
            'startDate',
            'endDate',
            'membershipTypes'
        ));
    }
}
