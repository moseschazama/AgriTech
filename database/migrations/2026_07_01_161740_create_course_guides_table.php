<?php
// database/migrations/2024_01_01_000008_create_course_guides_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create("course_guides", function (Blueprint $table) {
            $table->id();

            // Who uploaded it (always an admin)
            $table
                ->foreignId("uploaded_by")
                ->constrained("users")
                ->cascadeOnDelete();

            // Optionally link a guide to a specific course (nullable — some guides are general)
            $table
                ->foreignId("course_id")
                ->nullable()
                ->constrained("courses")
                ->nullOnDelete();

            $table->string("title");
            $table->text("description")->nullable();

            // Topic/category so farmers can filter guides exactly like courses
            $table
                ->enum("topic", [
                    "soil_crops",
                    "livestock",
                    "agri_tech",
                    "agribusiness",
                    "organic",
                    "irrigation",
                    "post_harvest",
                    "disease_control",
                    "general",
                ])
                ->default("general");

            // The actual uploaded file
            $table->string("file_path"); // storage path
            $table->string("file_name"); // original filename shown to user
            $table->unsignedInteger("file_size"); // bytes, for showing "2.4 MB"
            $table->unsignedSmallInteger("page_count")->nullable();

            $table->unsignedInteger("download_count")->default(0);
            $table->boolean("is_published")->default(true);

            $table->timestamps();

            $table->index(["topic", "is_published"]);
            $table->index("course_id");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("course_guides");
    }
};
