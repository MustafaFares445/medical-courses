<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('manages course sections', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $course = Course::factory()->create();

    $this->postJson("/api/admin/courses/{$course->id}/sections", [
        'title' => 'Module 1',
        'description' => 'Foundations',
        'sortOrder' => 10,
    ])->assertCreated()
        ->assertJsonPath('data.title', 'Module 1')
        ->assertJsonPath('data.sortOrder', 10);

    $section = CourseSection::query()->where('title', 'Module 1')->firstOrFail();

    $this->patchJson("/api/admin/course-sections/{$section->id}", [
        'title' => 'Updated Module',
        'sortOrder' => 20,
    ])->assertOk()
        ->assertJsonPath('data.title', 'Updated Module')
        ->assertJsonPath('data.sortOrder', 20);

    $this->getJson("/api/admin/courses/{$course->id}/sections?search=Updated")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Updated Module');
});

it('prevents deleting sections with lessons', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $section = CourseSection::factory()->create();
    Lesson::factory()->for($section, 'section')->create();

    $this->deleteJson("/api/admin/course-sections/{$section->id}")->assertStatus(409);

    $this->assertDatabaseHas('course_sections', ['id' => $section->id]);
});

it('deletes empty sections', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $section = CourseSection::factory()->create();

    $this->deleteJson("/api/admin/course-sections/{$section->id}")->assertNoContent();

    $this->assertDatabaseMissing('course_sections', ['id' => $section->id]);
});

it('manages lessons without preview fields', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $section = CourseSection::factory()->create();

    $this->postJson("/api/admin/course-sections/{$section->id}/lessons", [
        'title' => 'Initial Assessment',
        'summary' => 'Short lesson summary',
        'content' => 'Protected lesson content',
        'videoUrl' => 'https://video.example.com/private-link',
        'sortOrder' => 5,
        'status' => 'draft',
    ])->assertCreated()
        ->assertJsonPath('data.title', 'Initial Assessment')
        ->assertJsonPath('data.slug', 'initial-assessment')
        ->assertJsonPath('data.status', 'draft')
        ->assertJsonMissing(['isPreview' => true]);

    $lesson = Lesson::query()->where('slug', 'initial-assessment')->firstOrFail();

    $this->patchJson("/api/admin/lessons/{$lesson->id}", [
        'title' => 'Updated Assessment',
        'status' => 'published',
        'sortOrder' => 7,
    ])->assertOk()
        ->assertJsonPath('data.title', 'Updated Assessment')
        ->assertJsonPath('data.status', 'published')
        ->assertJsonPath('data.sortOrder', 7);

    $this->getJson("/api/admin/course-sections/{$section->id}/lessons?filter[status]=published")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Updated Assessment');

    $this->deleteJson("/api/admin/lessons/{$lesson->id}")->assertNoContent();

    $this->assertDatabaseMissing('lessons', ['id' => $lesson->id]);
});

it('validates lesson payloads', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    $section = CourseSection::factory()->create();

    $this->postJson("/api/admin/course-sections/{$section->id}/lessons", [
        'title' => '',
        'videoUrl' => 'not-a-url',
        'status' => 'preview',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'videoUrl', 'status']);
});
