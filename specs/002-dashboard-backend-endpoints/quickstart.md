# Quickstart: Dashboard Backend API Endpoints

## Local Validation

```bash
composer install
php artisan migrate:fresh --seed
php artisan route:list --path=api/admin
php artisan test tests/Feature/Admin/OverviewTest.php
```

## Manual Smoke Test

1. Log in as an admin user through the existing auth endpoint.
2. Send the token/session to:

```http
GET /api/admin/overview
```

3. Expected response:

```json
{
  "data": {
    "totalUsers": 1,
    "totalCourses": 0,
    "totalPublishedCourses": 0,
    "totalBooks": 0,
    "totalPublishedBooks": 0,
    "totalArticles": 0,
    "totalPublishedArticles": 0,
    "totalPaidOrders": 0,
    "totalRevenue": "0",
    "recentOrders": [],
    "recentPayments": []
  }
}
```

## Authorization Checks

- Guest request to `/api/admin/overview` returns `401`.
- Student request to `/api/admin/overview` returns `403`.
- Admin request returns `200`.

## Next Implementation Slice

Proceed with shared admin filter/request/resource infrastructure, then category CRUD.
