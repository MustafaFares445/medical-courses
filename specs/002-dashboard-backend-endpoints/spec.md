# Feature Specification: Dashboard Backend API Endpoints

**Feature Branch**: `dashboard-backend-endpoints`  
**Created**: 2026-06-15  
**Status**: Draft  
**Input**: `Dashboard_Backend_API_Implementation_plan`

## User Stories

- US-ADM-01: Admin can see dashboard overview metrics.
- US-ADM-02: Admin can list, create, update, and delete categories.
- US-ADM-03: Admin can list, create, update, publish, hide, and delete courses.
- US-ADM-04: Admin can manage course sections.
- US-ADM-05: Admin can manage lessons inside sections.
- US-ADM-06: Admin can list, create, update, publish, hide, and delete books.
- US-ADM-07: Admin can list, create, update, publish, hide, and delete articles.
- US-ADM-08: Admin can list and view users.
- US-ADM-09: Admin can list and view orders.
- US-ADM-10: Admin can list and view payments.
- US-ADM-11: Non-admin users are denied from every dashboard endpoint.

## Functional Requirements

### Authorization

- All routes live under `/api/admin/*`.
- All routes require `auth:sanctum` and `admin` middleware.
- V1 admin access uses `user_type = admin` through the existing `User::isAdmin()` path.
- Guests receive `401`; authenticated students receive `403`.

### Overview

- `GET /api/admin/overview` returns aggregate counts and recent order/payment summaries.
- Revenue is calculated from paid orders only.
- The endpoint must not expose card data, Stripe secrets, or raw payment payloads.

### Resource Management

- Category, course, section, lesson, book, and article writes use FormRequest validation.
- Index endpoints support `perPage`, `search`, `filter[...]`, and `sort`.
- API resources return camelCase keys.
- Write logic belongs in services where more than a trivial model write is required.
- Publish transitions set `published_at` the first time status becomes `published`.
- Course/book deletion with existing purchases is soft-deleted or rejected with `409`.

## Explicit V1 Exclusions

Do not include the following in contracts, database changes, tasks, tests, or implementation:

- Lesson preview functionality.
- Access duration management.
- Saved articles/bookmarks.
- Email sharing workflows.
- Course completion percentage/progress tracking.
- Coupons, refunds, certificates, quizzes, comments, notifications, analytics dashboards, and multi-tenant features.

## Acceptance Criteria

1. Admin can call `/api/admin/overview` and receive counts, revenue, recent orders, and recent payments.
2. Guest and student users cannot call dashboard endpoints.
3. Dashboard endpoints do not affect existing public website endpoints.
4. Dashboard JSON uses the existing API response wrapper and camelCase payload names.
5. Pest coverage exists for authorization and each endpoint group before that group is considered complete.
