<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('allows a purchased user to access a published lesson', function (): void {
    $user = User::factory()->student()->create();
    $course = Course::factory()->published()->create();
    $section = CourseSection::factory()->for($course)->create();
    $lesson = Lesson::factory()->published()->for($section, 'section')->create([
        'content' => 'Protected lesson content',
        'video_url' => 'https://video.example.test/lesson',
    ]);

    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);
    Sanctum::actingAs($user);

    $this->getJson("/api/my/courses/{$course->id}/lessons/{$lesson->id}")
        ->assertOk()
        ->assertJsonPath('data.id', $lesson->id)
        ->assertJsonPath('data.content', 'Protected lesson content')
        ->assertJsonPath('data.videoUrl', 'https://video.example.test/lesson');
});

it('denies an unpurchased user from protected lesson content', function (): void {
    $user = User::factory()->student()->create();
    $course = Course::factory()->published()->create();
    $section = CourseSection::factory()->for($course)->create();
    $lesson = Lesson::factory()->published()->for($section, 'section')->create();

    Sanctum::actingAs($user);

    $this->getJson("/api/my/courses/{$course->id}/lessons/{$lesson->id}")
        ->assertForbidden();
});

it('returns not found for mismatched course and lesson', function (): void {
    $user = User::factory()->student()->create();
    $course = Course::factory()->published()->create();
    $otherCourse = Course::factory()->published()->create();
    $section = CourseSection::factory()->for($otherCourse)->create();
    $lesson = Lesson::factory()->published()->for($section, 'section')->create();

    CourseAccess::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);
    Sanctum::actingAs($user);

    $this->getJson("/api/my/courses/{$course->id}/lessons/{$lesson->id}")
        ->assertNotFound();
});

it('requires authentication for protected lesson content', function (): void {
    $course = Course::factory()->published()->create();
    $section = CourseSection::factory()->for($course)->create();
    $lesson = Lesson::factory()->published()->for($section, 'section')->create();

    $this->getJson("/api/my/courses/{$course->id}/lessons/{$lesson->id}")
        ->assertUnauthorized();
});
