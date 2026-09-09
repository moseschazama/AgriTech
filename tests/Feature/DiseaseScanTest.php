<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DiseaseScanTest extends TestCase
{
    public function test_detect_returns_real_agronomic_report(): void
    {
        DB::beginTransaction();

        try {
            $user = User::firstOrFail();

            $photo = UploadedFile::fake()->image('maize-fall-armyworm.jpg');

            $this->actingAs($user)
                ->postJson('/diseases/detect', [
                    'photo' => $photo,
                    'farm_location' => 'Lilongwe District',
                ])
                ->assertOk()
                ->assertJson([
                    'success' => true,
                ])
                ->assertJsonPath('detection.crop', 'Maize')
                ->assertJsonPath('detection.disease', 'Fall Armyworm')
                ->assertJsonStructure([
                    'detection' => [
                        'id', 'disease', 'crop', 'confidence', 'severity',
                        'symptoms', 'cause', 'spread', 'seasonal_info',
                        'recommended_action', 'prevention', 'treatment', 'image_url',
                    ],
                ]);
        } finally {
            DB::rollBack();
        }
    }
}