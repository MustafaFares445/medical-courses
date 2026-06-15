# Medical Courses Backend Constitution

## Core Principles

1. API-only backend. Do not generate Filament, Blade dashboard, Livewire dashboard, or Inertia dashboard files.
2. Website and dashboard are separate frontend apps.
3. Backend is a clean Laravel API monolith.
4. Controllers must stay thin and delegate business logic to services.
5. Every write endpoint must use a FormRequest.
6. Every index endpoint must use a FilterRequest.
7. Data DTOs must be used where project standards require them.
8. API Resources must return camelCase JSON keys.
9. Database columns remain snake_case.
10. Public APIs must return published content only.
11. Protected course lessons and book files must never be exposed without access records.
12. Stripe webhook is the payment source of truth.
13. Stripe webhook processing must be idempotent.
14. Frontend prices, statuses, protected URLs, and access decisions are never trusted.
15. Multi-table writes must use database transactions.
16. Protected files must use private storage and signed URLs.
17. Dashboard endpoints must live under `/api/admin/*` and require `auth:sanctum` plus admin middleware.
18. Admin access is simple `user_type = admin` for V1.
19. No dynamic permissions system in V1.
20. Pest tests are required for core flows.

## V1 Exclusions

The following features must not appear in V1 API contracts, database schema, frontend contracts, tests, or Spec Kit tasks:

- Lesson preview functionality.
- Access duration management.
- Saved articles or bookmarks.
- Email sharing workflows.
- Course completion percentage or progress tracking.
- Subscriptions, coupons, refunds, certificates, quizzes, comments, reviews, notifications, analytics dashboards, and multi-tenant features.

## Implementation Gates

Each phase must pass these checks before moving to the next phase:

- Composer dependencies installed successfully.
- Laravel routes load successfully.
- Focused Pest tests pass.
- New API responses follow `{ "data": ... }` for success and `{ "message": ... }` for errors.
- No protected media path is returned from public endpoints.
