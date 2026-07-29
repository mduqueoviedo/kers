# Architecture

## Status

This document describes the architecture present after the first KERS domain
model iteration. Domain Livewire pages, incidents, response teams, capacity
rules, USGS integration, roles, policies, and the operational dashboard are
not implemented yet.

Update this document when architecture actually changes; do not treat planned
roadmap items as current components.

## System shape

KERS is currently one Laravel application with one PostgreSQL database.

```text
Browser
→ Laravel web routes
→ Blade view or single-file Livewire component
→ Laravel application services and Eloquent
→ PostgreSQL
```

There is no separate frontend application, API service, worker service, or
external integration process.

## Application framework

Laravel 13 provides application bootstrapping, routing, configuration,
middleware, database access, migrations, testing integration, and the
authentication foundation.

The conventional directory responsibilities are:

- `app/` contains application classes, providers, actions, concerns, Livewire
  actions, and Eloquent models.
- `bootstrap/` configures the Laravel application and providers.
- `config/` maps environment variables into runtime configuration.
- `database/` contains migrations, factories, and seeders.
- `resources/` contains CSS, JavaScript, Blade views, layouts, and Livewire
  single-file components.
- `routes/` declares web, settings, and console routes.
- `tests/` contains Pest feature tests.

No custom domain layer, repository abstraction, or generic service layer is
present.

## HTTP and UI

The public home route renders the starter welcome Blade view.

The dashboard renders a Blade view and currently requires the `auth` and
`verified` middleware aliases. Settings routes use authenticated single-file
Livewire components for profile, appearance, security, and account deletion.

The starter UI uses:

- Livewire 4
- Flux components
- Tailwind CSS 4
- Vite 8
- Shared Blade layouts and components

KERS domain navigation and pages have not been added.

## Livewire

Single-file components colocate a Livewire anonymous component class and its
Blade template. Current examples live under `resources/views/pages/settings/`
and demonstrate:

- Public component properties
- `mount()` initialization
- Validation
- Livewire actions
- Computed properties
- Redirects and toast feedback

Domain pages will follow this existing project choice when their roadmap items
are approved.

## Database

PostgreSQL 18 is the project database for local development, automated tests,
CI, and future deployment.

Database selection flows from environment variables through
`config/database.php`. The development database is `kers`; PHPUnit selects the
separate `kers_testing` database.

The current schema contains:

- Users, password-reset tokens, and sessions
- Cache and cache locks
- Jobs, job batches, and failed jobs
- Kaijus with category and threat-level constraints
- Laravel's migration history

The `Kaiju` Eloquent model casts its stored category string to the
`KaijuCategory` PHP enum. PostgreSQL check constraints independently restrict
categories to the enum's current values and threat levels to the range 1–5.
Incident and response-team tables do not exist yet.

## Authentication

Laravel Fortify provides registration, login, logout, password reset, and email
verification routes and actions. The starter `User` model, factory, profile
settings, and security settings are present.

The Fortify email-verification feature is configured. The application has not
yet activated the product's final authentication and authorization model:

- Domain routes do not exist yet.
- `operator` and `admin` roles do not exist.
- No domain policies or gates exist.
- The user model does not currently opt into the `MustVerifyEmail` contract.

Those decisions remain in the authentication roadmap phase.

## Testing

Pest is configured through `tests/Pest.php` and `phpunit.xml`.

Feature tests use Laravel's `RefreshDatabase` trait. PHPUnit forces the
`pgsql` connection and `kers_testing` database, preventing tests from targeting
the development database.

The current suite covers starter authentication, dashboard, profile, security,
and home-page behavior, plus Kaiju persistence, enum casting, and database
constraints.

## Quality and CI

Composer scripts provide the main quality pipeline:

```text
composer test
→ clear Laravel configuration cache
→ Pint formatting check
→ PHPStan analysis
→ Pest test suite
```

GitHub Actions checks pushes to `main` and pull requests. It provisions a
PostgreSQL 18 service, installs PHP and Node dependencies, prepares the
application, builds frontend assets, and runs the Composer CI checks.

## Current architectural decisions

### Laravel

Laravel supplies an integrated learning path for routing, configuration,
database access, validation, testing, authentication, and authorization.

### Livewire

Livewire enables interactive server-driven pages while keeping the primary
learning focus on Laravel and PHP. Single-file components provide a familiar
colocation model without introducing a separate SPA.

### PostgreSQL

Using PostgreSQL in every environment prevents SQLite-specific behavior from
hiding constraint, type, or query differences before deployment.

### Conventional structure

Native Laravel conventions remain visible and educational. Abstractions are
introduced only in response to implemented complexity.

### Portability

Configuration uses environment variables, secrets remain outside Git, and no
provider-specific deployment or persistent local-disk assumption exists.

## Planned but not implemented

The product requirements anticipate Eloquent domain models, relationships,
enums, policies, USGS HTTP mapping, capacity validation, and dashboard
aggregates. Their architecture will be documented here only after the
corresponding roadmap items are implemented.
