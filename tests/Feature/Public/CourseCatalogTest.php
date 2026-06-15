<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\Lesson;

it('lists published courses and excludes hidden courses', function (): void {
    Course::factory()->published()->create([
        'title' => 'Clinical Cardiology Basics',
        'slug' => 'clinical-cardiology-basics',
    ]);
    Course::factory()->hidden()->create([
        'title' => 'Hidden Course',
        'slug' => 'hidden-course',
    ]);

    $this->getJson('/api/courses?search=cardiology')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.slug', 'clinical-cardiology-basics');
});

it('shows published course details without protected lesson fields', function (): void {
    $course = Course::factory()->published()->create(['slug' => 'emergency-medicine']);
    $section = CourseSection::factory()->for($course)->create(['sort_order' => 1]);
    Lesson::factory()->published()->for($section, 'section')->create([
        'title' => 'Initial Assessment',
        'content' => 'Protected lesson notes',
        'video_url' => 'https://video.example.test/private',
    ]);
    Lesson::factory()->hidden()->for($section, 'section')->create(['title' => 'Hidden Lesson']);

    $this->getJson('/api/courses/emergency-medicine')
        ->assertOk()
        ->assertJsonPath('data.sections.0.lessons.0.title', 'Initial Assessment')
        ->assertJsonCount(1, 'data.sections.0.lessons')
        ->assertJsonMissing(['content' => 'Protected lesson notes'])
        ->assertJsonMissing(['videoUrl' => 'https://video.example.test/private']);
});

it('returns not found for hidden course details', function (): void {
    Course::factory()->hidden()->create(['slug' => 'hidden-course']);

    $this->getJson('/api/courses/hidden-course')->assertNotFound();
});
