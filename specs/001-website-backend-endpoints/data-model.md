# Data Model: Website Backend Endpoints

## Core Entities

- User
- Category
- Course
- Course section
- Lesson
- Book
- Article
- Order
- Order item
- Payment
- Course access
- Book access
- Media

## Phase 1 Implemented Data

### User Type

The `users` table has a `user_type` field to separate `admin` and `student` accounts.

Default value: `student`.

## Main Relationships Planned for Phase 2

- A user has orders and purchased content access records.
- A category can group courses, books, or articles.
- A course has sections, and each section has lessons.
- An order has order items and payment records.
- Access records grant purchased course or book access.
- Content models can have media attachments.

## Constraints Planned for Phase 2

- Unique slugs for public content.
- Unique category slug per category type.
- Unique order numbers.
- Unique payment provider event records for webhook idempotency.
- Unique access record per user and purchased item.
