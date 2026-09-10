<?php

// app/Support/CategoryIcons.php

namespace App\Support;

/**
 * Single source of truth for Font Awesome class names that replace the
 * category emojis used across the market, courses and innovation screens.
 * Keeps every view consistent and the markup professional (no emoji glyphs).
 */
class CategoryIcons
{
    private const COURSE = [
        "soil_crops" => "fa-seedling",
        "livestock" => "fa-cow",
        "agri_tech" => "fa-robot",
        "agribusiness" => "fa-chart-line",
        "organic" => "fa-leaf",
        "irrigation" => "fa-droplet",
        "post_harvest" => "fa-wheat-awn",
        "disease_control" => "fa-microscope",
    ];

    private const PRODUCT = [
        "seeds" => "fa-seedling",
        "fertilizer" => "fa-flask",
        "produce" => "fa-apple-whole",
        "livestock" => "fa-cow",
        "tools" => "fa-wrench",
        "equipment" => "fa-gear",
        "chemicals" => "fa-mortar-pestle",
        "other" => "fa-box",
    ];

    private const INNOVATION = [
        "water_management" => "fa-droplet",
        "technology" => "fa-mobile-screen",
        "infrastructure" => "fa-building",
        "crop_solutions" => "fa-seedling",
        "energy" => "fa-bolt",
        "post_harvest" => "fa-wheat-awn",
        "livestock" => "fa-cow",
        "business" => "fa-chart-line",
    ];

    public static function course(?string $category): string
    {
        return self::COURSE[$category] ?? "fa-book-open";
    }

    public static function product(?string $category): string
    {
        return self::PRODUCT[$category] ?? "fa-box";
    }

    public static function innovation(?string $category): string
    {
        return self::INNOVATION[$category] ?? "fa-lightbulb";
    }
}