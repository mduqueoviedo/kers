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

## Authentication and authorization

The Fortify starter scaffold remains available, but domain authorization is
introduced only in its roadmap phase.

Authorization uses Laravel middleware, policies, and Livewire authorization
checks. Roles are limited to `operator` and `admin`; no permissions package or
configurable matrix is required.

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

Deployment remains the final core phase. Until then:

- Use environment variables for all environment-specific configuration.
- Keep `.env.example` current.
- Use PostgreSQL.
- Avoid provider-specific files and services.
- Avoid relying on persistent local disk.
- Document migration, build, test, and startup commands.
- Add Docker only if it improves deployment portability during that phase.

Provider selection is deferred until current cost, PostgreSQL support, Laravel
support, operational simplicity, and demo reliability can be evaluated.
