# Data Model: Dashboard Backend API Endpoints

Dashboard endpoints reuse the existing tables and models from the website backend implementation.

## Existing Models Used

- `User`
- `Category`
- `Course`
- `CourseSection`
- `Lesson`
- `Book`
- `Article`
- `Order`
- `OrderItem`
- `Payment`
- `CourseAccess`
- `BookAccess`

## Dashboard-Specific Behavior

### Overview

Reads aggregate values from:

- `users`
- `courses`
- `books`
- `articles`
- `orders`
- `payments`

No new tables are required for the overview slice.

### CRUD Phases

Upcoming dashboard CRUD phases must not create alternate dashboard-only content tables. They must update the existing content tables so the public website continues to use the same source of truth.

## V1 Exclusions From Data Model

Do not add columns or tables for:

- Lesson preview management.
- Access duration or expiration.
- Saved articles/bookmarks.
- Email sharing workflow.
- Course progress/completion percentage.
