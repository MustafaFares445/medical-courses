# Tasks: Dashboard Backend API Endpoints

## Phase 1 — Overview Slice

- [x] T001 Create dashboard backend spec-kit folder.
- [x] T002 Add `/api/admin/overview` route behind `auth:sanctum` and `admin` middleware.
- [x] T003 Implement `DashboardOverviewService`.
- [x] T004 Implement `Admin\OverviewController`.
- [x] T005 Add Pest tests for overview authorization and response structure.

## Phase 2 — Shared Admin Infrastructure

- [ ] T006 [P] Create shared admin filter request helpers.
- [ ] T007 [P] Create shared publish transition service/helper.
- [ ] T008 [P] Create shared media replacement service for resource-specific upload endpoints.
- [ ] T009 [P] Create admin API resources for common response shapes.

## Phase 3 — Categories

- [ ] T010 Create `Admin\CategoryController`.
- [ ] T011 Create category FormRequest and FilterRequest.
- [ ] T012 Add category service with create/update/delete conflict handling.
- [ ] T013 Add category admin routes.
- [ ] T014 Add category Pest tests.

## Phase 4 — Courses, Sections, Lessons

- [ ] T015 Create `Admin\CourseController`.
- [ ] T016 Create `Admin\CourseSectionController`.
- [ ] T017 Create `Admin\LessonController`.
- [ ] T018 Add course, section, and lesson requests/resources/services.
- [ ] T019 Add media replacement handling for course thumbnails and lesson videos.
- [ ] T020 Add safe delete behavior for purchased courses.
- [ ] T021 Add course/section/lesson Pest tests.

## Phase 5 — Books and Articles

- [ ] T022 Create `Admin\BookController` and related request/resource/service.
- [ ] T023 Create `Admin\ArticleController` and related request/resource/service.
- [ ] T024 Add media replacement handling for covers, private book files, and article images.
- [ ] T025 Add safe delete behavior for purchased books.
- [ ] T026 Add book/article Pest tests.

## Phase 6 — Users, Orders, Payments

- [ ] T027 Create `Admin\UserController` index/show.
- [ ] T028 Create `Admin\OrderController` index/show.
- [ ] T029 Create `Admin\PaymentController` index/show.
- [ ] T030 Add users/orders/payments filters and resources.
- [ ] T031 Add admin operational view Pest tests.

## Phase 7 — Final Validation

- [ ] T032 Run focused admin test suite.
- [ ] T033 Run full backend test suite.
- [ ] T034 Review route list and OpenAPI contract.
- [ ] T035 Confirm V2 exclusions are absent.
