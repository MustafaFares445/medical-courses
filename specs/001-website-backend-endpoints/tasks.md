# Tasks: Website Backend Endpoints

## Phase 1: Foundation

- [x] T001 Add required Composer packages.
- [x] T002 Add API route loading in bootstrap/app.php.
- [x] T003 Add initial routes/api.php with health endpoints.
- [x] T004 Add Sanctum configuration.
- [x] T005 Add CORS configuration.
- [x] T006 Add simple admin middleware using user_type.
- [x] T007 Add API response helper.
- [x] T008 Add user_type support to User model, factory, and migration.
- [x] T009 Add Pest bootstrap and foundation tests.
- [x] T010 Initialize Spec Kit workspace and project constitution.

## Phase 2: Database and Core Models

- [x] T011 Create content migrations for categories, courses, course sections, lessons, books, and articles.
- [x] T012 Create commerce migrations for orders, order items, payments, course access, and book access.
- [x] T013 Add Spatie Media Library migration/config publishing output.
- [x] T014 Add models and relationships.
- [x] T015 Add model scopes for published/search/filter/status.
- [x] T016 Add factories for all main entities.
- [x] T017 Add admin user seeder and optional category seeder.
- [x] T018 Add migration/model/factory tests.

## Phase 3: Authentication

- [x] T019 Add auth requests, DTOs, services, resources, and controllers.
- [x] T020 Add register/login/logout/password reset/profile routes.
- [x] T021 Add authentication feature tests.

## Phase 4: Public Website Content APIs

- [x] T022 Add home endpoint.
- [x] T023 Add categories endpoint.
- [x] T024 Add courses list/detail endpoints.
- [x] T025 Add books list/detail endpoints.
- [x] T026 Add articles list/detail endpoints.
- [x] T027 Add public visibility and resource leak tests.

## Phase 5: Library and Protected Access

- [x] T028 Add library endpoint.
- [x] T029 Add protected lesson endpoint.
- [x] T030 Add protected book access endpoint.
- [x] T031 Add protected access tests.

## Phase 6: User Orders

- [x] T032 Add user order list/detail endpoints.
- [x] T033 Add user order isolation tests.

## Phase 7: Checkout and Stripe Webhook

- [x] T034 Add checkout request, DTOs, services, and controller.
- [x] T035 Add Stripe checkout session integration.
- [x] T036 Add Stripe webhook verification and processing.
- [x] T037 Add access granting service.
- [x] T038 Add checkout and webhook tests.
