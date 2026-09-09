<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SplashLoaderTest extends TestCase
{
    public function test_splash_only_on_first_visit_then_everything_updates(): void
    {
        DB::beginTransaction();

        try {
            // A fresh session on any page shows the splash
            $this->get(route('diseases'))
                ->assertOk()
                ->assertSee('data-splash="1"', false);

            // Any subsequent navigation in the SAME session hides the splash
            $this->get(route('diseases'))
                ->assertOk()
                ->assertSee('data-splash="0"', false);

            $this->get(route('diseases'))
                ->assertOk()
                ->assertSee('data-splash="0"', false);
        } finally {
            DB::rollBack();
        }
    }

    public function test_login_shows_splash_once_then_hides(): void
    {
        DB::beginTransaction();

        try {
            $user = User::create([
                'first_name' => 'Splash',
                'last_name'  => 'Test',
                'email'      => 'splash@test.dev',
                'phone'      => '+265991234000',
                'password'   => Hash::make('password123'),
                'role'       => 'farmer',
                'status'     => 'active',
                'district'   => 'Lilongwe',
            ]);

            $this->from(route('login'))
                ->post(route('login.submit'), [
                    'identifier' => $user->email,
                    'password'   => 'password123',
                ])
                ->assertRedirect(route('dashboard'));
            $this->assertAuthenticatedAs($user);

            // The first page load after login shows the splash exactly once
            $this->get(route('diseases'))
                ->assertOk()
                ->assertSee('data-splash="1"', false);

            // Normal navigation afterwards never flashes again
            $this->get(route('diseases'))
                ->assertOk()
                ->assertSee('data-splash="0"', false);
        } finally {
            DB::rollBack();
        }
    }
}