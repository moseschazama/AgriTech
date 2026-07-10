<?php
// app/Http/Controllers/LessonPdfController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Serves lesson PDF files through Laravel (bypasses Apache/Nginx
 * permission issues with the storage symlink) and checks the user
 * is enrolled before allowing access.
 */
class LessonPdfController extends Controller
{
    public function serve(Request $request, string $path)
    {
        // Decode the path (was encoded in JS with encodeURIComponent)
        $filePath = urldecode($path);

        // Security: prevent directory traversal attacks
        $filePath = str_replace(["../", "..\\", "../", "..\\"], "", $filePath);

        // Check file exists in storage
        if (!Storage::disk("public")->exists($filePath)) {
            abort(404, "PDF file not found.");
        }

        // Check user is logged in and enrolled in a course that has this lesson
        if (auth()->check()) {
            $lesson = \App\Models\Lesson::where("pdf_path", $filePath)->first();

            if ($lesson) {
                $canAccess =
                    $lesson->is_free_preview ||
                    auth()
                        ->user()
                        ->enrollments()
                        ->where("course_id", $lesson->course_id)
                        ->whereIn("status", ["active", "completed"])
                        ->exists();

                if (!$canAccess) {
                    abort(
                        403,
                        "Please enroll in this course to access the lesson PDF.",
                    );
                }
            }
        } else {
            // Not logged in — only allow free preview PDFs
            $lesson = \App\Models\Lesson::where("pdf_path", $filePath)->first();
            if ($lesson && !$lesson->is_free_preview) {
                abort(403, "Please log in and enroll to access this PDF.");
            }
        }

        $fullPath = Storage::disk("public")->path($filePath);

        return response()->file($fullPath, [
            "Content-Type" => "application/pdf",
            "Content-Disposition" =>
                'inline; filename="' . basename($filePath) . '"',
            "Cache-Control" => "private, max-age=3600",
        ]);
    }
}
