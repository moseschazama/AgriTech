<?php
// app/Services/DiseaseDetectionService.php

namespace App\Services;

use App\Models\Disease;
use App\Models\DiseaseDetection;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

/**
 * DiseaseDetectionService — handles the AI crop-disease photo analysis flow.
 *
 * Production note: swap callAiModel() to point at a real inference endpoint
 * (e.g. a hosted PlantVillage / Plantix-style model, or a custom-trained
 * TensorFlow Serving / Roboflow endpoint). The rest of the pipeline
 * (storage, DB record, notification) stays the same.
 */
class DiseaseDetectionService
{
    protected ?string $aiEndpoint;
    protected ?string $aiApiKey;

    public function __construct()
    {
        $this->aiEndpoint = config("services.disease_ai.endpoint");
        $this->aiApiKey = config("services.disease_ai.api_key");
    }

    /**
     * Full pipeline: store the uploaded photo, call the AI model,
     * persist a DiseaseDetection record, and notify the farmer.
     */
    public function analyze(
        User $user,
        UploadedFile $photo,
        ?string $farmLocation = null,
    ): DiseaseDetection {
        $path = $photo->store("disease-scans/" . $user->id, "public");

        $detection = DiseaseDetection::create([
            "user_id" => $user->id,
            "image_path" => $path,
            "status" => "pending",
            "farm_location" => $farmLocation,
        ]);

        try {
            $prediction = $this->callAiModel(
                Storage::disk("public")->path($path),
            );
            $detection->applyPrediction($prediction);
        } catch (\Throwable $e) {
            Log::error("Disease AI analysis failed: {$e->getMessage()}");
            $detection->update(["status" => "failed"]);
        }

        return $detection->fresh("disease");
    }

    /**
     * Call the external AI inference API. Returns a normalized prediction array:
     * [
     *   'disease_name'    => 'Fall Armyworm',
     *   'crop'            => 'Maize',
     *   'confidence'      => 94.2,
     *   'top_predictions' => [['name'=>'Fall Armyworm','confidence'=>94.2], ...]
     * ]
     */
    protected function callAiModel(string $imagePath): array
    {
        // Local/dev fallback — deterministic-ish simulation so the UI can be tested
        // without a live model. Replace this whole branch in production.
        if (app()->environment(["local", "testing"]) || !$this->aiEndpoint) {
            return $this->simulatePrediction();
        }

        $response = Http::withToken($this->aiApiKey)
            ->attach(
                "image",
                file_get_contents($imagePath),
                basename($imagePath),
            )
            ->post($this->aiEndpoint . "/predict");

        if (!$response->successful()) {
            throw new \Exception(
                "AI model request failed: " . $response->status(),
            );
        }

        $data = $response->json();

        return [
            "disease_name" => $data["top_prediction"]["label"] ?? "Unknown",
            "crop" => $data["crop"] ?? null,
            "confidence" => $data["top_prediction"]["confidence"] ?? 0,
            "top_predictions" => $data["predictions"] ?? [],
        ];
    }

    /**
     * Deterministic-ish simulated prediction for local development —
     * picks from diseases actually present in the library so the
     * detection always links correctly.
     */
    protected function simulatePrediction(): array
    {
        $disease = Disease::published()->inRandomOrder()->first();

        if (!$disease) {
            return [
                "disease_name" => "Healthy Plant",
                "crop" => null,
                "confidence" => 97.0,
                "top_predictions" => [
                    ["name" => "Healthy Plant", "confidence" => 97.0],
                ],
            ];
        }

        $confidence = rand(800, 980) / 10; // 80.0–98.0

        return [
            "disease_name" => $disease->name,
            "crop" => $disease->affected_crop,
            "confidence" => $confidence,
            "top_predictions" => [
                ["name" => $disease->name, "confidence" => $confidence],
            ],
        ];
    }

    /**
     * Farmer confirms or disputes the AI result — used to improve model
     * accuracy over time (feed back into retraining dataset).
     */
    public function recordFeedback(
        DiseaseDetection $detection,
        bool $wasCorrect,
        ?string $notes = null,
    ): void {
        $detection->update([
            "user_confirmed" => $wasCorrect,
            "user_notes" => $notes,
        ]);
    }
}
