<?php
// app/Http/Controllers/InnovationController.php

namespace App\Http\Controllers;

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

        $districts = District::with('tradingCentres')->orderBy('name')->get();

        return view('pages.innovation', compact('innovations', 'activeCompetition', 'districts'));
    }

    public function show(Innovation $innovation)
    {
        $innovation->load('user');
        $innovation->recordView();

        $hasVoted = Auth::check() && Auth::user()->hasVotedFor($innovation);

        return view('pages.innovation-detail', compact('innovation', 'hasVoted'));
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
     * Toggle vote — one vote per user per innovation, AJAX-friendly.
     */
    public function vote(Innovation $innovation)
    {
        $voted = $innovation->toggleVote(Auth::user());

        return response()->json([
            'voted'      => $voted,
            'vote_count' => $innovation->fresh()->vote_count,
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
