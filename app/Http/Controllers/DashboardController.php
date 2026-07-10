<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $farm = $user->farm;

            $stats = [
                'farm_area'           => $farm?->size_hectares ?? 0,
                'monthly_sales'       => $user->sales()
                                              ->where('payment_status', 'paid')
                                              ->whereMonth('created_at', now()->month)
                                              ->sum('total'),
                'courses_completed'   => $user->enrollments()->where('status', 'completed')->count(),
                'courses_total'       => $user->enrollments()->count(),
                'active_deliveries'   => Order::forBuyer($user->id)->active()->count(),
            ];

            $activeCourses = $user->enrollments()
                ->where('status', 'active')
                ->with('course')
                ->latest()
                ->limit(3)
                ->get();

            $recentOrders = Order::forBuyer($user->id)
                ->with('items')
                ->latest()
                ->limit(4)
                ->get();

            $notifications = $user->notifications()->limit(5)->get();

            $chartData = Order::forBuyer($user->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->selectRaw('DATE(created_at) as day, SUM(total) as total')
                ->groupBy('day')
                ->orderBy('day')
                ->get();

            return view('pages.dashboard', compact(
                'stats', 'activeCourses', 'recentOrders', 'notifications', 'chartData'
            ));
        } catch (\Exception $e) {
            Log::error("Dashboard load error: " . $e->getMessage());
            return view('pages.dashboard', [
                'stats' => [
                    'farm_area' => 0,
                    'monthly_sales' => 0,
                    'courses_completed' => 0,
                    'courses_total' => 0,
                    'active_deliveries' => 0,
                ],
                'activeCourses' => collect(),
                'recentOrders' => collect(),
                'notifications' => collect(),
                'chartData' => collect(),
            ])->with('error', 'Could not load some dashboard data.');
        }
    }
}
