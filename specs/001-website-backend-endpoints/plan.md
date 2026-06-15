# Implementation Plan: Website Backend Endpoints

## Architecture

Use a clean Laravel API monolith.

Implementation pattern:

```text
Route -> Controller -> FormRequest/FilterRequest -> Data DTO -> Service -> Model/DB -> API Resource
```

List endpoint pattern:

```text
Route -> Controller -> FilterRequest -> Model Query -> Resource::collection()
```

## Technology

- Laravel 13 API backend.
- PHP 8.3+ now, PHP 8.4 target when hosting supports it.
- MySQL for production.
- Sanctum for first-party frontend authentication.
- Spatie Laravel Data for DTOs.
- Spatie Query Builder or project filter traits for list filtering.
- Spatie Media Library for images/files.
- Stripe PHP SDK for Checkout and webhooks.
- Pest for backend tests.

## Phase 1: Foundation

- Install backend packages.
- Add API routes file.
- Configure Sanctum and CORS.
- Add `admin` middleware.
- Add API response helper.
- Add `user_type` support.
- Add Pest bootstrap and foundation tests.
- Initialize Spec Kit workspace.

## Phase 2: Database and Models

- Add content, order, payment, access, and media migrations.
- Add models, relationships, scopes, factories, and seeders.

## Phase 3: Authentication

- Register/login/logout/password reset/profile endpoints.
- Sanctum authentication tests.

## Phase 4: Public Website Content APIs

- Home endpoint.
- Categories endpoint.
- Courses catalog/detail.
- Books catalog/detail.
- Articles catalog/detail.

## Phase 5: Library and Protected Access

- User library.
- Protected lesson access.
- Protected book access.

## Phase 6: User Orders

- Current user's order list and detail endpoints.

## Phase 7: Checkout and Stripe Webhook

- Checkout session creation.
- Stripe webhook signature verification.
- Idempotent payment processing.
- Access granting.

## Quality Gates

- Focused tests pass after each phase.
- Public endpoints never expose protected media.
- Admin endpoints deny non-admin users.
- Stripe access granting is idempotent.
