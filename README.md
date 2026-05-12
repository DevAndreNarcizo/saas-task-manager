# SaaS Task Manager

Full-stack freemium task manager with Next.js, Laravel API, PostgreSQL, Redis and Stripe.

Note: the original plan targeted Next.js 14, but npm audit flagged known vulnerabilities. This scaffold uses the patched major version installed by `npm audit fix --force`.

## Apps

- `apps/web`: Next.js App Router dashboard.
- `apps/api`: Laravel API for auth, RBAC, plan limits, tasks and Stripe webhooks.

## Quick start

```bash
npm install
npm run dev:web
```

API:

```bash
cd apps/api
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve
```

## Product rules

- Free plan: 3 projects and 10 tasks.
- Pro plan: unlimited projects and tasks.
- RBAC roles: admin, member, viewer.
- Stripe webhooks are handled server-side and must be configured with `STRIPE_WEBHOOK_SECRET`.
