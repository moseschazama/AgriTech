<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the broadcast channels that your
| application supports. Broadcast channel authorization is handled
| by a simple policy check.
|
*/

// ── User-specific channel (each user sees only their own updates) ─────
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// ── Admin dashboard (all admins share this channel) ───────────────────
Broadcast::channel('admin.dashboard', function ($user) {
    return $user->isAdmin();
});

// ── Admin farm alerts ─────────────────────────────────────────────────
Broadcast::channel('admin.farm-alerts', function ($user) {
    return $user->isAdmin();
});

// ── Marketplace (all authenticated users can see marketplace updates) ─
Broadcast::channel('marketplace', function ($user) {
    return true;
});

// ── Innovation hub (all authenticated users) ──────────────────────────
Broadcast::channel('innovation.hub', function ($user) {
    return true;
});

// ── Disease alerts (all authenticated users for their district) ───────
Broadcast::channel('disease.alerts', function ($user) {
    return true;
});
