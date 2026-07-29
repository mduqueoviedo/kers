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
→ Livewire queries known kaijus through Eloquent
→ display an empty state or the ordered catalogue
→ show each category, threat level, and description
```

No authentication is required.

The following authentication workflows come from the starter scaffold. They
are not yet the final KERS role and authorization model.

### Register and authenticate

```text
Open registration
→ submit name, email, and password
→ Fortify validates the request
→ create the user through CreateNewUser
→ authenticate the new user
→ redirect to the dashboard
```

Existing users submit the login form, Fortify validates their credentials, and
successful authentication redirects to the dashboard. Logout invalidates the
session and redirects to the public home page.

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

### Update profile and security settings

```text
Open authenticated settings
→ Livewire initializes state from the current user
→ submit profile or password changes
→ Livewire validates input
→ Eloquent persists the user
→ render validation or success feedback
```

Account deletion requires the current password, deletes the user, logs out the
session, and redirects home.

## Planned product workflows

The following workflows are requirements, not implemented behavior.

### Create a kaiju

```text
Open the kaiju form
→ enter identity, category, threat level, and optional description
→ Livewire validates input
→ Eloquent stores the kaiju
→ show the created record or catalogue
```

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

### Delete a kaiju and its incidents

```text
Request kaiju deletion
→ count associated incidents
→ display the permanent cascade warning
→ explicitly confirm
→ delete the kaiju
→ PostgreSQL cascades deletion to its incidents
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
