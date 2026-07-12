<?php

declare(strict_types=1);

use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns a protected preview endpoint instead of a storage URL', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $section = CourseSection::factory()->create();
    $lesson = Lesson::factory()->for($section, 'section')->create();
    $lesson
        ->addMediaFromString('video content')
        ->usingFileName('lesson.mp4')
        ->toMediaCollection('lesson-video');

    $this->getJson("/api/admin/course-sections/{$section->id}/lessons")
        ->assertOk()
        ->assertJsonPath('data.0.lessonVideo', route('admin.lessons.video', ['lesson' => $lesson->id]));
});

it('streams an uploaded lesson video only for administrators', function (): void {
    $section = CourseSection::factory()->create();
    $lesson = Lesson::factory()->for($section, 'section')->create();
    $lesson
        ->addMediaFromString('video content')
        ->usingFileName('lesson.mp4')
        ->toMediaCollection('lesson-video');

    $this->get("/api/admin/lessons/{$lesson->id}/video")
        ->assertUnauthorized();

    Sanctum::actingAs(User::factory()->student()->create());

    $this->get("/api/admin/lessons/{$lesson->id}/video")
        ->assertForbidden();

    Sanctum::actingAs(User::factory()->admin()->create());

    $this->get("/api/admin/lessons/{$lesson->id}/video")
        ->assertOk()
        ->assertHeader('content-disposition', 'inline; filename="lesson.mp4"');
});
