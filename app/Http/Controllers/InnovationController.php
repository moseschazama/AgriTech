<?php
// app/Http/Controllers/InnovationController.php

namespace App\Http\Controllers;

use App\Events\InnovationVoteUpdated;
use App\Models\District;
use App\Models\Innovation;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InnovationController extends Controller
{
    public function index(Request $request)
    {
        $innovations = Innovation::approved()
            ->with('user')
            ->when($request->get('category'), fn ($q, $c) => $q->category($c))
            ->topVoted()
            ->paginate(9)
            ->withQueryString();

        $activeCompetition = Competition::where('status', 'active')
            ->where('ends_at', '>=', now())
            ->latest()
            ->first();

        $leaders = Innovation::approved()
            ->with('user')
            ->when(
                $activeCompetition,
                fn ($q) => $q->where('competition_id', $activeCompetition->id),
            )
            ->orderByDesc('vote_count')
            ->limit(3)
            ->get();

        $pastWinners = Innovation::whereNotNull('winner_position')
            ->with('user')
            ->orderByDesc('won_at')
            ->orderByRaw('CASE winner_position WHEN 1 THEN 0 WHEN 2 THEN 1 WHEN 3 THEN 2 ELSE 3 END')
            ->limit(3)
            ->get();

        $districts = District::with('tradingCentres')->orderBy('name')->get();

        return view('pages.innovation', compact(
            'innovations',
            'activeCompetition',
            'leaders',
            'pastWinners',
            'districts',
        ));
    }

    public function show(Innovation $innovation)
    {
        $innovation->load('user');
        $innovation->recordView();

        $hasVoted = Auth::check() && Auth::user()->hasVotedFor($innovation);

        return view('pages.innovation-detail', compact('innovation', 'hasVoted'));
    }

    /**
     * Public results list — download how each entry performed since the competition call.
     * Available once a round is closed or winners have been published.
     */
    public function results(Competition $competition)
    {
        $hasWinners = $competition->winners()->count() > 0;
        abort_unless($competition->status === 'closed' || $hasWinners, 404);

        $rows = $competition->entriesWithPerformance();

        $csv = fopen('php://temp/maxmemory:1048576', 'rw');
        fputcsv($csv, [
            'Position', 'Prize (MWK)', 'Innovation', 'Category',
            'Farmer', 'District', 'Votes', 'Views', 'Impact', 'Submitted',
        ]);

        $rows->each(function ($row) use ($csv) {
            fputcsv($csv, [
                $row['position'] ?: '',
                $row['position'] && $row['prize'] !== null
                    ? number_format((float) $row['prize'])
                    : '',
                $row['title'],
                $row['category'],
                $row['farmer'],
                $row['district'],
                $row['votes'],
                $row['views'],
                $row['impact'],
                $row['submitted_at'],
            ]);
        });

        rewind($csv);
        $output = stream_get_contents($csv);
        fclose($csv);

        return response($output, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' =>
                'attachment; filename="' . \Illuminate\Support\Str::slug($competition->title) . '-results.csv"',
        ]);
    }

    /**
     * Submit a new innovation — goes to pending_review queue for admin moderation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'                  => ['required', 'string', 'max:150'],
            'category'               => ['required', 'in:water_management,technology,infrastructure,crop_solutions,energy,post_harvest,livestock,business'],
            'description'            => ['required', 'string', 'max:3000'],
            'impact_summary'         => ['nullable', 'string', 'max:200'],
            'estimated_cost'         => ['nullable', 'string', 'max:100'],
            'implementation_steps'   => ['nullable', 'string'],
            'district'               => ['nullable', 'string'],
            'in_competition'         => ['boolean'],
            'competition_id'         => ['nullable', 'exists:competitions,id'],
            'images'                 => ['nullable', 'array', 'max:5'],
            'images.*'               => ['image', 'max:4096'],
        ]);

        // Enforce that the competition is actually open if entering one
        if (!empty($validated['in_competition']) && !empty($validated['competition_id'])) {
            $competition = Competition::findOrFail($validated['competition_id']);
            abort_unless($competition->isOpen(), 422, 'This competition is no longer accepting entries.');
            $competition->increment('entry_count');
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imagePaths[] = $image->store('innovations/' . Auth::id(), 'public');
            }
        }

        $innovation = Innovation::create([
            ...$validated,
            'user_id' => Auth::id(),
            'status'  => 'pending_review',
            'images'  => $imagePaths,
        ]);

        Auth::user()->increment('total_innovations');

        return redirect()
            ->route('innovation')
            ->with('success', "\"{$innovation->title}\" submitted! It will appear once reviewed by our team.");
    }

    /**
     * Vote — one vote per user per innovation per round, add only.
     */
    public function vote(Innovation $innovation)
    {
        $voted = $innovation->castVote(Auth::user());

        // Broadcast vote update to all connected users
        event(new InnovationVoteUpdated($innovation->fresh(), $voted));

        return response()->json([
            'voted'         => $voted,
            'already_voted' => !$voted,
            'vote_count'    => $innovation->fresh()->vote_count,
            'message'       => $voted
                ? 'Vote recorded! You can vote once per innovation per round.'
                : 'You already voted for this innovation this round.',
        ]);
    }

    public function myInnovations()
    {
        $innovations = Innovation::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('pages.my-innovations', compact('innovations'));
    }
}
