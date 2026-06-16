# Implementation Plan: Dashboard Backend API Endpoints

## Technical Context

- Backend: Laravel API-only monolith.
- Auth: existing Sanctum auth and admin middleware.
- Existing implementation: website endpoints are already present and must not be broken.
- Data model: reuse existing content, order, payment, access, and media tables.
- API standard: thin controllers, FormRequest/FilterRequest validation, services for business logic, camelCase API resources, Pest tests.

## Phase 1 — Spec-Kit Bootstrap and Overview Slice

1. Create `specs/002-dashboard-backend-endpoints/*` artifacts.
2. Add protected `/api/admin/overview` route.
3. Implement `DashboardOverviewService`.
4. Implement `Admin\OverviewController`.
5. Add Pest tests for guest/student/admin authorization and overview response structure.

## Phase 2 — Admin Shared Infrastructure

1. Add shared admin filter request base.
2. Add shared admin pagination and sort allow-list helpers.
3. Add admin resource classes for category, course, section, lesson, book, article, user, order, and payment.
4. Add service helpers for publish transitions and safe deletion.

## Phase 3 — Category CRUD

1. Implement `/api/admin/categories` resource routes.
2. Add create/update FormRequest validation.
3. Prevent deleting categories referenced by active content.
4. Add filtering, search, sorting, pagination, and Pest coverage.

## Phase 4 — Course, Section, and Lesson Management

1. Implement course CRUD and media thumbnail handling.
2. Implement course section CRUD.
3. Implement lesson CRUD and protected lesson media handling.
4. Preserve V1 exclusion: no `isPreview` management.
5. Add Pest coverage for authorization, validation, CRUD, filters, publishing, and safe deletion.

## Phase 5 — Book and Article Management

1. Implement book CRUD, cover upload, private book file upload, external file URL support.
2. Enforce publish rule: published book requires private file or protected external URL.
3. Implement article CRUD and image upload.
4. Add Pest coverage for authorization, validation, CRUD, filters, publishing, and protected file non-leakage.

## Phase 6 — Admin Operational Views

1. Implement admin users index/show.
2. Implement admin orders index/show.
3. Implement admin payments index/show.
4. Do not add user deletion, manual order mutation, refunds, or role-management in V1.

## Quality Gates

For each phase:

1. Run focused Pest tests for the phase.
2. Run `php artisan test` before merge.
3. Confirm `php artisan route:list --path=api/admin` shows expected protected routes.
4. Confirm public website endpoint tests still pass.
