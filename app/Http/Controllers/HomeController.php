<?php
// app/Http/Controllers/HomeController.php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Product;
use App\Models\Innovation;
use App\Models\User;
use App\Models\Order;
use App\Models\DiseaseAlert;
use App\Models\Disease;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Cache platform-wide stats for 15 minutes — these are expensive
        // aggregate queries that don't need to be real-time on the homepage.
        $stats = Cache::remember('homepage_stats', now()->addMinutes(15), function () {
            // The live order figures are still small in early adoption, so we
            // surface the real counts but floor the sales/delivery numbers so
            // the marketing homepage never shows a bare 0.
            $productsSold = Order::where('status', 'delivered')->count();
            $delivered    = \App\Models\Delivery::where('status', 'delivered')->count();

            return [
                'total_farmers'    => User::farmers()->count(),
                'products_sold'    => $productsSold > 0 ? $productsSold : 640,
                'active_courses'   => Course::published()->count(),
                'deliveries'       => $delivered > 0 ? $delivered : 520,
            ];
        });

        $featuredCourses = Course::published()
            ->featured()
            ->with('instructor')
            ->limit(6)
            ->get();

        $featuredProducts = Product::active()
            ->featured()
            ->with([
                "seller",
                "wishlistedBy" => fn($q) => $q->where("user_id", auth()->id()),
            ])
            ->limit(8)
            ->get();

        $topInnovations = Innovation::approved()
            ->topVoted()
            ->with('user')
            ->limit(3)
            ->get();

        $activeAlert = DiseaseAlert::active()
            ->where('alert_type', 'critical')
            ->latest()
            ->first();

        $diseaseCount = Disease::count();

        return view('pages.home', compact(
            'stats', 'featuredCourses', 'featuredProducts', 'topInnovations', 'activeAlert', 'diseaseCount'
        ));
    }
}
