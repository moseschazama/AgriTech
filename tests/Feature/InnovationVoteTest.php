<?php

namespace Tests\Feature;

use App\Models\Innovation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InnovationVoteTest extends TestCase
{

    public function test_user_can_vote_once_per_innovation_per_round(): void
    {
        DB::beginTransaction();

        try {
            $user = User::firstOrFail();

            $innovation = Innovation::create([
                'user_id'  => $user->id,
                'title'    => 'Vote Test Innovation',
                'slug'     => 'vote-test-innovation-' . \Illuminate\Support\Str::random(6),
                'description' => 'Test innovation for one-vote rule',
                'category' => 'technology',
                'status'   => 'approved',
            ]);

            $this->actingAs($user)
                ->postJson(route('innovation.vote', $innovation))
                ->assertOk()
                ->assertJson(['voted' => true, 'already_voted' => false]);

            $this->assertTrue($user->hasVotedFor($innovation));
            $this->assertSame(1, $innovation->fresh()->vote_count);
            $this->assertSame(
                1,
                $innovation->votes()->where('user_id', $user->id)->count(),
            );

            // Second attempt must not add/remove a vote
            $this->actingAs($user)
                ->postJson(route('innovation.vote', $innovation))
                ->assertOk()
                ->assertJson(['voted' => false, 'already_voted' => true]);

            $this->assertSame(1, $innovation->fresh()->vote_count);
            $this->assertSame(
                1,
                $innovation->votes()->where('user_id', $user->id)->count(),
            );
        } finally {
            DB::rollBack();
        }
    }
}