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

### Demo data API

KERS also exposes two non-visual API operations for resetting the temporary
demo without rebuilding its database:

```text
DELETE /api/demo-data
POST /api/demo-data/seed
```

Set a strong `KERS_DEMO_API_KEY` in the local `.env` file before using them.
The routes are unavailable while this value is empty. Send the same value as a
Bearer token:

```bash
DEMO_BASE_URL='http://localhost:8000'
DEMO_API_KEY='value-from-KERS_DEMO_API_KEY'

curl --request DELETE \
    --header "Authorization: Bearer ${DEMO_API_KEY}" \
    "${DEMO_BASE_URL}/api/demo-data"

curl --request POST \
    --header "Authorization: Bearer ${DEMO_API_KEY}" \
    "${DEMO_BASE_URL}/api/demo-data/seed"

unset DEMO_BASE_URL DEMO_API_KEY
```

The wipe operation deletes only Kaijus and their cascaded Incidents; it does
not drop tables or run migrations. Seeding alone preserves additional records
while restoring canonical seed values. Call wipe and then seed to restore
exactly the representative dataset.

For the deployed demo, configure `KERS_DEMO_API_KEY` as a secret variable on
the Railway application service. Do not commit its value or add it to GitHub:
GitHub Actions neither deploys the application nor calls these endpoints.

In Railway, open the Laravel application service, select **Variables**, create
`KERS_DEMO_API_KEY`, optionally seal it, and deploy the staged variable change.
Once the deployment is active, use the exact production routes:

```bash
RAILWAY_DEMO_API_KEY='value-configured-in-railway'

curl --request DELETE \
    --header "Accept: application/json" \
    --header "Authorization: Bearer ${RAILWAY_DEMO_API_KEY}" \
    https://kers-production.up.railway.app/api/demo-data

curl --request POST \
    --header "Accept: application/json" \
    --header "Authorization: Bearer ${RAILWAY_DEMO_API_KEY}" \
    https://kers-production.up.railway.app/api/demo-data/seed

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
