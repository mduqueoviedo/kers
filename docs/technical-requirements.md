# Technical Requirements

## Technology

KERS uses:

- PHP 8.5
- Laravel 13
- Livewire 4 with single-file components
- Tailwind CSS 4
- PostgreSQL 18
- Pest
- Laravel Pint
- The Laravel Livewire starter kit and Fortify authentication scaffold

These choices must not be replaced without explicit approval.

## Language

Everything stored in the repository is written in English, including source
code, user-facing copy, tests, documentation, branches, commits, and pull
requests.

## Runtime and configuration

- Configuration is provided through environment variables.
- Secrets are never committed.
- `.env.example` documents required variables without real passwords.
- PostgreSQL is the only project database for development, tests, and
  deployment.
- Tests use a database separate from local development.
- The application must not silently fall back to SQLite.
- Persistent local disk and provider-specific services are avoided.

## Demo API security

The temporary demo-data API uses native Laravel routing, a controller, and
middleware. Its safeguards are:

- Only `DELETE /api/demo-data` and `POST /api/demo-data/seed` mutate demo data.
- Both routes require `KERS_DEMO_API_KEY` as an HTTPS Bearer token.
- An empty configured key disables the routes.
- Missing or invalid keys must never modify data.
- Credentials are never accepted in query parameters.
- Wiping removes domain records only; it must not drop or rebuild the schema.
- The real key exists only in local `.env` configuration or the Railway
  application service.
- GitHub Actions does not require this secret because tests override Laravel
  configuration and do not call the deployed environment.

This API is temporary demo tooling, not a replacement for the planned user,
role, policy, and authorization model.

## Application architecture

Use conventional Laravel structure and native framework features before custom
abstractions:

- Eloquent models and relationships
- Reversible migrations and database constraints
- PHP enums and model casts
- Factories and seeders
- Livewire validation and state
- Policies and middleware when authorization is introduced
- Laravel's HTTP client and configuration for USGS

Do not introduce repository layers, DTO frameworks, generic action systems,
interfaces for every service, speculative domain layers, or event-driven
architecture without a current need.

Logic is extracted only when an implemented responsibility has become complex
or repeated.

## Database rules

- Foreign keys and database-level constraints enforce durable invariants.
- Kaiju names use an exact unique constraint.
- Kaiju deletion cascades to associated incidents.
- Incident occurrence times use UTC throughout the application.
- Response-team capacity is enforced using current incident state.
- USGS source identifiers use an appropriate uniqueness constraint.
- Indexes support actual search, filtering, relationships, and uniqueness
  requirements.
- Migrations are reversible unless an irreversible change is explicitly
  reviewed.
- Factories produce representative test data when their models are introduced.

## Livewire and interface rules

- Domain pages use single-file Livewire components.
- Early interfaces remain simple, accessible, and functional.
- Forms provide clear validation errors.
- Lists provide simple empty states.
- Destructive actions require explicit confirmation.
- Pagination precedes reactive search and filtering.
- Reusable components are extracted only after meaningful repetition exists.
- Final command-centre styling must remain restrained and readable.

The intended progression is:

```text
Basic pages and navigation
→ lists
→ forms and validation
→ pagination
→ search and filters
→ reusable components
→ feedback states
→ dashboard
→ visual refinement
```

## Testing

Pest is the primary test syntax.

- Feature tests cover routes, pages, forms, and complete behavior.
- Livewire tests cover state, actions, rendering, and validation.
- Database assertions cover persistence and relationships.
- Unit tests are reserved for genuinely isolated business logic.
- HTTP fakes replace every USGS request in automated tests.
- Every bug fix includes a regression test.
- Tests assert observable behavior and business rules rather than framework
  internals.

Every functional acceptance criterion requires meaningful automated coverage
in the same pull request.

The primary command is:

```bash
php artisan test
```

## Quality

At minimum, every pull request runs:

```bash
php artisan test
vendor/bin/pint --test
```

Static analysis remains supporting infrastructure rather than a core learning
objective. The existing command is:

```bash
composer types:check
```

Frontend changes also require:

```bash
npm run build
```

GitHub Actions installs backend and frontend dependencies, provisions
PostgreSQL, runs migrations, executes tests and formatting checks, performs
static analysis, and builds frontend assets through the existing Composer
workflow.

## Dependency policy

No Composer, npm, or other third-party dependency is added without explicit
approval.

A dependency proposal must explain the problem, why current tools are
insufficient, its complexity and maintenance cost, and the alternative without
the dependency. Built-in Laravel and PHP features are preferred.

## Demo authentication

The application uses Fortify only for standard session-based login and logout.
One configuration-backed demo user is seeded by email with a Laravel-hashed
password. Registration, password recovery, email verification, profiles, user
management, roles, and policies are out of scope for the disposable demo.

Public catalogue and detail pages remain readable. Route middleware and
server-side Livewire checks protect every Kaiju, Incident, and USGS-import
mutation; hiding a control is never the sole authorization measure.

## External HTTP

USGS integration uses Laravel's HTTP client, configuration, environment
variables, explicit response mapping, failure handling, and HTTP fakes.

External payloads do not determine the core domain model. Jobs or queues are
not added merely because an HTTP request is involved.

## Git and pull requests

Each iteration uses one focused branch, one primary learning objective, clear
imperative commits, and one reviewable pull request.

Before implementation, the proposal must describe objective, value, concepts,
scope, files, acceptance criteria, testing, risks, and split decisions. Material
scope changes require renewed approval.

Codex may create branches, commit, push, and open pull requests. The owner
reviews and merges through squash merge. Codex never merges, pushes directly to
`main`, force-pushes without permission, or starts the next item automatically.

Every pull request describes its summary, Laravel concepts, main changes,
testing, review guide, documentation impact, and roadmap status.

## Portability and deployment

Railway is the confirmed provider for the initial temporary technical demo.
This is a high-priority delivery checkpoint rather than the final product
deployment. It does not select a provider for any future long-lived
environment.

The initial deployment uses one Railway project containing:

- A Laravel application service connected directly to this GitHub repository
- A Railway PostgreSQL service
- Railway's private service connection for application database traffic
- A public Railway URL for the current application

Railway observes `main` and automatically builds and deploys after merges.
GitHub Actions remains an independent validation gate and does not initially
deploy the application. Railway's `Wait for CI` option is enabled manually.

Railpack handles dependency installation, the Vite build, and the Laravel web
server automatically. The repository does not define custom build or start
commands. `railway.json` adds only the explicit pre-deploy lifecycle command,
the `/up` healthcheck, and a limited restart policy.

Production configuration must:

- Set `APP_ENV=production` and disable debug mode.
- Generate and configure `APP_KEY` outside the repository.
- Provide database and other environment-specific values through Railway.
- Keep secrets and real credentials out of Git and `.env.example`.
- Use PostgreSQL without a SQLite fallback.
- Install production Composer dependencies and build Vite assets.
- Set `RAILPACK_SKIP_MIGRATIONS=true` to disable Railpack's implicit migration
  and seeding startup behavior.
- Run pending migrations and then seed only `DemoUserSeeder` in the pre-deploy
  phase.
- Do not drop Railway tables or automatically seed Kaijus and Incidents.
- This is a pragmatic reliability rollback: Railway's opaque pre-deploy
  failures made the destructive rebuild impossible to diagnose. Use the
  protected demo-data API for an explicit domain reset.
- Populate only a small, production-safe demonstration dataset.
- Treat the application filesystem as ephemeral.
- Avoid local uploads unless losing them is an explicitly accepted demo
  limitation.

The environment is expected to remain active for approximately one week. Its
expected cost is zero through Railway trial credit. Usage must be reviewed
after the demo, and the application and database services must then be stopped
or removed when no longer needed.

The initial scope excludes Redis, queues, workers, custom domains, external
storage, advanced observability, and other unnecessary infrastructure. Docker
is permitted only if the repository audit demonstrates that Railway cannot
deploy the application reliably without it.

The required Railway application-service variables are:

```text
APP_NAME=KERS
APP_ENV=production
APP_DEBUG=false
APP_KEY=<generated Laravel key>
APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
KERS_DEMO_API_KEY=<generated random secret>
KERS_DEMO_USER_NAME="KERS Demo Operator"
KERS_DEMO_USER_EMAIL=demo@kers.test
KERS_DEMO_USER_PASSWORD=kers-demo-password
DB_CONNECTION=pgsql
DB_URL=${{Postgres.DATABASE_URL}}
LOG_CHANNEL=stderr
LOG_LEVEL=info
SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
CACHE_STORE=database
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
RAILPACK_SKIP_MIGRATIONS=true
```

`APP_KEY` is generated outside Git and stored only as a Railway secret.
`KERS_DEMO_API_KEY` is a separate random secret required to use the protected
demo reset operations. Leaving it empty disables those routes.
The demo-user values are intentionally public credentials for the disposable
application; the seeded account is recreated during every pre-deploy command.
`DB_URL` references the PostgreSQL service inside the same Railway project.
The local `.env` file is never copied to Railway.

Railway runs only `DemoUserSeeder` during pre-deploy, preserving editable
Kaijus and Incidents. The full repeatable seeders remain available for local
development and the protected demo API.
