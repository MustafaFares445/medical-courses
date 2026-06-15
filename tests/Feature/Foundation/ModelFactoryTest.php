<?php

declare(strict_types=1);

use App\Models\Article;
use App\Models\Book;
use App\Models\BookAccess;
use App\Models\Category;
use App\Models\Course;
use App\Models\CourseAccess;
use App\Models\CourseSection;
use App\Models\Lesson;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;

it('creates content models with relationships', function (): void {
    $courseCategory = Category::factory()->course()->create();
    $bookCategory = Category::factory()->book()->create();
    $articleCategory = Category::factory()->article()->create();

    $course = Course::factory()->published()->for($courseCategory)->create();
    $section = CourseSection::factory()->for($course)->create();
    $lesson = Lesson::factory()->published()->for($section, 'section')->create();
    $book = Book::factory()->published()->for($bookCategory)->create();
    $article = Article::factory()->published()->for($articleCategory)->create();

    expect($course->category->is($courseCategory))->toBeTrue()
        ->and($section->course->is($course))->toBeTrue()
        ->and($lesson->section->is($section))->toBeTrue()
        ->and($book->category->is($bookCategory))->toBeTrue()
        ->and($article->category->is($articleCategory))->toBeTrue();
});

it('creates order payment and access records', function (): void {
    $user = User::factory()->student()->create();
    $course = Course::factory()->published()->create();
    $book = Book::factory()->published()->create();
    $order = Order::factory()->paid()->for($user)->create();

    $courseItem = OrderItem::factory()->for($order)->course($course)->create();
    $bookItem = OrderItem::factory()->for($order)->book($book)->create();
    $payment = Payment::factory()->paid()->for($order)->create();
    $courseAccess = CourseAccess::factory()->for($user)->for($course)->for($courseItem, 'orderItem')->create();
    $bookAccess = BookAccess::factory()->for($user)->for($book)->for($bookItem, 'orderItem')->create();

    expect($order->user->is($user))->toBeTrue()
        ->and($order->items()->count())->toBe(2)
        ->and($payment->order->is($order))->toBeTrue()
        ->and($courseAccess->course->is($course))->toBeTrue()
        ->and($bookAccess->book->is($book))->toBeTrue();
});
