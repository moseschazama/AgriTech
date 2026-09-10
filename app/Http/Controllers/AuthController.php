<?php

namespace App\Http\Controllers;

use App\Events\UserRegistered;
use App\Models\District;
use App\Models\User;
use App\Models\Farm;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(protected SmsService $sms) {}

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route("dashboard");
        }
        return view("pages.auth", [
            "activeTab" => "login",
            "districts" => District::with("tradingCentres")->orderBy("name")->get(),
        ]);
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route("dashboard");
        }
        return view("pages.auth", [
            "activeTab" => "register",
            "districts" => District::with("tradingCentres")->orderBy("name")->get()
        ]);
    }

    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                "identifier" => ["required", "string"],
                "password" => ["required", "string"],
                "remember" => ["boolean"],
            ]);

            $field = filter_var($validated["identifier"], FILTER_VALIDATE_EMAIL)
                ? "email"
                : "phone";

            $identifier = $field === "phone"
                ? $this->normalizePhone($validated["identifier"])
                : $validated["identifier"];

            $credentials = [$field => $identifier, "password" => $validated["password"]];

            if (!Auth::attempt($credentials, $validated["remember"] ?? false)) {
                return back()
                    ->withErrors(["identifier" => "These credentials do not match our records."])
                    ->withInput($request->only("identifier", "remember", "_form"));
            }

            $user = Auth::user();

            if ($user->status === "suspended") {
                Auth::logout();
                return back()
                    ->withErrors(["identifier" => "Your account has been suspended. Contact support."])
                    ->withInput($request->only("identifier", "remember", "_form"));
            }

            $request->session()->regenerate();

            $user->update([
                "last_login_at" => now(),
                "last_login_ip" => $request->ip(),
            ]);

            return redirect()->intended(
                $user->isAdmin() ? route("admin.index") : route("dashboard"),
            );
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error("Login error: " . $e->getMessage());
            return back()
                ->with("error", "An unexpected error occurred. Please try again.")
                ->withInput($request->except("password"));
        }
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                "first_name" => ["required", "string", "max:100"],
                "last_name" => ["required", "string", "max:100"],
                "email" => ["nullable", "email", "unique:users,email"],
                "phone" => [
                    "required",
                    "string",
                    "unique:users,phone",
                    function ($attribute, $value, $fail) {
                        $cleaned = preg_replace("/[\s\-\+\(\)]/", "", $value);
                        if (!preg_match('/^\+?[0-9]{9,15}$/', $cleaned)) {
                            $fail("Please enter a valid phone number (e.g. +265 99 123 4567).");
                        }
                    },
                ],
                "password" => ["required", "string", "min:8", "confirmed"],
                "district" => ["required", "string"],
                "farm_type" => [
                    "required",
                    Rule::in(["small_scale", "commercial", "livestock", "mixed", "organic", "agribusiness"]),
                ],
                "trading_centre" => ["nullable", "string", "max:200"],
            ]);

            $user = User::create([
                "first_name" => $validated["first_name"],
                "last_name" => $validated["last_name"],
                "email" => $validated["email"],
                "phone" => $this->normalizePhone($validated["phone"]),
                "password" => Hash::make($validated["password"]),
                "role" => "farmer",
                "status" => "active",
                "district" => $validated["district"],
                "trading_centre" => $validated["trading_centre"] ?? null,
            ]);

            Farm::create([
                "user_id" => $user->id,
                "name" => "{$user->first_name}'s Farm",
                "farm_type" => $validated["farm_type"],
                "district" => $validated["district"],
            ]);

            Auth::login($user);

            // Broadcast new user registration to admins
            event(new UserRegistered($user));

            // Notify all admins about new registration
            try {
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    \App\Jobs\BroadcastNotification::dispatch(
                        $admin->id,
                        '👤 New Farmer Registered',
                        "{$user->full_name} from {$user->district} just joined.",
                        'system',
                        'fas fa-user-plus',
                        'var(--primary)',
                        route('admin.farmers'),
                    );
                }
            } catch (\Throwable $e) {}

            try {
                $this->sms->send(
                    phone: $user->phone,
                    message: "Welcome to AgriTech Pro, {$user->first_name}! 🌱 Explore courses, the marketplace and more at agritechpro.zm",
                    type: "custom",
                    recipient: $user,
                );
            } catch (\Exception $e) {
                Log::warning("Welcome SMS failed for user {$user->id}: " . $e->getMessage());
            }

            return redirect()
                ->route("dashboard")
                ->with("success", "Welcome to AgriTech Pro, {$user->first_name}! 🌱");
        } catch (ValidationException $e) {
            if ($request->expectsJson()) {
                return response()->json(["errors" => $e->errors()], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            Log::error("Registration error: " . $e->getMessage());
            return back()
                ->with("error", "Registration failed due to a system error. Please try again.")
                ->withInput($request->except("password", "password_confirmation"));
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route("home");
    }

    public function sendResetOtp(Request $request)
    {
        try {
            $request->validate(["phone" => "required|string"]);

            $user = User::where("phone", $this->normalizePhone($request->phone))->first();

            if ($user) {
                $code = str_pad((string) random_int(0, 999999), 6, "0", STR_PAD_LEFT);
                $user->update([
                    "two_factor_code" => Hash::make($code),
                    "two_factor_expires_at" => now()->addMinutes(10),
                ]);
                try {
                    $this->sms->sendOtp($user, $code);
                } catch (\Exception $e) {
                    Log::error("OTP SMS failed for user {$user->id}: " . $e->getMessage());
                }
            }

            return back()->with("success", "If that number is registered, a code has been sent.");
        } catch (\Exception $e) {
            Log::error("Send reset OTP error: " . $e->getMessage());
            return back()->with("error", "Failed to send reset code. Please try again.");
        }
    }

    public function verifyResetOtp(Request $request)
    {
        try {
            $validated = $request->validate([
                "phone" => ["required", "string"],
                "code" => ["required", "string", "size:6"],
                "password" => ["required", "string", "min:8", "confirmed"],
            ]);

            $user = User::where("phone", $this->normalizePhone($validated["phone"]))->first();

            if (!$user || !$user->two_factor_expires_at || $user->two_factor_expires_at->isPast() || !Hash::check($validated["code"], $user->two_factor_code)) {
                return back()->withErrors(["code" => "Invalid or expired code."]);
            }

            $user->update([
                "password" => Hash::make($validated["password"]),
                "two_factor_code" => null,
                "two_factor_expires_at" => null,
            ]);

            return redirect()->route("login")->with("success", "Password reset! You can now sign in.");
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error("Verify reset OTP error: " . $e->getMessage());
            return back()->with("error", "Failed to verify code. Please try again.");
        }
    }

    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace("/\D/", "", $phone);
        if (str_starts_with($phone, "0")) {
            $phone = "265" . substr($phone, 1);
        }
        if (strlen($phone) === 9) {
            $phone = "265" . $phone;
        }
        return "+" . ltrim($phone, "+");
    }
}
