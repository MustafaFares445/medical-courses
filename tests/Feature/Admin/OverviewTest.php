<?php

declare(strict_types=1);

use App\Models\Article;
use App\Models\Book;
use App\Models\Course;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('denies guests from dashboard overview', function (): void {
    $this->getJson('/api/admin/overview')->assertUnauthorized();
});

it('denies students from dashboard overview', function (): void {
    Sanctum::actingAs(User::factory()->student()->create());

    $this->getJson('/api/admin/overview')->assertForbidden();
});

it('returns dashboard overview metrics for admins', function (): void {
    Sanctum::actingAs(User::factory()->admin()->create());

    Course::factory()->count(2)->published()->create();
    Course::factory()->draft()->create();
    Book::factory()->published()->create();
    Article::factory()->published()->create();

    $student = User::factory()->student()->create();
    $order = Order::factory()->for($student)->paid()->create(['total' => 75.50]);
    Payment::factory()->for($order)->create(['status' => 'paid', 'amount' => 75.50]);

    $this->getJson('/api/admin/overview')
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'totalUsers',
                'totalCourses',
                'totalPublishedCourses',
                'totalBooks',
                'totalPublishedBooks',
                'totalArticles',
                'totalPublishedArticles',
                'totalPaidOrders',
                'totalRevenue',
                'recentOrders',
                'recentPayments',
            ],
        ])
        ->assertJsonPath('data.totalPublishedCourses', 2)
        ->assertJsonPath('data.totalPublishedBooks', 1)
        ->assertJsonPath('data.totalPublishedArticles', 1)
        ->assertJsonPath('data.totalPaidOrders', 1);
});
