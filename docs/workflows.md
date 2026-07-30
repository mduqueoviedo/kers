# Workflows

## Purpose

This document summarizes meaningful current workflows and clearly labels
planned product workflows. It does not attempt to document every route,
component method, or screen.

## Current development workflows

### Install and configure the application

```text
Install Composer and npm dependencies
→ copy .env.example to .env
→ generate the Laravel application key
→ create the kers and kers_testing PostgreSQL databases
→ configure the local database password
→ run Laravel migrations
```

The full commands are maintained in the project README.

### Start local development

```text
Run composer dev
→ Laravel development processes start
→ Vite serves frontend assets
→ open the configured APP_URL
→ the root URL redirects to the Kaiju catalogue
```

### Run automated tests

```text
Run php artisan test
→ PHPUnit applies the testing environment
→ Laravel connects to kers_testing
→ RefreshDatabase prepares schema state
→ Pest executes feature tests
→ the development database remains untouched
```

### Validate a pull request

```text
Open or update a pull request
→ GitHub Actions starts PostgreSQL
→ Composer prepares the Laravel application
→ npm builds frontend assets
→ Pint checks formatting
→ PHPStan checks analysed PHP code
→ Pest runs against the CI test database
```

## Current deployment workflow

### Deploy the current application to Railway

The temporary environment is deployed at:

[Open the KERS live demo](https://kers-production.up.railway.app/kaijus)

```text
Complete the deployment-readiness audit
→ connect Railway directly to the GitHub repository
→ create one Laravel application service
→ create PostgreSQL in the same Railway project
→ configure production-safe Laravel environment variables and APP_KEY
→ connect Laravel to PostgreSQL over Railway's private network
→ set RAILPACK_SKIP_MIGRATIONS=true
→ let Railpack install dependencies, build Vite, and serve Laravel
→ railway.json runs migrations and the demo seeder during pre-deploy
→ expose and smoke-test the public Railway URL
→ merge a later change into main
→ verify Railway automatically deploys that revision
```

GitHub Actions continues to run the test and quality pipeline. Railway observes
`main` directly; GitHub Actions does not initially deploy the application.

The deployment must not copy local `.env` values, commit secrets, reset the
production database, or depend on persistent local files. It does not add
Redis, queues, workers, custom domains, external storage, or advanced
observability.

The explicit pre-deploy command is:

```text
php artisan migrate --force --no-interaction && php artisan db:seed --force --no-interaction
```

The seeders are repeatable and preserve unrelated records. A redeployment may
restore canonical values for predefined demo records because the seeders use
`updateOrCreate`.

The Railway dashboard configuration remains manual:

1. Connect the application service to the GitHub repository and `main`.
2. Add PostgreSQL to the same project.
3. Add the documented Laravel variables, generate `APP_KEY`, and reference the
   PostgreSQL `DATABASE_URL` as `DB_URL`.
4. Generate and add `KERS_DEMO_API_KEY` as a secret application variable.
5. Generate a public Railway domain for the application service.
6. Enable automatic deployments and `Wait for CI`.
7. Inspect the build, pre-deploy, and runtime logs.
8. Smoke-test the current public Kaiju and Incident workflows.

The initial deployment verified:

- Railway builds the Laravel application successfully.
- Vite assets build and load over HTTPS.
- PostgreSQL migrations and repeatable seeders complete successfully.
- The public Kaiju catalogue is accessible.
- Laravel uses Railway's forwarded proxy headers for the correct HTTPS scheme.
- The `/up` healthcheck succeeds.
- Changes from the connected GitHub branch deploy automatically.

### Reset the temporary demo through the API

The application service must have a strong `KERS_DEMO_API_KEY` configured in
Railway. Open the Laravel service's **Variables** tab, create the variable,
optionally seal it, and deploy the staged variable change. The key is sent only
in the HTTPS `Authorization: Bearer` header; it is never committed, added to a
URL, or stored as a GitHub Actions secret.

Use these exact commands against the production demo:

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

```text
Send DELETE /api/demo-data with the configured Bearer key
→ demo-key middleware authenticates the request
→ DemoDataController counts current Kaijus and Incidents
→ Eloquent deletes all Kaijus in a transaction
→ PostgreSQL cascades deletion to their Incidents
→ receive a JSON summary of deleted domain records

Send POST /api/demo-data/seed with the configured Bearer key
→ demo-key middleware authenticates the request
→ DemoDataController runs the repeatable DatabaseSeeder
→ receive a JSON summary of current domain records
```

Calling seed alone restores canonical values and preserves additional records.
Calling wipe and then seed restores exactly the representative dataset. Neither
operation drops tables or runs migrations. When the key is empty, both routes
are unavailable; an incorrect or missing key cannot modify data.

### Verify and retire the temporary demo

```text
Open the public URL
→ confirm APP_ENV is production and debug mode is disabled
→ exercise the same Kaiju and Incident creation flows used locally
→ verify representative demo data and static assets
→ confirm GitHub Actions and the latest Railway deployment are successful
→ keep the environment available for the demonstration period
→ review usage after approximately one week
→ stop or remove both Railway services when no longer needed
```

## Current user workflows

### Browse the kaiju catalogue

```text
Open the public kaiju catalogue
→ Livewire reads the requested page from the URL
→ optionally search by name and filter by category or threat level
→ Eloquent combines active criteria and queries matching kaijus
→ display a catalogue page or the appropriate empty state
→ show each category, threat level, and description
→ navigate between pages without a full browser reload
```

Criteria are synchronized with the URL and can be combined or cleared
together. Changing any criterion returns pagination to its first page. Name
search is partial and case-insensitive. No authentication is required.

### View a kaiju

```text
Follow a detail link from the public catalogue
→ Laravel resolves the Kaiju route parameter
→ Livewire receives the Eloquent model in mount()
→ display its category, threat level, description, and friendly timestamps
→ return to the catalogue
```

An unknown route key returns Laravel's standard 404 response.

### Register a kaiju

```text
Open the public Kaiju registration form
→ enter name, category, threat level, and optional description
→ Livewire validates the submitted state
→ Eloquent stores the Kaiju
→ redirect to the catalogue with confirmation feedback
```

Names must be unique using exact PostgreSQL string comparison. Category and
threat-level rules are enforced both before persistence and by the database.

### Edit a kaiju

```text
Open a Kaiju detail page
→ follow its edit link
→ Laravel resolves the Kaiju route parameter
→ Livewire prefills scalar form state from the Eloquent model
→ change the known details
→ Livewire validates and Eloquent updates the Kaiju
→ redirect to the updated detail page with confirmation feedback
```

The current Kaiju is excluded from the unique-name check, so its name may
remain unchanged. An exact name already used by another Kaiju is rejected.
Eloquent preserves `created_at` and updates `updated_at` automatically.

### Delete a kaiju

```text
Open a Kaiju detail page
→ request deletion
→ Livewire opens a permanent-deletion confirmation
→ count and display the associated incidents
→ cancel and preserve the Kaiju, or explicitly confirm
→ Eloquent deletes the selected Kaiju
→ PostgreSQL cascades deletion to its incidents
→ redirect to the catalogue with confirmation feedback
```

The warning distinguishes zero, one, and multiple incidents. The delete action
ignores calls made before confirmation, and cancellation preserves both the
Kaiju and its incidents. Deletion is currently permanent and public,
consistently with the other Kaiju workflows. Role-based authorization will be
added in its later roadmap phase.

### Create a manual incident

```text
Open the public Incident form from the Kaiju catalogue
→ select one known Kaiju
→ enter required incident details and a UTC occurrence time
→ Livewire validates the submitted state
→ Eloquent creates the Incident through the Kaiju relationship
→ redirect to that Kaiju's detail page with confirmation feedback
```

Opening the same form from a Kaiju detail page adds its identifier to the URL
and preselects that Kaiju while leaving the selector editable. The submitted
identifier is still validated. If no Kaijus exist, the page explains the
dependency and links to Kaiju registration instead of showing an unusable form.

### Browse the incident catalogue

```text
Open the public Incident catalogue
→ Livewire restores search, filters, ordering, and page from the URL
→ optionally search title or location
→ optionally filter by status and Kaiju
→ order incidents by newest or oldest occurrence
→ Eloquent combines the criteria and eager loads matching Kaijus
→ display status, location, UTC occurrence time, and linked Kaiju
→ navigate between pages without a full browser reload
```

The page size comes from KERS configuration and defaults to nine. The
catalogue shows an explicit empty state when no incidents have been recorded.
When active criteria have no matches, it instead explains that the catalogue
is filtered and lets the user clear all criteria. Changing search, filters, or
ordering resets pagination while navigation retains the active criteria.
Each catalogue card links to its incident detail page and keeps the associated
Kaiju available as a separate navigation target.

### View an incident

```text
Follow an Incident detail link
→ Laravel resolves the Incident route parameter
→ Livewire eager loads its associated Kaiju
→ display the current details, status, and timestamps in UTC
→ navigate to the Kaiju or back to the Incident catalogue
```

An unknown Incident identifier returns Laravel's standard 404 response. The
detail page links to the current editing workflow. Deletion is introduced in a
later roadmap item.

### Edit an incident and change its status

```text
Open an Incident detail page
→ follow the edit link
→ Laravel resolves the Incident route parameter
→ Livewire preloads its fields and current Kaiju
→ change the recorded details, Kaiju, or status
→ Livewire validates the submitted state
→ convert the occurrence time explicitly to UTC
→ Eloquent updates the Incident
→ redirect to the detail page with confirmation feedback
```

The current workflow permits transitions between any of the `open`,
`contained`, and `closed` statuses. Invalid fields, unsupported statuses, and
unknown Kaijus do not update the record. Cancelling returns to the detail page
without saving.

### Delete an incident

```text
Open an Incident detail page
→ request deletion
→ Livewire opens a permanent-deletion confirmation
→ cancel and preserve the Incident, or explicitly confirm
→ Eloquent deletes only the selected Incident
→ keep its associated Kaiju and other Incidents
→ redirect to the Incident catalogue with confirmation feedback
```

The delete action ignores calls made before confirmation. Deletion is currently
permanent and public, consistently with the other Incident workflows.
Role-based authorization will be added in its later roadmap phase.

The following authentication workflows remain as a technical foundation. They
are not exposed in the current navigation and are not yet the final KERS role
and authorization model.

### Register and authenticate

```text
Open registration
→ submit name, email, and password
→ Fortify validates the request
→ create the user through CreateNewUser
→ authenticate the new user
→ redirect to the Kaiju catalogue
```

Existing users submit the login form, Fortify validates their credentials, and
successful authentication redirects to the Kaiju catalogue. Logout invalidates
the session and redirects through the root URL to the catalogue.

### Reset a password

```text
Request a password-reset link
→ Laravel sends a reset notification
→ open the tokenized reset route
→ submit and confirm a new password
→ Fortify resets the password
→ redirect to login
```

Local mail uses the configured log driver unless another mailer is selected.

## Planned product workflows

The following workflows are requirements, not implemented behavior.

### Change incident status

```text
Open an incident
→ choose open, contained, or closed
→ validate the status
→ persist the change
→ recalculate whether assigned-team capacity is consumed
```

### Assign a response team

```text
Open an incident
→ review eligible teams and workload
→ select, replace, or remove one team
→ validate active capacity
→ persist the assignment
→ show updated workload
```

### Create incidents from USGS events

```text
Request recent USGS events
→ Laravel HTTP client fetches and maps current results
→ display results without persistence
→ select one or more events
→ select an existing kaiju
→ reject duplicate source identifiers
→ create one incident per accepted event
```

Automated tests replace the external HTTP request with Laravel HTTP fakes.

### Apply role authorization

```text
Authenticate a user
→ middleware protects selected routes
→ policy evaluates operator or admin capability
→ Livewire action repeats the authorization check
→ allow the action or return a forbidden response
```

These workflows become current only after their roadmap pull requests are
merged.
