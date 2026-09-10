<?php
// app/Http/Controllers/FarmRecordsController.php

namespace App\Http\Controllers;

use App\Events\FarmAlertCreated;
use App\Jobs\BroadcastNotification;
use App\Models\Farm;
use App\Models\FarmSeason;
use App\Models\FarmCost;
use App\Models\FarmSale;
use App\Models\FarmEvent;
use App\Models\FarmAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FarmRecordsController extends Controller
{
    /**
     * Farm Records Dashboard — main overview with season tracking,
     * cost/revenue analytics, and intelligent insights.
     */
    public function index()
    {
        $farm = $this->getUserFarm();
        if (!$farm) {
            return redirect()->route('profile')->with('info', 'Please set up your farm first to access records.');
        }

        $seasons = $farm->seasons()->latest()->get();
        $activeSeason = $farm->seasons()->active()->first();
        $alerts = $farm->alerts()->unread()->latest()->limit(5)->get();

        $recentEvents = FarmEvent::whereIn('season_id', $farm->seasons()->select('id'))
            ->latest('occurred_at')
            ->limit(5)
            ->get();

        // Analytics summary
        $analytics = $this->computeAnalytics($farm);

        // Insights & recommendations
        $insights = $this->generateInsights($farm, $activeSeason);

        return view('pages.farm-records', compact(
            'farm', 'seasons', 'activeSeason', 'alerts', 'recentEvents',
            'analytics', 'insights'
        ));
    }

    /**
     * Store a new season.
     */
    public function storeSeason(Request $request)
    {
        $farm = $this->getUserFarm();
        abort_unless($farm, 403);

        $validated = $request->validate([
            'name'                  => 'required|string|max:100',
            'crop'                  => 'required|string|max:80',
            'field_size_hectares'   => 'nullable|numeric|min:0',
            'field_name'            => 'nullable|string|max:80',
            'planted_at'            => 'nullable|date',
            'expected_harvest_at'   => 'nullable|date|after_or_equal:planted_at',
            'expected_yield_kg'     => 'nullable|numeric|min:0',
            'expected_revenue'      => 'nullable|numeric|min:0',
            'notes'                 => 'nullable|string|max:1000',
        ]);

        $validated['farm_id'] = $farm->id;
        $validated['status'] = 'planning';

        $season = FarmSeason::create($validated);

        // Generate initial recommendation alert
        $this->createAlert($farm, $season, 'recommendation',
            '🌱 New Season Started',
            "Your {$season->crop} season \"{$season->name}\" has been created. " .
            "Start logging costs and activities to track your farm's performance.",
            'low'
        );

        return back()->with('success', "Season \"{$season->name}\" created successfully!");
    }

    /**
     * Update a season's status or details.
     */
    public function updateSeason(Request $request, FarmSeason $season)
    {
        $farm = $this->getUserFarm();
        abort_unless($farm && $season->farm_id === $farm->id, 403);

        $validated = $request->validate([
            'status'               => 'sometimes|in:planning,active,harvested,completed',
            'actual_yield_kg'      => 'nullable|numeric|min:0',
            'harvested_at'         => 'nullable|date',
            'actual_revenue'       => 'nullable|numeric|min:0',
            'notes'                => 'nullable|string|max:1000',
        ]);

        $season->update($validated);

        if (in_array($validated['status'] ?? '', ['harvested', 'completed'])) {
            $season->recalculateTotals();

            if ($season->actual_yield_kg && $season->expected_yield_kg) {
                $yieldDiff = $season->actual_yield_kg - $season->expected_yield_kg;
                $pct = round(($yieldDiff / $season->expected_yield_kg) * 100);

                if ($pct < -20) {
                    $this->createAlert($farm, $season, 'low_yield',
                        '⚠️ Yield Below Expectations',
                        "Your {$season->crop} yield was {$pct}% below expectations. " .
                        "Review your input costs and activities to identify potential causes.",
                        'high'
                    );
                }
            }
        }

        return back()->with('success', 'Season updated!');
    }

    /**
     * Delete a season and all its records.
     */
    public function destroySeason(FarmSeason $season)
    {
        $farm = $this->getUserFarm();
        abort_unless($farm && $season->farm_id === $farm->id, 403);

        $season->delete();
        return back()->with('success', 'Season deleted.');
    }

    // ── Cost CRUD ──────────────────────────────────────────────────────

    public function storeCost(Request $request, FarmSeason $season)
    {
        $farm = $this->getUserFarm();
        abort_unless($farm && $season->farm_id === $farm->id, 403);

        $validated = $request->validate([
            'category'      => 'required|in:seed,fertilizer,chemicals,labour,transport,irrigation,equipment,rent,other',
            'item_name'     => 'required|string|max:150',
            'quantity'      => 'nullable|numeric|min:0',
            'unit'          => 'nullable|string|max:30',
            'unit_cost'     => 'required|numeric|min:0',
            'total_cost'    => 'required|numeric|min:0',
            'purchased_at'  => 'nullable|date',
            'supplier'      => 'nullable|string|max:150',
            'notes'         => 'nullable|string|max:500',
        ]);

        $validated['currency'] = 'MWK';
        $season->costs()->create($validated);
        $season->recalculateTotals();

        // Check for cost anomalies
        $this->checkCostAnomaly($farm, $season);

        return back()->with('success', 'Cost record added!');
    }

    public function destroyCost(FarmCost $cost)
    {
        $farm = $this->getUserFarm();
        $season = $cost->season;
        abort_unless($farm && $season->farm_id === $farm->id, 403);

        $cost->delete();
        $season->recalculateTotals();
        return back()->with('success', 'Cost record removed.');
    }

    // ── Sale CRUD ──────────────────────────────────────────────────────

    public function storeSale(Request $request, FarmSeason $season)
    {
        $farm = $this->getUserFarm();
        abort_unless($farm && $season->farm_id === $farm->id, 403);

        $validated = $request->validate([
            'crop'          => 'required|string|max:80',
            'quantity_kg'   => 'required|numeric|min:0.01',
            'price_per_kg'  => 'required|numeric|min:0',
            'total_revenue' => 'required|numeric|min:0',
            'buyer_name'    => 'nullable|string|max:150',
            'buyer_type'    => 'nullable|string|max:50',
            'market'        => 'nullable|string|max:150',
            'sold_at'       => 'required|date',
            'notes'         => 'nullable|string|max:500',
        ]);

        $validated['currency'] = 'MWK';
        $season->sales()->create($validated);
        $season->recalculateTotals();

        return back()->with('success', 'Sale recorded!');
    }

    public function destroySale(FarmSale $sale)
    {
        $farm = $this->getUserFarm();
        $season = $sale->season;
        abort_unless($farm && $season->farm_id === $farm->id, 403);

        $sale->delete();
        $season->recalculateTotals();
        return back()->with('success', 'Sale record removed.');
    }

    // ── Event CRUD ─────────────────────────────────────────────────────

    public function storeEvent(Request $request, FarmSeason $season)
    {
        $farm = $this->getUserFarm();
        abort_unless($farm && $season->farm_id === $farm->id, 403);

        $validated = $request->validate([
            'type'               => 'required|in:activity,weather,disease,pest,treatment,observation,milestone,alert',
            'title'              => 'required|string|max:200',
            'description'        => 'nullable|string|max:1000',
            'occurred_at'        => 'required|date',
            'severity'           => 'nullable|in:low,medium,high,critical',
            'temperature_c'      => 'nullable|numeric',
            'rainfall_mm'        => 'nullable|numeric|min:0',
            'weather_condition'  => 'nullable|string|max:100',
            'product_used'       => 'nullable|string|max:200',
            'dosage'             => 'nullable|numeric|min:0',
            'dosage_unit'        => 'nullable|string|max:30',
            'requires_follow_up' => 'nullable|boolean',
            'follow_up_date'     => 'nullable|date',
        ]);

        $season->events()->create($validated);

        // Generate alerts for critical events
        if (in_array($validated['type'] ?? '', ['disease', 'pest', 'alert']) && in_array($validated['severity'] ?? '', ['high', 'critical'])) {
            $this->createAlert($farm, $season, $validated['type'] === 'disease' ? 'disease_outbreak' : 'weather_risk',
                '🚨 ' . $validated['title'],
                $validated['description'] ?? 'Immediate attention required for your ' . $season->crop . ' crop.',
                $validated['severity'] === 'critical' ? 'urgent' : 'high'
            );
        }

        return back()->with('success', 'Event logged!');
    }

    public function destroyEvent(FarmEvent $event)
    {
        $farm = $this->getUserFarm();
        $season = $event->season;
        abort_unless($farm && $season->farm_id === $farm->id, 403);

        $event->delete();
        return back()->with('success', 'Event removed.');
    }

    // ── Alerts ─────────────────────────────────────────────────────────

    public function markAlertRead(FarmAlert $alert)
    {
        $farm = $this->getUserFarm();
        abort_unless($farm && $alert->farm_id === $farm->id, 403);

        $alert->update(['is_read' => true]);
        return response()->json(['ok' => true]);
    }

    public function dismissAlert(FarmAlert $alert)
    {
        $farm = $this->getUserFarm();
        abort_unless($farm && $alert->farm_id === $farm->id, 403);

        $alert->update([
            'is_dismissed' => true,
            'dismissed_at' => now(),
            'dismissed_by' => Auth::id(),
        ]);
        return back()->with('success', 'Alert dismissed.');
    }

    // ── Analytics API (for charts) ─────────────────────────────────────

    public function analytics(Farm $farm)
    {
        abort_unless($farm->user_id === Auth::id(), 403);

        $seasons = $farm->seasons()->with(['costs', 'sales'])->get();
        $ids = $farm->seasons()->select('id')->pluck('id');

        $costByCategory = FarmCost::whereIn('season_id', $ids)
            ->select('category', DB::raw('SUM(total_cost) as total'))
            ->groupBy('category')
            ->get();

        $revenueOverTime = FarmSale::whereIn('season_id', $ids)
            ->select(DB::raw("DATE_FORMAT(sold_at, '%Y-%m') as month"), DB::raw('SUM(total_revenue) as total'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $seasonComparison = $seasons->map(fn($s) => [
            'name'  => $s->name,
            'costs' => (float) $s->total_costs,
            'revenue' => (float) $s->actual_revenue,
            'profit' => (float) $s->net_profit,
            'roi'   => (float) ($s->roi_percentage ?? 0),
        ]);

        return response()->json([
            'costByCategory'     => $costByCategory,
            'revenueOverTime'    => $revenueOverTime,
            'seasonComparison'   => $seasonComparison,
        ]);
    }

    // ── Admin: View all farm alerts ────────────────────────────────────

    public function adminAlerts()
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $alerts = FarmAlert::with(['farm', 'season'])
            ->where('is_dismissed', false)
            ->latest()
            ->paginate(20);

        $stats = [
            'total'    => FarmAlert::where('is_dismissed', false)->count(),
            'urgent'   => FarmAlert::where('priority', 'urgent')->where('is_dismissed', false)->count(),
            'unread'   => FarmAlert::where('is_read', false)->where('is_dismissed', false)->count(),
            'farms'    => Farm::count(),
        ];

        return view('pages.admin-farm-alerts', compact('alerts', 'stats'));
    }

    public function adminDismissAlert(FarmAlert $alert)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $alert->update([
            'is_dismissed' => true,
            'dismissed_at' => now(),
            'dismissed_by' => Auth::id(),
        ]);

        return back()->with('success', 'Alert dismissed.');
    }

    public function adminIntervene(Request $request, Farm $farm)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $validated = $request->validate([
            'title'   => 'required|string|max:200',
            'message' => 'required|string|max:2000',
            'priority'=> 'required|in:low,medium,high,urgent',
        ]);

        FarmAlert::create([
            'farm_id'          => $farm->id,
            'type'             => 'admin_intervention',
            'title'            => $validated['title'],
            'message'          => $validated['message'],
            'priority'         => $validated['priority'],
            'requires_action'  => true,
            'action_url'       => route('farm-records'),
        ]);

        // Notify the farm owner
        BroadcastNotification::dispatch(
            $farm->user_id,
            '👤 Admin Intervention — ' . $validated['title'],
            $validated['message'],
            'farm',
            'fas fa-user-shield',
            '#dc2626',
            route('farm-records'),
        );

        return back()->with('success', 'Intervention alert sent to the farmer.');
    }

    // ── Private Helpers ────────────────────────────────────────────────

    private function getUserFarm(): ?Farm
    {
        return Farm::where('user_id', Auth::id())->first();
    }

    private function seasonIds(Farm $farm)
    {
        return $farm->seasons()->select('id')->pluck('id');
    }

    private function computeAnalytics(Farm $farm): array
    {
        $ids = $this->seasonIds($farm);

        $totalCosts = FarmCost::whereIn('season_id', $ids)->sum('total_cost');
        $totalRevenue = FarmSale::whereIn('season_id', $ids)->sum('total_revenue');
        $seasonCount = $farm->seasons()->count();
        $activeSeasons = $farm->seasons()->active()->count();
        $avgYield = $farm->seasons()->whereNotNull('yield_per_hectare')->avg('yield_per_hectare');

        $costByCategory = FarmCost::whereIn('season_id', $ids)
            ->select('category', DB::raw('SUM(total_cost) as total'))
            ->groupBy('category')
            ->get()
            ->pluck('total', 'category');

        $recentSales = FarmSale::whereIn('season_id', $ids)
            ->orderBy('sold_at', 'desc')
            ->limit(5)
            ->get();

        $upcomingEvents = FarmEvent::whereIn('season_id', $ids)
            ->where('requires_follow_up', true)
            ->where('is_resolved', false)
            ->where('follow_up_date', '>=', now())
            ->orderBy('follow_up_date')
            ->limit(5)
            ->get();

        return [
            'total_costs'      => (float) $totalCosts,
            'total_revenue'    => (float) $totalRevenue,
            'net_profit'       => (float) ($totalRevenue - $totalCosts),
            'roi'              => $totalCosts > 0 ? round((($totalRevenue - $totalCosts) / $totalCosts) * 100, 1) : 0,
            'season_count'     => $seasonCount,
            'active_seasons'   => $activeSeasons,
            'avg_yield'        => $avgYield ? round((float) $avgYield, 1) : 0,
            'cost_by_category' => $costByCategory,
            'recent_sales'     => $recentSales,
            'upcoming_events'  => $upcomingEvents,
        ];
    }

    private function generateInsights(Farm $farm, ?FarmSeason $activeSeason): array
    {
        $insights = [];
        $ids = $this->seasonIds($farm);

        // Insight 1: Cost breakdown
        $totalCosts = FarmCost::whereIn('season_id', $ids)->sum('total_cost');
        if ($totalCosts > 0) {
            $biggestCost = FarmCost::whereIn('season_id', $ids)
                ->select('category', DB::raw('SUM(total_cost) as total'))
                ->groupBy('category')
                ->orderByDesc('total')
                ->first();

            if ($biggestCost) {
                $pct = round(($biggestCost->total / $totalCosts) * 100, 1);
                $catLabel = FarmCost::categories()[$biggestCost->category]['label'] ?? $biggestCost->category;
                $insights[] = [
                    'type'    => 'cost',
                    'icon'    => 'fas fa-chart-pie',
                    'color'   => '#f59e0b',
                    'title'   => "Top Cost: {$catLabel}",
                    'message' => "{$pct}% of your total costs go to {$catLabel} (MWK " . number_format($biggestCost->total) . "). Consider exploring cheaper alternatives or bulk purchasing.",
                    'action'  => 'Review costs in your active season',
                ];
            }
        }

        // Insight 2: Active season progress
        if ($activeSeason && $activeSeason->planted_at) {
            $daysPlanted = $activeSeason->planted_at->diffInDays(now());
            $insights[] = [
                'type'    => 'season',
                'icon'    => 'fas fa-seedling',
                'color'   => '#22c55e',
                'title'   => "{$activeSeason->crop} — Day {$daysPlanted}",
                'message' => "Your {$activeSeason->crop} was planted {$daysPlanted} days ago." .
                    ($activeSeason->expected_harvest_at ? " Expected harvest: {$activeSeason->expected_harvest_at->format('M d, Y')}." : ''),
                'action'  => 'Log activities and treatments',
            ];
        }

        // Insight 3: Pending follow-ups
        $pendingEvents = FarmEvent::whereIn('season_id', $ids)
            ->where('requires_follow_up', true)
            ->where('is_resolved', false)
            ->count();
        if ($pendingEvents > 0) {
            $insights[] = [
                'type'    => 'followup',
                'icon'    => 'fas fa-clock',
                'color'   => '#8b5cf6',
                'title'   => "{$pendingEvents} Pending Follow-up(s)",
                'message' => 'You have farm events that require follow-up action. Check your event log to stay on top of treatments and observations.',
                'action'  => 'View pending events',
            ];
        }

        // Insight 4: ROI comparison
        $bestSeason = $farm->seasons()
            ->whereNotNull('roi_percentage')
            ->orderByDesc('roi_percentage')
            ->first();
        if ($bestSeason) {
            $insights[] = [
                'type'    => 'roi',
                'icon'    => 'fas fa-trophy',
                'color'   => '#eab308',
                'title'   => "Best ROI: {$bestSeason->name}",
                'message' => "Your best performing season achieved {$bestSeason->roi_percentage}% ROI with {$bestSeason->crop}. " .
                    "Net profit: MWK " . number_format($bestSeason->net_profit) . ".",
                'action'  => 'Compare seasons',
            ];
        }

        return $insights;
    }

    private function checkCostAnomaly(Farm $farm, FarmSeason $season): void
    {
        $avgCostPerSeason = $farm->seasons()
            ->where('id', '!=', $season->id)
            ->avg('total_costs');

        if ($avgCostPerSeason && $season->total_costs > $avgCostPerSeason * 1.5) {
            $this->createAlert($farm, $season, 'cost_anomaly',
                '💰 Cost Anomaly Detected',
                "Season \"{$season->name}\" costs (MWK " . number_format($season->total_costs) .
                ") are " . round((($season->total_costs / $avgCostPerSeason) - 1) * 100) .
                "% higher than your average. Review your input spending.",
                'medium'
            );
        }
    }

    private function createAlert(Farm $farm, ?FarmSeason $season, string $type, string $title, string $message, string $priority): void
    {
        $alert = FarmAlert::create([
            'farm_id'   => $farm->id,
            'season_id' => $season?->id,
            'type'      => $type,
            'title'     => $title,
            'message'   => $message,
            'priority'  => $priority,
        ]);

        // Broadcast farm alert to the farmer and admins
        event(new FarmAlertCreated($alert));

        // Send notification to the farm owner
        try {
            BroadcastNotification::dispatch(
                $farm->user_id,
                $title,
                $message,
                'farm',
                $priority === 'urgent' ? 'fas fa-exclamation-triangle' : 'fas fa-info-circle',
                $priority === 'urgent' ? '#dc2626' : 'var(--primary)',
                route('farm-records'),
            );
        } catch (\Throwable $e) {}
    }
}
