# Architecture

## Status

This document describes the architecture present after adding the Kaiju
management flow, manual Incident creation, the paginated Incident catalogue,
Incident search, filtering, ordering, details and editing, and a protected
demo-data API. Incident deletion, response teams, capacity rules, USGS
integration, roles, policies, and the operational dashboard are not implemented
yet.

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

Authorized API client
→ Laravel API route and demo-key middleware
→ DemoDataController
→ Eloquent or the application seeder
→ PostgreSQL
```

There is no separate frontend application, API service, worker service, or
external integration process.

## Railway deployment

Status: deployed and verified for the temporary technical demo.

```text
GitHub repository
├── pull requests and main → GitHub Actions → tests and validation
└── main branch → Railway observes changes → application build and deployment

Browser
→ https://kers-production.up.railway.app/kaijus
→ Railway Laravel application service
→ private Railway connection
→ Railway PostgreSQL service
```

The application and PostgreSQL run as separate services in the same Railway
project. Database traffic uses Railway's private service connection, while only
the application receives a public demo URL.

GitHub Actions remains the validation system. It does not initially publish or
deploy artifacts. Railway connects directly to the repository, observes
`main`, and automatically rebuilds and deploys after merged changes.

The public environment reproduces the current locally verified application
behavior. It does not require the remaining product roadmap, additional
runtime services, or a permanent hosting decision.

Railpack detects the Laravel application, installs Composer and npm
dependencies, builds Vite assets, and serves the application without a custom
build or start command. `railway.json` configures `/up` as the deployment
healthcheck and runs this explicit pre-deploy command:

```text
php artisan migrate --force --no-interaction && php artisan db:seed --force --no-interaction
```

The Railway application service must set
`RAILPACK_SKIP_MIGRATIONS=true` so Railpack does not repeat migration and
seeding during startup. The current seeders use `updateOrCreate`: redeployments
do not delete unrelated data, but may restore the canonical values of
predefined demo records.

Laravel trusts Railway's reverse proxy and its standard forwarded headers,
including `X-Forwarded-Proto`, so generated asset URLs retain the public HTTPS
scheme. The Railway build, Vite assets, PostgreSQL migrations and seeders,
public catalogue, reverse-proxy handling, and `/up` healthcheck have been
verified successfully.

Docker was not required.

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
name and links to the Kaiju and Incident catalogues. The generated welcome
page, sidebar, dashboard, user menus, and account settings UI have been removed
so the visible application only represents implemented KERS functionality.

The starter UI uses:

- Livewire 4
- Flux components
- Tailwind CSS 4
- Vite 8
- Shared Blade layouts and components

The Kaiju catalogue, management forms, detail view with Incident history,
confirmed deletion action, manual Incident form, Incident catalogue, and
Incident detail view are the first KERS domain interfaces. The remaining
domain workflows are not implemented yet.

## Demo data API

Laravel loads `routes/api.php` through `bootstrap/app.php`, which gives these
routes the `/api` prefix and API middleware behavior without introducing a
separate service. `DemoDataController` exposes two JSON operations:

```text
DELETE /api/demo-data
POST /api/demo-data/seed
```

The `EnsureDemoApiKey` middleware runs before the controller and compares the
request's Bearer token with `kers.demo_api_key`, populated by
`KERS_DEMO_API_KEY`. An empty configured key makes both routes unavailable;
missing or incorrect credentials receive an unauthorized JSON response. The
real key exists only in local environment configuration or Railway.

Wiping deletes all Kaijus in a transaction and relies on the existing
PostgreSQL foreign-key cascade to delete Incidents. It does not change the
schema or Laravel infrastructure data. Seeding invokes the existing
`db:seed --force` Artisan command in a transaction. Because the seeders use
`updateOrCreate`, seeding alone preserves additional records; wipe followed by
seed restores the canonical dataset.

## Livewire

Single-file components colocate a Livewire anonymous component class and its
Blade template. The public Kaiju catalogue uses Livewire's `WithPagination`
trait and exposes an ordered Eloquent paginator through a computed property.
The current page, name search, category, and threat level are represented in
the URL. Updating any criterion resets pagination before the computed query
applies its conditions and alphabetical ordering. PostgreSQL receives Laravel's
case-insensitive `whereLike()` name search plus exact category and threat-level
conditions. The page size comes from the KERS-specific
`kers.pagination.kaijus_per_page` configuration value, which defaults to nine.
The registration component holds form state, validates it, creates a Kaiju
through Eloquent, dispatches toast feedback, and redirects to the catalogue.
The detail and edit components receive their Kaiju through route model binding
in `mount()`; Laravel returns 404 before rendering when the route key is
missing. The detail component eager loads its Incidents in newest-first
occurrence order, renders their current status and UTC occurrence time, and
links each entry to its existing detail route. The loaded relationship also
supplies the deletion warning count without issuing a second count query. The
edit component initializes scalar form state from that model, validates
changes, and updates it through Eloquent. Its unique-name rule ignores only the
current model, allowing an unchanged name while continuing to reject another
Kaiju's exact name. The detail component also owns a boolean confirmation state
for permanent deletion. Opening or cancelling its Flux modal does not change
persistence, and the Eloquent delete action is guarded by that state before
redirecting to the catalogue. The modal uses Laravel pluralization to state
the exact number of incidents affected before PostgreSQL performs its cascade.

The public Incident creation component loads known Kaijus alphabetically,
validates all submitted state, converts its timezone-free form value explicitly
to UTC, and creates the record through `Kaiju::incidents()`. Its Kaiju selection
is synchronized with the `kaiju` query parameter. The general catalogue link
opens an unselected form, while a Kaiju detail link opens that same route with
its Kaiju preselected. The query parameter remains untrusted and must pass both
Livewire existence validation and the PostgreSQL foreign key.

The public Incident catalogue uses Livewire pagination, case-insensitive title
or location search, exact status and Kaiju filters, and selectable newest- or
oldest-first occurrence ordering. Search, filters, ordering, and pagination are
synchronized with the URL. Changing any criterion resets pagination, and all
criteria can be cleared together. The Eloquent query groups the two search
columns before applying exact filters and uses the identifier as a deterministic
ordering tie-breaker. It eager loads each Incident's Kaiju in one relationship
query, shows occurrence times explicitly as UTC, and uses the configurable
`kers.pagination.incidents_per_page` page size, which defaults to nine.
Each catalogue card links to a single-file detail component. Laravel resolves
the `Incident` route parameter into its Eloquent model before `mount()` runs;
missing identifiers therefore return 404 without custom lookup code. The
component eager loads its Kaiju and renders the incident, relationship, and
timestamps explicitly in UTC.

The Incident edit component also receives its model through route model
binding, then initializes scalar form state in `mount()`. It validates the
selected Kaiju and status against current database and enum values, converts
the timezone-free date input to UTC, and updates the Eloquent model. All three
supported statuses may currently transition to any other supported status.

The Incident detail component owns a boolean confirmation state for permanent
deletion. Opening or cancelling its Flux modal preserves the record, and the
delete action returns without changing persistence unless confirmation is
active. A confirmed Eloquent deletion removes only that Incident, preserves its
Kaiju, and redirects to the catalogue with toast feedback.

Kaiju-category and Incident-status badge colors are presentation settings
stored under `kers.badges`. The enums remain responsible for valid domain
values, while the centralized configuration lets the visual mapping change
without adding conditional color logic to individual Blade views.

## Database

PostgreSQL 18 is the project database for local development, automated tests,
CI, and the target Railway deployment.

Database selection flows from environment variables through
`config/database.php`. The development database is `kers`; PHPUnit selects the
separate `kers_testing` database.

The current schema contains:

- Users, password-reset tokens, and sessions
- Cache and cache locks
- Jobs, job batches, and failed jobs
- Kaijus with category and threat-level constraints
- Incidents belonging to known Kaijus with status and foreign-key constraints
- Laravel's migration history

The `Kaiju` Eloquent model casts its stored category string to the
`KaijuCategory` PHP enum. PostgreSQL check constraints independently restrict
categories to the enum's current values and threat levels to the range 1–5. A
unique index prevents exact duplicate names. Livewire validates these rules
before persistence so users receive form errors instead of database exceptions.
`KaijuFactory` generates valid test records, while `KaijuSeeder` maintains 12
deterministic local records covering every category and both initial catalogue
pages. `IncidentSeeder` maintains nine deterministic UTC incidents associated
with those Kaijus and covering every lifecycle status. The root database seeder
runs the Kaiju seeder first and is safe to repeat without duplicating either
dataset.

Each `Incident` belongs to one `Kaiju`, and a Kaiju has many incidents.
PostgreSQL rejects unknown Kaiju references and invalid status values. Its
foreign key cascades deletion from a Kaiju to its incidents while leaving other
Kaijus' incidents untouched. Eloquent casts status to the `IncidentStatus` enum
and occurrence time to the application's configured immutable Carbon date
class. `occurred_at` is a timezone-free PostgreSQL timestamp whose value is
always interpreted as UTC; the Laravel application timezone is also UTC.
`IncidentFactory` creates valid related records, can reuse an explicit Kaiju
through Laravel's `for()` factory method, and provides open, contained, and
closed states. The manual creation, paginated catalogue, detail, and editing
routes are implemented. Confirmed deletion is handled by the detail component;
search and filtering are not implemented yet.

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
Kaiju catalogue with combined search and filters, Kaiju registration,
route-bound details, editing, and confirmed deletion, plus persistence, enum
casting, database constraints, Incident relationships and cascade behavior,
factory states for both current domain models, repeatable related seeding,
exact cascade-warning counts, manual Incident creation with validation, and
the paginated Incident catalogue with combined search, filters, ordering,
URL-backed state, eager-loaded Kaijus, and UTC dates.
Incident detail tests cover route model binding, current record and relationship
rendering, catalogue navigation, configured badges, and missing-record 404
responses. Incident edit tests cover prefilled state, complete updates,
relationship reassignment, status changes, UTC conversion, validation,
navigation, and missing-record 404 responses. Incident deletion tests cover
confirmation, cancellation, guarded actions, selective deletion, relationship
preservation, feedback, and redirection. API tests cover disabled and invalid
credentials, allowed HTTP methods, domain-only deletion, repeatable seeding,
and canonical reset behavior.

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

Configuration uses environment variables and secrets remain outside Git. The
temporary Railway architecture keeps the application and PostgreSQL together
for operational simplicity while continuing to use standard Laravel and
PostgreSQL configuration.

Railway storage is treated as ephemeral. The initial demo does not add uploads,
external storage, Redis, queues, workers, custom domains, or advanced
observability. Both Railway services should be stopped or removed after the
approximately one-week demo period when they are no longer needed.

## Planned but not implemented

The product requirements anticipate further Eloquent domain models,
relationships, policies, USGS HTTP mapping, capacity validation, and dashboard
aggregates. Their architecture will be documented here only after the
corresponding roadmap items are implemented.
