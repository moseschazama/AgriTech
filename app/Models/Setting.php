<?php
// app/Models/Setting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Setting — simple key/value store for platform-wide toggles managed
 * from the Admin Panel's Settings tab (maintenance mode, registrations
 * open/closed, SMS enabled, etc). Replaces the old config()-based stub
 * that never actually persisted anything.
 */
class Setting extends Model
{
    protected $primaryKey = "key";
    public $incrementing = false;
    protected $keyType = "string";

    protected $fillable = ["key", "value"];

    /**
     * Get a setting value, decoded from its stored string form back
     * into a real PHP type (booleans stored as "1"/"" round-trip cleanly).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $row = static::find($key);

        if (!$row) {
            return $default;
        }

        // Booleans were stored as "1" or "0" — decode them back
        return match ($row->value) {
            "1" => true,
            "0" => false,
            default => $row->value,
        };
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ["key" => $key],
            ["value" => is_bool($value) ? ($value ? "1" : "0") : $value],
        );
    }
}
