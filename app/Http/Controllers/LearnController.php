<?php
// app/Http/Controllers/LearnController.php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseReview;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearnController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::published()
            ->with("instructor")
            ->search($request->get("q"))
            ->when($request->get("category"), fn($q, $c) => $q->category($c))
            ->when($request->get("access") === "free", fn($q) => $q->free())
            ->when(
                $request->get("sort"),
                function ($q, $sort) {
                    match ($sort) {
                        "newest" => $q->latest(),
                        "rating" => $q->orderByDesc("average_rating"),
                        "price" => $q->orderBy("price"),
                        default => $q->orderByDesc("total_enrolled"),
                    };
                },
                fn($q) => $q->orderByDesc("total_enrolled"),
            )
            ->paginate(12)
            ->withQueryString();

        $continueWatching = Auth::check()
            ? Auth::user()
                ->enrollments()
                ->where("status", "active")
                ->with("course")
                ->latest()
                ->limit(3)
                ->get()
            : collect();

        // NEW — real admin-uploaded PDF guides, newest first, max 8 shown
        $guides = \App\Models\CourseGuide::published()
            ->when(
                $request->get("guide_topic"),
                fn($q, $topic) => $q->topic($topic),
            )
            ->latest()
            ->limit(8)
            ->get();

        return view(
            "pages.learn",
            compact("courses", "continueWatching", "guides"),
        );
    }
    public function show(Course $course)
    {
        abort_if($course->status !== "published", 404);

        $course->load([
            "instructor",
            "lessons" => fn($q) => $q->orderBy("sort_order"),
            "reviews" => fn($q) => $q
                ->where("is_approved", true)
                ->with("user")
                ->latest()
                ->limit(10),
        ]);

        try {
            $course->increment("total_views");
        } catch (\Throwable $e) {
        }

        $enrollment = Auth::check()
            ? $course->enrollments()->where("user_id", Auth::id())->first()
            : null;

        return view("pages.course-detail", compact("course", "enrollment"));
    }
    public function enroll(Request $request, Course $course)
    {
        // Must be logged in (route already has auth middleware but guard anyway)
        if (!Auth::check()) {
            return redirect()
                ->route("login")
                ->with("error", "Please log in to enroll in courses.");
        }

        $user = Auth::user();

        // Already enrolled? Just go to the course.
        $existing = $course
            ->enrollments()
            ->where("user_id", $user->id)
            ->first();

        if ($existing) {
            return redirect()
                ->route("learn.show", $course)
                ->with(
                    "info",
                    "You are already enrolled in \"{$course->title}\".",
                );
        }

        // Only free courses can be enrolled this way
        if ($course->access_type !== "free") {
            return back()->with(
                "error",
                "This is a paid course. Please contact us to enroll.",
            );
        }

        try {
            // Create enrollment
            \App\Models\Enrollment::create([
                "user_id" => $user->id,
                "course_id" => $course->id,
                "status" => "active",
                "amount_paid" => 0,
            ]);

            // Increment total_enrolled on the course (non-blocking)
            try {
                $course->increment("total_enrolled");
            } catch (\Throwable $e) {
                // Column might not exist yet — ignore
            }

            // Create a welcome notification (non-blocking)
            try {
                \App\Models\Notification::create([
                    "user_id" => $user->id,
                    "title" => "🎓 Enrolled: " . $course->title,
                    "message" => "You are now enrolled in \"{$course->title}\". Start learning anytime!",
                    "type" => "lesson",
                    "icon" => "fas fa-graduation-cap",
                    "icon_color" => "var(--primary)",
                    "is_read" => false,
                ]);
            } catch (\Throwable $e) {
                // Notification failure should never block enrollment
            }

            return redirect()
                ->route("learn.show", $course)
                ->with(
                    "success",
                    "🎉 Enrolled in \"{$course->title}\"! Start your first lesson below.",
                );
        } catch (\Throwable $e) {
            return back()->with(
                "error",
                "Could not complete enrollment. Error: " . $e->getMessage(),
            );
        }
    }

    /**
     * Mark a lesson as watched/complete. Auto-issues a certificate
     * via Enrollment::completeLesson() once 100% of lessons are done.
     */
    public function completeLesson(Request $request, \App\Models\Lesson $lesson)
    {
        $user = Auth::user();

        // Find their enrollment for this lesson's course
        $enrollment = $lesson->course
            ->enrollments()
            ->where("user_id", $user->id)
            ->where("status", "active")
            ->first();

        if (!$enrollment) {
            return response()->json(
                ["success" => false, "message" => "Not enrolled"],
                403,
            );
        }

        try {
            // Record the lesson as complete
            $enrollment->completeLesson($lesson);

            $pct = $enrollment->fresh()->progressPercentage();
            $done = $enrollment->fresh()->status === "completed";

            return response()->json([
                "success" => true,
                "progress" => $pct,
                "completed" => $done,
                "message" => $done
                    ? "🎉 Course complete! Your certificate is ready."
                    : "Lesson complete! {$pct}% of the course done.",
            ]);
        } catch (\Throwable $e) {
            return response()->json(
                [
                    "success" => false,
                    "message" => "Error saving progress: " . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function submitReview(Request $request, Course $course)
    {
        $validated = $request->validate([
            "rating" => ["required", "integer", "min:1", "max:5"],
            "review" => ["nullable", "string", "max:1000"],
        ]);

        $enrollment = $course
            ->enrollments()
            ->where("user_id", Auth::id())
            ->firstOrFail(); // must be enrolled to review

        CourseReview::updateOrCreate(
            ["user_id" => Auth::id(), "course_id" => $course->id],
            [
                ...$validated,
                "enrollment_id" => $enrollment->id,
                "is_approved" => true,
            ],
        );

        return back()->with("success", "Thanks for your review!");
    }

    public function myCourses()
    {
        $enrollments = Auth::user()
            ->enrollments()
            ->with(["course.instructor", "course.lessons"])
            ->latest()
            ->paginate(12);

        return view("pages.my-courses", compact("enrollments"));
    }
    public function certificate(\App\Models\Enrollment $enrollment)
    {
        abort_unless($enrollment->user_id === Auth::id(), 403);
        abort_unless(
            $enrollment->certificate_number,
            404,
            "No certificate issued for this course yet.",
        );

        return view("pages.certificate", compact("enrollment"));
    }
}
