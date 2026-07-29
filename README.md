# KERS

KERS (Kaiju Emergency Response System) is a Laravel learning project for
managing fictional kaijus, their incidents, and the response teams assigned to
them.

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

## Development

Start the Laravel application and frontend development server:

```bash
composer dev
```

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
