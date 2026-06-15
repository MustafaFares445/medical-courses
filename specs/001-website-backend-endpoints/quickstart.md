# Quickstart: Website Backend Endpoints

## Install PHP Dependencies

```bash
composer update
```

## Install Spec Kit Specify CLI

```bash
composer speckit:install
```

Optional pinned install:

```bash
bash scripts/install-spec-kit.sh vX.Y.Z
```

## Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Set local frontend origins in `.env`:

```env
FRONTEND_URL=http://localhost:3000
DASHBOARD_URL=http://localhost:5173
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,localhost:5173,127.0.0.1,127.0.0.1:8000
CORS_ALLOWED_ORIGINS=http://localhost:3000,http://localhost:5173
```

## Run Migrations

```bash
php artisan migrate
```

## Run Tests

```bash
composer test
```

## Verify API Foundation

```bash
php artisan serve
```

Open `/api/health` on the local Laravel server. The response should include `data.status = ok`.
