# KERS

[![tests](https://github.com/mduqueoviedo/kers/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/mduqueoviedo/kers/actions/workflows/tests.yml)

KERS (Kaiju Emergency Response System) is a Laravel learning project for
managing fictional kaijus, their incidents, and the response teams assigned to
them.

## Live demo

[Open the KERS live demo](https://kers-production.up.railway.app/kaijus)

This temporary production-like environment was created for the technical demo
and may be removed after the review period.

## Technology

- PHP 8.5
- Laravel 13
- Livewire 4 with single-file components
- Tailwind CSS 4
- PostgreSQL 18
- Pest

## Requirements

- PHP 8.5 with the PostgreSQL PDO extension
- Composer 2
- Node.js 22 or later with npm
- PostgreSQL 18

## Installation

Install the backend and frontend dependencies:

```bash
composer install
npm install
```

Create the local environment file and application key:

```bash
cp .env.example .env
php artisan key:generate
```

## Database setup

Open the PostgreSQL prompt as its administrative system user:

```bash
sudo -u postgres psql
```

Create a local application role and separate development and test databases:

```sql
CREATE ROLE kers WITH LOGIN PASSWORD 'your-local-password';
CREATE DATABASE kers OWNER kers;
CREATE DATABASE kers_testing OWNER kers;
\q
```

Set `DB_PASSWORD` in `.env` to the password selected above. Update the other
`DB_*` values if your local PostgreSQL host, port, database, or username differs
from the provided defaults.

Run the development database migrations:

```bash
php artisan migrate
```

Optionally seed representative local data:

```bash
php artisan db:seed
```

The deterministic demo data contains 12 kaijus and 9 related incidents. It
covers both catalogue pages, every Kaiju category, and every Incident status.

### Reset local data

Rebuild the development database with empty tables:

```bash
php artisan migrate:fresh
```

Rebuild it with the representative demo data:

```bash
php artisan migrate:fresh --seed
```

Both commands delete all data in the configured database before running the
migrations. Use them only for local development and confirm that `.env` points
to the intended `kers` database first.

Railway runs pending migrations and then seeds only the deterministic demo user
before each deployment. It does not drop tables or automatically seed Kaijus
and Incidents. This is a pragmatic reliability decision after opaque Railway
pre-deploy failures made the destructive rebuild impossible to diagnose. Use
the protected demo-data API below when a full Kaiju and Incident reset is
required.

### Demo login

The disposable demo has one configured account. `migrate:fresh --seed` in
local development and Railway's dedicated pre-deploy seeder recreate it using
its email as the stable identifier.
Set these variables outside Git when needed; the `.env.example` values are safe
public demo defaults and are also displayed on the login page:

```text
KERS_DEMO_USER_NAME="KERS Demo Operator"
KERS_DEMO_USER_EMAIL=demo@kers.test
KERS_DEMO_USER_PASSWORD=kers-demo-password
```

The password is hashed through Laravel when the user is seeded. Registration,
password recovery, email verification, profiles, and user management are not
part of this disposable demo.

### Demo data API

KERS exposes one non-visual API operation for resetting the temporary demo
without rebuilding its database:

```text
POST /api/demo-data/reset
```

Set a strong `KERS_DEMO_API_KEY` in the local `.env` file before using it.
The routes are unavailable while this value is empty. Send the same value as a
Bearer token:

```bash
DEMO_BASE_URL='http://localhost:8000'
DEMO_API_KEY='value-from-KERS_DEMO_API_KEY'

curl --request POST \
    --header "Authorization: Bearer ${DEMO_API_KEY}" \
    "${DEMO_BASE_URL}/api/demo-data/reset"

unset DEMO_BASE_URL DEMO_API_KEY
```

The reset operation deletes only Kaijus and their cascaded Incidents, then
restores the canonical Kaiju and Incident seed data. It does not drop tables,
run migrations, or change users. This protected API operation is the explicit
reset mechanism for Railway as well as local development.

For the deployed demo, configure `KERS_DEMO_API_KEY` as a secret variable on
the Railway application service. Do not commit its value or add it to GitHub:
GitHub Actions neither deploys the application nor calls these endpoints.

In Railway, open the Laravel application service, select **Variables**, create
`KERS_DEMO_API_KEY`, optionally seal it, and deploy the staged variable change.
Once the deployment is active, use the exact production routes:

```bash
RAILWAY_DEMO_API_KEY='value-configured-in-railway'

curl --request POST \
    --header "Accept: application/json" \
    --header "Authorization: Bearer ${RAILWAY_DEMO_API_KEY}" \
    https://kers-production.up.railway.app/api/demo-data/reset

unset RAILWAY_DEMO_API_KEY
```

## Development

Start the Laravel application and frontend development server:

```bash
composer dev
```

## Deployment status

A minimal temporary Railway deployment is the current delivery priority. It is
intended to expose the same KERS behavior already verified locally, not the
complete planned product.

The confirmed architecture, environment variables, manual dashboard steps, and
acceptance criteria are maintained in the
[architecture](docs/architecture.md), [technical requirements](docs/technical-requirements.md),
[roadmap](docs/roadmap.md), and [workflows](docs/workflows.md). Do not infer
production values from the local `.env` file.

## Quality checks

Run the test suite:

```bash
php artisan test
```

Check PHP formatting:

```bash
vendor/bin/pint --test
```

Run static analysis:

```bash
composer types:check
```

Build the frontend assets:

```bash
npm run build
```

## Documentation

- [Project instructions](AGENTS.md)
- [Product requirements](docs/product-requirements.md)
- [Technical requirements](docs/technical-requirements.md)
- [Roadmap](docs/roadmap.md)
- [Architecture](docs/architecture.md)
- [Workflows](docs/workflows.md)
