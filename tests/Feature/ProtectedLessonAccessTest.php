<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('returns protected lesson content for purchased course users', function (): void {
    $user = User::factory()->student()->create();
    Sanctum::actingAs($user);

    $course = Course::factory()->published()->create();
    $section = CourseSection::factory()->for($course)->create();
    $lesson = Lesson::factory()->for($section, 'section')->published()->create([
        'title' => ['en' => 'Protected Lesson', 'ar' => null],
        'content' => ['en' => 'Purchased lesson content', 'ar' => null],
        'video_url' => 'https://video.example.com/private.mp4',
    ]);
    CourseAccess::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    $this->getJson("/api/my/courses/{$course->id}/lessons/{$lesson->id}")
        ->assertOk()
        ->assertJsonPath('data.title', 'Protected Lesson')
        ->assertJsonPath('data.content', 'Purchased lesson content')
        ->assertJsonPath('data.videoUrl', 'https://video.example.com/private.mp4');
});

it('denies protected lesson content for users without course access', function (): void {
    Sanctum::actingAs(User::factory()->student()->create());

    $course = Course::factory()->published()->create();
    $section = CourseSection::factory()->for($course)->create();
    $lesson = Lesson::factory()->for($section, 'section')->published()->create();

    $this->getJson("/api/my/courses/{$course->id}/lessons/{$lesson->id}")
        ->assertForbidden();
});

it('does not allow lesson access through a mismatched course route', function (): void {
    $user = User::factory()->student()->create();
    Sanctum::actingAs($user);

    $course = Course::factory()->published()->create();
    $otherCourse = Course::factory()->published()->create();
    $otherSection = CourseSection::factory()->for($otherCourse)->create();
    $lesson = Lesson::factory()->for($otherSection, 'section')->published()->create();
    CourseAccess::factory()->create([
        'user_id' => $user->id,
        'course_id' => $course->id,
    ]);

    $this->getJson("/api/my/courses/{$course->id}/lessons/{$lesson->id}")
        ->assertNotFound();
});
