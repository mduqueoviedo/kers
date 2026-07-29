# Architecture

## Status

This document describes the architecture present after adding the first KERS
domain model, its development data, and its public catalogue. Incidents,
response teams, capacity rules, USGS integration, roles, policies, and the
operational dashboard are not implemented yet.

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
- `routes/` declares web and console routes.
- `tests/` contains Pest feature tests.

No custom domain layer, repository abstraction, or generic service layer is
present.

## HTTP and UI

The root route redirects to the public `/kaijus` catalogue. That Livewire page
queries and renders the current Kaiju records.

The shared application layout provides a small top navigation with the KERS
name and catalogue link. The generated welcome page, sidebar, dashboard, user
menus, and account settings UI have been removed so the visible application
only represents implemented KERS functionality.

The starter UI uses:

- Livewire 4
- Flux components
- Tailwind CSS 4
- Vite 8
- Shared Blade layouts and components

The Kaiju catalogue, registration form, and detail view are the first KERS
domain pages. Editing and the remaining domain workflows are not implemented
yet.

## Livewire

Single-file components colocate a Livewire anonymous component class and its
Blade template. The public Kaiju catalogue uses Livewire's `WithPagination`
trait and exposes an ordered Eloquent paginator through a computed property.
The current page is represented in the URL. Its page size comes from the
KERS-specific `kers.pagination.kaijus_per_page` configuration value, which
defaults to nine. The registration component holds form state, validates it,
creates a Kaiju through Eloquent, dispatches toast feedback, and redirects to
the catalogue. The detail component receives its Kaiju through route model
binding in `mount()`; Laravel returns 404 before rendering when the route key is
missing.

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
categories to the enum's current values and threat levels to the range 1–5. A
unique index prevents exact duplicate names. Livewire validates these rules
before persistence so users receive form errors instead of database exceptions.
`KaijuFactory` generates valid test records, while `KaijuSeeder` maintains 12
deterministic local records covering every category and both initial catalogue
pages. The root database seeder is safe to repeat without duplicating that
catalogue. Incident and response-team tables do not exist yet.

## Authentication

Laravel Fortify provides registration, login, logout, password reset, and email
verification routes and actions. The `User` model, factory, and authentication
views remain as the foundation for the later authentication phase. Successful
authentication redirects to the public Kaiju catalogue; there is no temporary
starter dashboard or account-settings UI.

The Fortify email-verification feature is configured. The application has not
yet activated the product's final authentication and authorization model:

- Current domain routes remain public.
- `operator` and `admin` roles do not exist.
- No domain policies or gates exist.
- The user model does not currently opt into the `MustVerifyEmail` contract.

Those decisions remain in the authentication roadmap phase.

## Testing

Pest is configured through `tests/Pest.php` and `phpunit.xml`.

Feature tests use Laravel's `RefreshDatabase` trait. PHPUnit forces the
`pgsql` connection and `kers_testing` database, preventing tests from targeting
the development database.

The current suite covers authentication, root redirection, the paginated public
catalogue, Kaiju registration, and route-bound details, plus persistence, enum
casting, database constraints, factory states, and repeatable seeding.

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
