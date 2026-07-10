<?php
// app/Http/Controllers/DiseaseController.php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Disease;
use App\Models\DiseaseAlert;
use App\Models\DiseaseDetection;
use App\Services\DiseaseDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiseaseController extends Controller
{
    public function __construct(protected DiseaseDetectionService $detectionService) {}

    /**
     * Disease library + AI scanner landing page.
     */
    public function index(Request $request)
    {
        $diseases = Disease::published()
            ->search($request->get('q'))
            ->when($request->get('crop'), fn ($q, $c) => $q->forCrop($c))
            ->when($request->get('severity'), fn ($q, $s) => $q->severity($s))
            ->orderByDesc('view_count')
            ->paginate(9)
            ->withQueryString();

        $activeAlerts = DiseaseAlert::active()
            ->when(
                Auth::check() && Auth::user()->district,
                fn ($q) => $q->forDistrict(Auth::user()->district)
            )
            ->latest()
            ->limit(4)
            ->get();

        $crops = Disease::published()->distinct()->pluck('affected_crop');

        $districts = District::with('tradingCentres')->orderBy('name')->get();

        return view('pages.diseases', compact('diseases', 'activeAlerts', 'crops', 'districts'));
    }

    public function show(Disease $disease)
    {
        $disease->recordView();
        return view('pages.disease-detail', compact('disease'));
    }

    /**
     * Handle AI photo upload + analysis. Returns JSON for the
     * frontend's "Scanning..." → result-card UX.
     */
    public function detect(Request $request)
    {
        $validated = $request->validate([
            'photo'        => ['required', 'image', 'max:10240'], // 10MB
            'farm_location'=> ['nullable', 'string', 'max:150'],
        ]);

        $detection = $this->detectionService->analyze(
            user: Auth::user(),
            photo: $request->file('photo'),
            farmLocation: $validated['farm_location'] ?? null
        );

        if ($detection->status === 'failed') {
            return response()->json([
                'success' => false,
                'message' => 'Could not analyze the image. Please try again with a clearer photo.',
            ], 422);
        }

        return response()->json([
            'success'    => true,
            'detection'  => [
                'id'          => $detection->id,
                'disease'     => $detection->detected_disease,
                'crop'        => $detection->affected_crop,
                'confidence'  => $detection->confidence_score,
                'severity'    => $detection->severity,
                'disease_url' => $detection->disease ? route('diseases.show', $detection->disease) : null,
                'image_url'   => $detection->image_url,
            ],
        ]);
    }

    /**
     * Farmer confirms whether the AI diagnosis was accurate — used to
     * monitor and improve model accuracy over time.
     */
    public function feedback(Request $request, DiseaseDetection $detection)
    {
        abort_unless($detection->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'was_correct' => ['required', 'boolean'],
            'notes'       => ['nullable', 'string', 'max:500'],
        ]);

        $this->detectionService->recordFeedback(
            $detection, $validated['was_correct'], $validated['notes'] ?? null
        );

        return back()->with('success', 'Thanks for the feedback — this helps improve our AI!');
    }

    public function myDetections()
    {
        $detections = DiseaseDetection::where('user_id', Auth::id())
            ->with('disease')
            ->latest()
            ->paginate(10);

        return view('pages.my-detections', compact('detections'));
    }

    public function subscribeAlerts(Request $request)
    {
        $request->validate(['phone' => ['required', 'string']]);

        if (Auth::check()) {
            Auth::user()->update(['sms_alerts' => true, 'phone' => $request->phone]);
        }

        return back()->with('success', 'You are now subscribed to disease outbreak alerts!');
    }
}
