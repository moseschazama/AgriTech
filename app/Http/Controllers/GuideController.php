<?php
// app/Http/Controllers/GuideController.php

namespace App\Http\Controllers;

use App\Models\CourseGuide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * GuideController — handles the PDF Guides feature shown on the
 * Learning Center page. Admins upload guides here; any visitor
 * (logged in or not) can download a published guide.
 */
class GuideController extends Controller
{
    /**
     * Stream/redirect a guide file to the user and increment its
     * download counter. Public route — no auth required, since field
     * guides should be freely downloadable even by non-registered farmers.
     */
    public function download(CourseGuide $guide)
    {
        abort_unless($guide->is_published, 404);

        if (!Storage::disk("public")->exists($guide->file_path)) {
            abort(404, "This file is no longer available.");
        }

        $guide->recordDownload();

        return Storage::disk("public")->download(
            $guide->file_path,
            $guide->file_name,
        );
    }

    // ── Admin-only below ──────────────────────────────────────────────

    /**
     * Handle the admin "Upload New Guide" form submission from the
     * admin panel's Courses tab.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            "title" => ["required", "string", "max:150"],
            "description" => ["nullable", "string", "max:500"],
            "topic" => [
                "required",
                "in:soil_crops,livestock,agri_tech,agribusiness,organic,irrigation,post_harvest,disease_control,general",
            ],
            "course_id" => ["nullable", "exists:courses,id"],
            "page_count" => ["nullable", "integer", "min:1"],
            "file" => ["required", "file", "mimes:pdf", "max:20480"], // 20MB max
        ]);

        $file = $request->file("file");
        $path = $file->store("guides", "public");

        CourseGuide::create([
            "uploaded_by" => $request->user()->id,
            "course_id" => $validated["course_id"] ?? null,
            "title" => $validated["title"],
            "description" => $validated["description"] ?? null,
            "topic" => $validated["topic"],
            "file_path" => $path,
            "file_name" => $file->getClientOriginalName(),
            "file_size" => $file->getSize(),
            "page_count" => $validated["page_count"] ?? null,
            "is_published" => true,
        ]);

        return back()->with(
            "success",
            "\"{$validated["title"]}\" uploaded and is now available for download.",
        );
    }

    /**
     * Toggle a guide's published state without deleting it — lets an
     * admin temporarily hide an outdated guide.
     */
    public function togglePublish(CourseGuide $guide)
    {
        $guide->update(["is_published" => !$guide->is_published]);

        return back()->with(
            "success",
            $guide->is_published
                ? "\"{$guide->title}\" is now visible to farmers."
                : "\"{$guide->title}\" has been hidden.",
        );
    }

    /**
     * Permanently remove a guide and its file from storage.
     */
    public function destroy(CourseGuide $guide)
    {
        $title = $guide->title;
        $guide->deleteWithFile();

        return back()->with("success", "\"{$title}\" has been removed.");
    }
}
