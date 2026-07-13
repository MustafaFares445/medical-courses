<?php

declare(strict_types=1);

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class DemoContentSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $emergency = $this->category('course', 'emergency-medicine');
            $cardiology = $this->category('course', 'cardiology');
            $internal = $this->category('course', 'internal-medicine');
            $clinicalGuides = $this->category('book', 'clinical-guides');
            $anatomy = $this->category('book', 'anatomy');
            $studyTips = $this->category('article', 'study-tips');
            $medicalEducation = $this->category('article', 'medical-education');

            $emergencyCourse = $this->course([
                'category_id' => $emergency->id,
                'title' => ['en' => 'Emergency Medicine Basics', 'ar' => 'أساسيات طب الطوارئ'],
                'slug' => 'emergency-medicine-basics',
                'short_description' => ['en' => 'Learn rapid assessment, triage, and first-line emergency care.', 'ar' => 'تعلم التقييم السريع والفرز والرعاية الأولية في الطوارئ.'],
                'description' => ['en' => 'A practical course for medical students and junior clinicians covering emergency assessment, stabilization, and common urgent presentations.', 'ar' => 'كورس عملي لطلاب الطب والأطباء الجدد يغطي تقييم الطوارئ وتثبيت الحالة والحالات العاجلة الشائعة.'],
                'price' => '49.00',
                'currency' => 'USD',
                'status' => 'published',
                'published_at' => now()->subDays(10),
            ]);

            $this->sectionWithLessons($emergencyCourse, [
                'title' => ['en' => 'Initial Assessment', 'ar' => 'التقييم الأولي'],
                'description' => ['en' => 'Primary survey and first decisions.', 'ar' => 'المسح الأولي واتخاذ القرارات الأولى.'],
                'sort_order' => 1,
                'lessons' => [
                    ['slug' => 'primary-survey', 'title' => ['en' => 'Primary Survey', 'ar' => 'المسح الأولي'], 'summary' => ['en' => 'Airway, breathing, circulation, disability, and exposure.', 'ar' => 'مجرى الهواء والتنفس والدورة الدموية والوعي والتعرض.'], 'content' => ['en' => 'Use a structured ABCDE approach and treat life-threatening problems as they are found.', 'ar' => 'استخدم منهج ABCDE المنظم وعالج المشكلات المهددة للحياة فور اكتشافها.'], 'sort_order' => 1],
                    ['slug' => 'triage-priorities', 'title' => ['en' => 'Triage Priorities', 'ar' => 'أولويات الفرز'], 'summary' => ['en' => 'Identify who needs immediate care.', 'ar' => 'تحديد من يحتاج إلى رعاية فورية.'], 'content' => ['en' => 'Triage prioritizes unstable patients, high-risk complaints, and time-sensitive interventions.', 'ar' => 'يرتب الفرز أولوية المرضى غير المستقرين والشكاوى عالية الخطورة والتدخلات الحساسة للوقت.'], 'sort_order' => 2],
                ],
            ]);

            $this->sectionWithLessons($emergencyCourse, [
                'title' => ['en' => 'Common Emergencies', 'ar' => 'الطوارئ الشائعة'],
                'description' => ['en' => 'High-yield emergency presentations.', 'ar' => 'حالات طوارئ مهمة ومتكررة.'],
                'sort_order' => 2,
                'lessons' => [
                    ['slug' => 'shock-approach', 'title' => ['en' => 'Approach to Shock', 'ar' => 'منهج التعامل مع الصدمة'], 'summary' => ['en' => 'Recognize shock patterns and start stabilization.', 'ar' => 'تمييز أنماط الصدمة وبدء تثبيت الحالة.'], 'content' => ['en' => 'Assess perfusion, identify likely shock type, start oxygen, access, fluids or targeted therapy.', 'ar' => 'قيم التروية وحدد نوع الصدمة المحتمل وابدأ الأكسجين والوصول الوريدي والسوائل أو العلاج الموجه.'], 'sort_order' => 1],
                ],
            ]);

            $ecgCourse = $this->course([
                'category_id' => $cardiology->id,
                'title' => ['en' => 'ECG Interpretation for Beginners', 'ar' => 'قراءة تخطيط القلب للمبتدئين'],
                'slug' => 'ecg-interpretation-for-beginners',
                'short_description' => ['en' => 'Build a safe step-by-step ECG reading method.', 'ar' => 'ابن طريقة آمنة ومنظمة لقراءة تخطيط القلب.'],
                'description' => ['en' => 'Learn rhythm, rate, axis, intervals, ischemia patterns, and common ECG diagnoses with clinical examples.', 'ar' => 'تعلم النظم والسرعة والمحور والفواصل وأنماط نقص التروية وتشخيصات التخطيط الشائعة بأمثلة سريرية.'],
                'price' => '39.00',
                'currency' => 'USD',
                'status' => 'published',
                'published_at' => now()->subDays(8),
            ]);

            $this->sectionWithLessons($ecgCourse, [
                'title' => ['en' => 'Reading Method', 'ar' => 'طريقة القراءة'],
                'description' => ['en' => 'A repeatable ECG checklist.', 'ar' => 'قائمة تحقق متكررة لتخطيط القلب.'],
                'sort_order' => 1,
                'lessons' => [
                    ['slug' => 'rate-and-rhythm', 'title' => ['en' => 'Rate and Rhythm', 'ar' => 'السرعة والنظم'], 'summary' => ['en' => 'Estimate rate and classify rhythms.', 'ar' => 'تقدير السرعة وتصنيف النظم.'], 'content' => ['en' => 'Start with calibration, rate, rhythm regularity, P waves, PR interval, and QRS width.', 'ar' => 'ابدأ بالمعايرة والسرعة وانتظام النظم وموجات P وفاصل PR وعرض QRS.'], 'sort_order' => 1],
                ],
            ]);

            $this->course([
                'category_id' => $internal->id,
                'title' => ['en' => 'Internal Medicine Case Reviews', 'ar' => 'مراجعة حالات الطب الباطني'],
                'slug' => 'internal-medicine-case-reviews',
                'short_description' => ['en' => 'Practice clinical reasoning through common inpatient cases.', 'ar' => 'تدرب على التفكير السريري من خلال حالات تنويم شائعة.'],
                'description' => ['en' => 'Case-based lessons covering differential diagnosis, investigation planning, and treatment priorities.', 'ar' => 'دروس مبنية على الحالات تغطي التشخيص التفريقي وخطة الاستقصاءات وأولويات العلاج.'],
                'price' => '59.00',
                'currency' => 'USD',
                'status' => 'published',
                'published_at' => now()->subDays(6),
            ]);

            $this->course([
                'category_id' => $internal->id,
                'title' => ['en' => 'Clinical Pharmacology Draft Course', 'ar' => 'مسودة كورس علم الأدوية السريري'],
                'slug' => 'clinical-pharmacology-draft-course',
                'short_description' => ['en' => 'Draft course hidden from public APIs.', 'ar' => 'كورس مسودة لا يظهر في واجهات الموقع العامة.'],
                'description' => ['en' => 'Seeded draft content for dashboard and visibility testing.', 'ar' => 'محتوى تجريبي بحالة مسودة لاختبار الداشبورد والظهور.'],
                'price' => '44.00',
                'currency' => 'USD',
                'status' => 'draft',
                'published_at' => null,
            ]);

            $clinicalBook = $this->book([
                'category_id' => $clinicalGuides->id,
                'title' => ['en' => 'Clinical Handbook for Medical Students', 'ar' => 'الدليل السريري لطلاب الطب'],
                'slug' => 'clinical-handbook-for-medical-students',
                'short_description' => ['en' => 'A concise guide for wards, clinics, and exam revision.', 'ar' => 'دليل مختصر للأقسام والعيادات ومراجعة الامتحانات.'],
                'description' => ['en' => 'Practical summaries, checklists, and clinical approaches for everyday medical learning.', 'ar' => 'ملخصات عملية وقوائم تحقق ومناهج سريرية للتعلم الطبي اليومي.'],
                'price' => '29.00',
                'currency' => 'USD',
                'status' => 'published',
                'published_at' => now()->subDays(9),
            ]);

            $this->book([
                'category_id' => $anatomy->id,
                'title' => ['en' => 'Applied Anatomy Notes', 'ar' => 'ملاحظات التشريح التطبيقي'],
                'slug' => 'applied-anatomy-notes',
                'short_description' => ['en' => 'High-yield anatomy notes with clinical correlations.', 'ar' => 'ملاحظات تشريح مركزة مع روابط سريرية.'],
                'description' => ['en' => 'A focused anatomy resource connecting structures to examination and clinical practice.', 'ar' => 'مرجع تشريح مركز يربط التراكيب بالفحص والممارسة السريرية.'],
                'price' => '24.00',
                'currency' => 'USD',
                'status' => 'published',
                'published_at' => now()->subDays(7),
            ]);

            $this->book([
                'category_id' => $clinicalGuides->id,
                'title' => ['en' => 'Hidden Procedure Checklist Book', 'ar' => 'كتاب قوائم الإجراءات المخفي'],
                'slug' => 'hidden-procedure-checklist-book',
                'short_description' => ['en' => 'Hidden demo book for dashboard state testing.', 'ar' => 'كتاب مخفي تجريبي لاختبار حالات الداشبورد.'],
                'description' => ['en' => 'This seeded record should not appear in public book catalogs.', 'ar' => 'هذا السجل التجريبي لا يجب أن يظهر في قوائم الكتب العامة.'],
                'price' => '19.00',
                'currency' => 'USD',
                'status' => 'hidden',
                'published_at' => null,
            ]);

            $this->article([
                'category_id' => $studyTips->id,
                'title' => ['en' => 'How to Study Medicine Without Burning Out', 'ar' => 'كيف تدرس الطب دون إرهاق'],
                'slug' => 'how-to-study-medicine-without-burning-out',
                'excerpt' => ['en' => 'Simple habits for sustainable medical learning.', 'ar' => 'عادات بسيطة لتعلم طبي مستدام.'],
                'body' => ['en' => 'Use active recall, spaced repetition, focused clinical questions, and realistic weekly planning. Keep revision short and frequent instead of relying on long last-minute sessions.', 'ar' => 'استخدم الاسترجاع النشط والتكرار المتباعد والأسئلة السريرية المركزة والتخطيط الأسبوعي الواقعي. اجعل المراجعة قصيرة ومتكررة بدلا من الاعتماد على جلسات طويلة في آخر لحظة.'],
                'status' => 'published',
                'published_at' => now()->subDays(5),
            ]);

            $this->article([
                'category_id' => $medicalEducation->id,
                'title' => ['en' => 'Building a Clinical Reasoning Framework', 'ar' => 'بناء إطار للتفكير السريري'],
                'slug' => 'building-a-clinical-reasoning-framework',
                'excerpt' => ['en' => 'A practical way to move from symptoms to differential diagnosis.', 'ar' => 'طريقة عملية للانتقال من الأعراض إلى التشخيص التفريقي.'],
                'body' => ['en' => 'Start with the main complaint, identify dangerous diagnoses first, group causes by system, and use history and examination to narrow the list.', 'ar' => 'ابدأ بالشكوى الرئيسية وحدد التشخيصات الخطيرة أولا ثم صنف الأسباب حسب الجهاز واستخدم القصة والفحص لتضييق القائمة.'],
                'status' => 'published',
                'published_at' => now()->subDays(4),
            ]);

            $this->article([
                'category_id' => $studyTips->id,
                'title' => ['en' => 'Draft Article for Admin Review', 'ar' => 'مقال مسودة لمراجعة المدير'],
                'slug' => 'draft-article-for-admin-review',
                'excerpt' => ['en' => 'Draft article excluded from public API.', 'ar' => 'مقال مسودة مستبعد من الواجهة العامة.'],
                'body' => ['en' => 'This is seeded as draft content for dashboard testing.', 'ar' => 'هذا محتوى تجريبي بحالة مسودة لاختبار الداشبورد.'],
                'status' => 'draft',
                'published_at' => null,
            ]);

            $student = User::query()->updateOrCreate(
                ['email' => 'student@example.com'],
                ['name' => 'Demo Student', 'password' => Hash::make('password'), 'user_type' => 'student'],
            );

            $order = Order::query()->updateOrCreate(
                ['order_number' => 'DEMO-PAID-0001'],
                ['user_id' => $student->id, 'status' => 'paid', 'subtotal' => '78.00', 'total' => '78.00', 'currency' => 'USD', 'checkout_session_id' => 'cs_test_demo_seed_0001', 'paid_at' => now()->subDays(1)],
            );

            $courseItem = OrderItem::query()->updateOrCreate(
                ['order_id' => $order->id, 'item_type' => 'course', 'item_id' => $emergencyCourse->id],
                ['title_snapshot' => 'Emergency Medicine Basics', 'price_snapshot' => '49.00', 'currency' => 'USD'],
            );

            $bookItem = OrderItem::query()->updateOrCreate(
                ['order_id' => $order->id, 'item_type' => 'book', 'item_id' => $clinicalBook->id],
                ['title_snapshot' => 'Clinical Handbook for Medical Students', 'price_snapshot' => '29.00', 'currency' => 'USD'],
            );

            Payment::query()->updateOrCreate(
                ['provider_event_id' => 'evt_demo_seed_0001'],
                ['order_id' => $order->id, 'provider' => 'stripe', 'provider_payment_id' => 'pi_demo_seed_0001', 'provider_session_id' => 'cs_test_demo_seed_0001', 'status' => 'paid', 'amount' => '78.00', 'currency' => 'USD', 'raw_payload' => ['seeded' => true, 'type' => 'checkout.session.completed'], 'processed_at' => now()->subDays(1)],
            );

            CourseAccess::query()->updateOrCreate(
                ['user_id' => $student->id, 'course_id' => $emergencyCourse->id],
                ['order_item_id' => $courseItem->id, 'purchased_at' => now()->subDays(1)],
            );

            BookAccess::query()->updateOrCreate(
                ['user_id' => $student->id, 'book_id' => $clinicalBook->id],
                ['order_item_id' => $bookItem->id, 'purchased_at' => now()->subDays(1)],
            );

            Order::query()->updateOrCreate(
                ['order_number' => 'DEMO-PENDING-0002'],
                ['user_id' => $student->id, 'status' => 'pending', 'subtotal' => '39.00', 'total' => '39.00', 'currency' => 'USD', 'checkout_session_id' => 'cs_test_demo_seed_0002', 'paid_at' => null],
            );
        });
    }

    private function category(string $type, string $slug): Category
    {
        return Category::query()->where('type', $type)->where('slug', $slug)->firstOrFail();
    }

    private function course(array $data): Course
    {
        return Course::query()->updateOrCreate(['slug' => $data['slug']], $data);
    }

    private function sectionWithLessons(Course $course, array $data): void
    {
        $section = CourseSection::query()->updateOrCreate(
            ['course_id' => $course->id, 'sort_order' => $data['sort_order']],
            ['title' => $data['title'], 'description' => $data['description']],
        );

        foreach ($data['lessons'] as $lesson) {
            Lesson::query()->updateOrCreate(
                ['course_section_id' => $section->id, 'slug' => $lesson['slug']],
                ['title' => $lesson['title'], 'summary' => $lesson['summary'], 'content' => $lesson['content'], 'video_url' => 'https://video.example.com/demo/'.$lesson['slug'], 'sort_order' => $lesson['sort_order'], 'status' => 'published'],
            );
        }
    }

    private function book(array $data): Book
    {
        return Book::query()->updateOrCreate(['slug' => $data['slug']], $data);
    }

    private function article(array $data): Article
    {
        return Article::query()->updateOrCreate(['slug' => $data['slug']], $data);
    }
}
