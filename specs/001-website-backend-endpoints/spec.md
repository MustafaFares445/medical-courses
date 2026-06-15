# Feature Specification: Website Backend Endpoints

**Feature Branch**: `001-website-backend-endpoints`  
**Status**: Draft  
**Scope**: Laravel API endpoints consumed by the public website and authenticated student account area.

## User Stories

- US-WEB-01: Visitor can view home page summaries.
- US-WEB-02: Visitor can browse published courses.
- US-WEB-03: Visitor can view published course details.
- US-WEB-04: Visitor can browse published books.
- US-WEB-05: Visitor can view published book details.
- US-WEB-06: Visitor can browse and read published articles.
- US-WEB-07: User can register, login, logout, reset password, and view profile.
- US-WEB-08: User can start checkout for course/book items.
- US-WEB-09: Stripe webhook confirms payment and grants access.
- US-WEB-10: User can view purchased courses/books in library.
- US-WEB-11: User can access purchased lesson content.
- US-WEB-12: User can access purchased book files securely.
- US-WEB-13: User can view own order history.

## Functional Requirements

### Public Content

- Public catalog APIs return only `published` courses, books, and articles.
- Public course detail returns course metadata, public section data, and lesson metadata only.
- Public book detail never returns private file URLs, signed URLs, or internal storage paths.
- Public article detail returns body only when article is published.

### Authentication

- Users can register as `student` only.
- Admin users are seeded or created outside public registration.
- Authenticated APIs use Sanctum.
- Dashboard/admin access is controlled by `user_type = admin`.

### Checkout and Access

- Checkout accepts an `items` array containing courses and books.
- Checkout prices are loaded from the database, never from frontend payload.
- Orders are created as `pending` before Stripe Checkout redirect.
- Stripe webhooks are the only source of truth for paid status and access granting.
- Webhook processing must be idempotent.
- Course/book access records are created only after verified successful payment.

### User Library and Orders

- Users can see only their own library and orders.
- Library is based on course/book access tables.
- Order history must not expose raw Stripe payloads to students.

## Non-Functional Requirements

- Use clean Laravel monolith structure.
- New PHP classes use `declare(strict_types=1)`.
- Controllers stay thin.
- Services own business logic.
- FormRequest for writes.
- FilterRequest for indexes.
- API Resources return camelCase keys.
- Pest tests cover core flows.
- Protected files use private storage and signed temporary URLs.

## Out of Scope for V1

- Filament or backend dashboard UI.
- Dynamic permissions.
- Lesson preview functionality.
- Access duration management.
- Saved articles.
- Email sharing workflows.
- Course progress/completion percentage.
- Coupons, refunds, subscriptions, certificates, quizzes, comments, reviews, notifications, or advanced analytics.
