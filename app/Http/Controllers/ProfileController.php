<?php
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Farm;
use App\Models\FarmProduction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $farm = $user->farm;

        $certificates = $user->enrollments()
            ->whereNotNull('certificate_number')
            ->with('course')
            ->get();

        $listings = $user->products()->withCount('orderItems')->latest()->get();

        $districts = District::with('tradingCentres')->orderBy('name')->get();

        return view('pages.profile', compact('user', 'farm', 'certificates', 'listings', 'districts'));
    }

    public function updatePersonalInfo(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name'  => ['required', 'string', 'max:100'],
            'email'      => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'district'   => ['required', 'string'],
            'district'   => ['nullable', 'string'],
            'avatar'     => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('avatar')) {
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return back()->with('success', 'Profile updated.');
    }

    public function updateNotificationPreferences(Request $request)
    {
        $validated = $request->validate([
            'sms_alerts'   => ['boolean'],
            'email_alerts' => ['boolean'],
        ]);

        Auth::user()->update($validated);

        return back()->with('success', 'Notification preferences saved.');
    }

    public function updateFarm(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:150'],
            'size_hectares'     => ['nullable', 'numeric', 'min:0'],
            'primary_crops'     => ['nullable', 'array'],
            'livestock'         => ['nullable', 'array'],
            'irrigation_type'   => ['required', 'in:rain_fed,drip,sprinkler,flood,borehole'],
        ]);

        $farm = $user->farm ?? new Farm(['user_id' => $user->id, 'district' => $user->district]);
        $farm->fill($validated)->save();

        return back()->with('success', 'Farm details updated.');
    }

    public function addProductionRecord(Request $request)
    {
        $farm = Auth::user()->farm;
        abort_unless($farm, 422, 'Please set up your farm profile first.');

        $validated = $request->validate([
            'season'             => ['required', 'string', 'max:20'],
            'crop'               => ['required', 'string', 'max:100'],
            'yield_per_hectare'  => ['nullable', 'numeric', 'min:0'],
            'total_yield'        => ['nullable', 'numeric', 'min:0'],
            'revenue'            => ['nullable', 'numeric', 'min:0'],
            'planted_at'         => ['nullable', 'date'],
            'harvested_at'       => ['nullable', 'date', 'after_or_equal:planted_at'],
        ]);

        FarmProduction::create([...$validated, 'farm_id' => $farm->id]);

        return back()->with('success', 'Production record added.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('success', 'Password updated.');
    }

    public function toggleTwoFactor(Request $request)
    {
        $user = Auth::user();
        $user->update(['two_factor_enabled' => !$user->two_factor_enabled]);

        return back()->with('success', 'Two-factor authentication ' .
            ($user->two_factor_enabled ? 'enabled.' : 'disabled.'));
    }
}
