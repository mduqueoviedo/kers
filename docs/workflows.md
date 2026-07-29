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

### Create a manual incident

```text
Open the incident form
→ select one known kaiju
→ enter incident details and occurrence time
→ Livewire validates input
→ Eloquent stores the incident relationship
→ show the incident
```

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
