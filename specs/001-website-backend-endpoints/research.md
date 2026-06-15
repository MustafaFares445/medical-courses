# Research: Website Backend Endpoints

## Decisions

### Laravel API Monolith

Decision: Use a single Laravel API monolith.

Reason: Courses, books, articles, payments, and access control share one domain and database. A monolith keeps development, testing, deployment, and AI-assisted implementation simpler.

### Authentication

Decision: Use Laravel Sanctum.

Reason: Website and dashboard are first-party frontends. Sanctum supports SPA cookie auth for same top-level domains and token auth if deployment requires it.

### Dashboard Authorization

Decision: Use `user_type = admin` and `auth:sanctum` + `admin` middleware.

Reason: V1 does not need dynamic permissions or role management.

### Payments

Decision: Use Stripe Checkout plus verified webhooks.

Reason: Stripe Checkout reduces PCI scope, and webhooks provide reliable source-of-truth payment confirmation.

### Media

Decision: Use Spatie Media Library and private disks for protected files.

Reason: It standardizes file attachment collections and supports public/private storage strategies.

### Spec Kit

Decision: Use GitHub Spec Kit / Specify CLI as a process tool, not a Laravel runtime dependency.

Reason: Spec Kit is installed as a CLI with `uv tool install specify-cli --from git+https://github.com/github/spec-kit.git@<tag>` and generates development artifacts and agent commands rather than PHP application code.
