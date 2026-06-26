<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class CategorySeeder extends Seeder
{
    public function run(): void
    {
        collect([
            [
                'type' => 'course',
                'name' => ['en' => 'Emergency Medicine', 'ar' => 'طب الطوارئ'],
                'description' => ['en' => 'Courses for emergency assessment and urgent care skills.', 'ar' => 'كورسات لتقييم الحالات الطارئة ومهارات الرعاية العاجلة.'],
            ],
            [
                'type' => 'course',
                'name' => ['en' => 'Cardiology', 'ar' => 'أمراض القلب'],
                'description' => ['en' => 'Courses about ECG, cardiac assessment, and common heart conditions.', 'ar' => 'كورسات عن تخطيط القلب وتقييم القلب والحالات القلبية الشائعة.'],
            ],
            [
                'type' => 'course',
                'name' => ['en' => 'Internal Medicine', 'ar' => 'الطب الباطني'],
                'description' => ['en' => 'Clinical reasoning and core internal medicine topics.', 'ar' => 'التفكير السريري وموضوعات الطب الباطني الأساسية.'],
            ],
            [
                'type' => 'book',
                'name' => ['en' => 'Clinical Guides', 'ar' => 'أدلة سريرية'],
                'description' => ['en' => 'Practical clinical handbooks and quick references.', 'ar' => 'كتيبات سريرية عملية ومراجع سريعة.'],
            ],
            [
                'type' => 'book',
                'name' => ['en' => 'Anatomy', 'ar' => 'علم التشريح'],
                'description' => ['en' => 'Medical anatomy books for students and clinicians.', 'ar' => 'كتب تشريح طبية للطلاب والممارسين.'],
            ],
            [
                'type' => 'article',
                'name' => ['en' => 'Study Tips', 'ar' => 'نصائح الدراسة'],
                'description' => ['en' => 'Articles that help medical students study more effectively.', 'ar' => 'مقالات تساعد طلاب الطب على الدراسة بفعالية أكبر.'],
            ],
            [
                'type' => 'article',
                'name' => ['en' => 'Medical Education', 'ar' => 'التعليم الطبي'],
                'description' => ['en' => 'Educational articles for medical learning and exam preparation.', 'ar' => 'مقالات تعليمية للتعلم الطبي والتحضير للامتحانات.'],
            ],
        ])->each(function (array $category): void {
            Category::query()->updateOrCreate(
                [
                    'type' => $category['type'],
                    'slug' => Str::slug($category['name']['en']),
                ],
                [
                    'name' => $category['name'],
                    'description' => $category['description'],
                    'is_active' => true,
                ],
            );
        });
    }
}
