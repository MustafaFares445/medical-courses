# Research: Dashboard Backend API Endpoints

## Decisions

### Keep Dashboard API-Only

The backend remains a REST API-only Laravel monolith. No Filament, Blade, Livewire, or Inertia dashboard files are introduced.

### Reuse Existing Website Backend Foundation

The website endpoint plan is already implemented in the repo. Dashboard work reuses the existing models, migrations, factories, Sanctum authentication, admin middleware, API response wrapper, and testing setup.

### Admin Authorization

Use the existing simple admin check through `user_type = admin`. Do not add Spatie Permission or dynamic role management in V1.

### Overview First

The first implementation slice is `/api/admin/overview` because it is independently testable and validates the protected admin route group before CRUD endpoints are added.

## Rejected Alternatives

- Building Filament resources: rejected because dashboard is a separate frontend application.
- Adding analytics/reporting dashboards: rejected as outside MVP/V1.
- Adding refunds/coupons/manual order mutation: rejected as deferred/not specified.
- Adding preview lessons/progress/access duration: rejected as V2 exclusions.
