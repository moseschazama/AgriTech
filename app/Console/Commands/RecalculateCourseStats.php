<?php
/*
|--------------------------------------------------------------------------
| ARTISAN COMMAND — app/Console/Commands/RecalculateCourseStats.php
|--------------------------------------------------------------------------
|
| Run this ONCE after deploying the storeLesson fix to repair all existing
| courses that currently show wrong lesson counts and durations.
|
| Usage:
|   php artisan courses:recalculate-stats
|
| Then DELETE this file — you won't need it again after the one-time fix.
*/

namespace App\Console\Commands;

use App\Models\Course;
use Illuminate\Console\Command;

class RecalculateCourseStats extends Command
{
    protected $signature = "courses:recalculate-stats";
    protected $description = "Recalculate total_lessons and total_duration_minutes for all courses";

    public function handle(): void
    {
        $courses = Course::withoutGlobalScopes()->get();
        $bar = $this->output->createProgressBar($courses->count());

        $this->info("Recalculating stats for {$courses->count()} courses...");
        $bar->start();

        $fixed = 0;
        foreach ($courses as $course) {
            $old_lessons = $course->total_lessons;
            $old_duration = $course->total_duration_minutes;

            $course->recalculateStats();
            $course->refresh();

            if (
                $course->total_lessons !== $old_lessons ||
                $course->total_duration_minutes !== $old_duration
            ) {
                $fixed++;
                $this->newLine();
                $this->line(
                    "  Fixed: {$course->title} → {$course->total_lessons} lessons, {$course->total_duration_minutes} mins",
                );
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Done. Fixed {$fixed} course(s) with incorrect stats.");
    }
}
