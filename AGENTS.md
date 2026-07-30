# KERS — Project Instructions for Codex

## 1. Project context

This repository contains **KERS: Kaiju Emergency Response System**, a Laravel application used as a structured learning project.

The application manages fictional kaijus, incidents caused by them, and response teams. It will later integrate with the public USGS earthquake API so users can review real seismic events and create fictional kaiju incidents from selected events.

The fictional theme is intentional, but the application must be developed as a serious, maintainable Laravel project.

The project already uses:

* Laravel
* PHP 8.5
* Livewire
* Single-file Livewire components
* Tailwind CSS
* Pest
* PostgreSQL
* The Laravel starter kit
* Email verification and registration support

Do not replace these choices unless explicitly instructed.

---

## 2. Primary objective

The primary purpose of this project is **learning Laravel and its ecosystem incrementally**.

Do not optimize for completing the entire application as quickly as possible.

Optimize for:

1. Clear Laravel learning value.
2. Small, understandable changes.
3. Idiomatic Laravel code.
4. Easy manual pull request review.
5. Explicit understanding of what the framework is doing.
6. Gradual introduction of new Laravel concepts.
7. A working application after every merged iteration.

The project owner has extensive software engineering experience, especially with TypeScript, React, Next.js, Node.js and NestJS, but has very limited recent PHP experience and is new to Laravel.

Avoid treating the owner as a junior software engineer. Explain Laravel-specific conventions and PHP details that may not be obvious, but do not over-explain general software engineering concepts.

---

## 3. Language requirements

Everything stored in the repository must be written in English:

* Source code
* Class names
* Method names
* Variable names
* Database names and fields
* Comments
* Documentation
* Branch names
* Commit messages
* Pull request titles
* Pull request descriptions
* Test descriptions
* User-facing interface copy
* Technical explanations produced during development

Conversation with the project owner may follow the language used by the owner, but all reusable project content must remain in English.

---

## 4. Core working principle

Use the following rule throughout the project:

> One small feature, one focused branch, one reviewable pull request, and one primary Laravel learning objective.

A pull request may introduce one or two closely related Laravel concepts, but it must not become a large package of unrelated changes.

Each pull request must be small enough for the project owner to review, understand and explain.

As a soft guideline, prefer fewer than approximately 300 relevant changed lines per pull request, excluding:

* Generated files
* Lockfiles
* Pure formatting changes
* Mechanical framework-generated code

This is not a strict numerical limit. Clarity and reviewability are more important than line count.

If an implementation starts touching too many files, introducing several independent workflows, or requiring multiple new framework concepts, stop and propose a smaller breakdown.

---

## 5. Mandatory approval before implementation

Before implementing any roadmap item, present a concise proposal containing:

* Objective
* Laravel learning objective
* Expected files or areas
* Acceptance criteria
* Tests
* Risks or required split, only when relevant

Keep the proposal to one concise sentence or a few short bullets per section.
Do not repeat general project rules already documented elsewhere.

Wait for explicit approval before:

* Creating the feature branch
* Modifying code
* Installing dependencies
* Changing the roadmap
* Starting implementation

Do not make implementation changes while preparing the proposal.

Approval of the feature proposal also approves the complete workflow for the
approved scope: creating the dedicated branch, implementing the change,
committing it, pushing the branch, and opening the pull request. After
approval, do not pause to ask whether to commit, push, or create the pull
request. Material scope changes still require new approval.

Small implementation details may evolve after approval, but material changes to scope must be discussed first.

---

## 6. Handling features that are too large

When a roadmap item is too large for a focused pull request, do not split it silently.

Propose a sequence of smaller iterations first.

For each proposed pull request, explain:

* Its functional boundary
* Its Laravel learning objective
* Its dependencies
* Why the split improves reviewability

Wait for approval before updating the roadmap or implementing the first part.

The roadmap is a living guide, not an inflexible contract. It may evolve when gaps, dependencies or better learning opportunities are discovered, but significant changes must always be explained and approved.

---

## 7. Git and pull request workflow

Codex may perform all normal development actions except merging pull requests.

For every approved feature:

1. Confirm that local `main` is up to date.
2. Create a dedicated branch.
3. Implement only the approved scope.
4. Add or update tests.
5. Run all required quality checks.
6. Update documentation where relevant.
7. Update the roadmap status where appropriate.
8. Create clear commits.
9. Push the branch.
10. Open a pull request against `main`, ready for review.
11. Stop.

The project owner will manually review and merge pull requests from GitHub using **Squash and merge**.

This workflow is automatic after explicit feature-proposal approval. Codex
must not create a draft pull request unless explicitly requested. If a blocker
prevents committing, pushing, or opening the pull request, report the blocker
clearly instead of silently stopping.

Codex must never:

* Merge a pull request; the project owner performs the manual review and
  squash-merge
* Push directly to `main`
* Force-push without explicit permission
* Rewrite shared Git history
* Delete remote branches without explicit permission
* Start the next roadmap item automatically
* Bundle unrequested cleanup into a feature
* Perform destructive Git operations without explicit permission

After opening the ready-for-review pull request, stop and wait for further
instructions. Do not start the next roadmap item automatically.

### Branch naming

Use simple names such as:

```text
feature/list-kaijus
feature/create-incident
feature/filter-incidents
feature/assign-response-team
feature/import-usgs-events
fix/prevent-duplicate-usgs-incidents
chore/add-ci
docs/update-roadmap
```

### Commit messages

Use short, clear, imperative English commit messages.

Examples:

```text
Add kaiju creation form
Validate kaiju threat level
Test incident deletion
Document USGS import workflow
```

---

## 8. Pull request requirements

Every pull request description must contain:

### Summary

A concise explanation of the functional change.

### Laravel concepts introduced

List the relevant Laravel or Livewire concepts used.

### Main changes

Summarize the important files and behavior.

### Testing

Explain:

* Which behaviors are tested
* Which relevant cases are not covered, if any
* Why any case was intentionally omitted
* Which commands were executed
* Whether all commands passed

### Review guide

Identify:

* The files most worth reviewing
* The main request or data flow
* Important Laravel conventions used
* Any code that deserves special attention
* Questions the owner should be able to answer after reviewing the PR

Avoid repeating the same explanation under Summary, Main changes and Review
guide. Mention only relevant Laravel concepts and focus the review guide on
important files and the main execution flow.

### Documentation

State which documentation was updated, or explain why no documentation change was needed.

### Roadmap

Reference the roadmap item and its resulting status.

---

## 9. Testing rules

Tests are mandatory from the beginning.

A functional change is not complete unless its expected behavior is covered by automated tests in the same pull request.

Use **Pest** as the primary testing syntax.

Prefer:

* Feature tests for routes, pages, forms and complete application behavior
* Livewire tests for component state, actions, rendering and validation
* Database assertions for persistence and relationships
* Unit tests only for genuinely isolated business logic
* HTTP fakes for USGS integration
* Regression tests for every bug fix

Tests should verify observable behavior and business rules rather than Laravel internals or implementation details.

Do not pursue 100% coverage as an objective. Instead, ensure that every important acceptance criterion has meaningful test coverage.

During implementation, run the smallest relevant test file or filtered test
set. Before opening a pull request, run the complete suite:

```bash
php artisan test
```

Targeted test runs do not replace final full validation. Every pull request must
include the tests relevant to its scope.

---

## 10. Quality checks

During implementation, run targeted checks relevant to the modified files when
supported. Do not repeatedly run complete checks after every small edit.

Before opening a pull request, run the complete checks required by the project.

At minimum:

```bash
php artisan test
vendor/bin/pint --test
```

When frontend assets are affected, also run the appropriate frontend build or validation command.

A simple GitHub Actions workflow should validate pull requests. CI is supporting infrastructure, not a Laravel learning objective.

The workflow should remain straightforward and execute the relevant steps, such as:

* Install PHP dependencies
* Install frontend dependencies where necessary
* Configure the test environment
* Prepare PostgreSQL
* Run tests
* Run Laravel Pint
* Build frontend assets where relevant

Do not build an unnecessarily complicated CI matrix.

---

## 11. Static analysis

Larastan/PHPStan is not an initial requirement.

It may be added later as an optional quality-focused iteration after the application and PHP codebase are stable enough for the results to be educational rather than noisy.

Do not install or configure it unless explicitly requested.

---

## 12. Dependency policy

Do not install a new Composer, npm or other third-party dependency without explicit approval.

Before proposing a dependency, explain:

* What problem it solves
* Why Laravel, Livewire, PHP or the current toolchain cannot reasonably solve it
* What complexity it adds
* Its maintenance implications
* The available alternative without the dependency

Do not execute commands such as these without approval:

```bash
composer require ...
npm install ...
pnpm add ...
yarn add ...
```

Prefer built-in Laravel and PHP features whenever reasonable.

---

## 13. Architecture principles

Start with a conventional Laravel architecture.

Prefer native Laravel concepts and conventions before custom abstractions.

Use appropriate framework features such as:

* Eloquent models and relationships
* Migrations
* Database constraints
* Enums and casts
* Form validation
* Livewire components
* Policies
* Middleware
* HTTP client
* Configuration and environment variables
* Factories and seeders

Do not introduce these by default:

* Repository patterns
* Custom DTO frameworks
* Domain layers
* Interfaces for every service
* Action-class systems
* Generic service abstractions
* Event-driven architecture without a real need
* Premature scalability abstractions

Do not create architecture merely because it may be useful in the future.

Extract logic only when the current implementation has a clear responsibility or complexity problem.

Use framework conventions in an idiomatic and understandable way.

---

## 14. UI and Livewire principles

The user interface must evolve gradually.

The first versions should be simple and functional. Do not generate a polished control center or a complex design system before the underlying Livewire concepts have been introduced and reviewed.

Use:

* Single-file Livewire components
* Tailwind CSS
* Simple forms
* Basic lists and detail pages
* Clear validation feedback
* Simple empty states
* Simple confirmation messages
* Accessible controls

The intended progression is:

```text
Basic pages and navigation
→ Basic Livewire lists
→ Forms and validation
→ Pagination
→ Search and reactive filters
→ Reusable components
→ User feedback states
→ Dashboard
→ Final visual refinement
```

Do not allow visual work to overshadow Laravel and Livewire learning objectives.

---

## 15. Database requirements

Use PostgreSQL for:

* Local development
* Automated testing
* Deployment

Do not silently use SQLite as a fallback.

Avoid relying on SQLite-specific behavior.

Use:

* Proper foreign keys
* Database constraints
* Appropriate indexes
* Reversible migrations
* Factories and seeders
* A separate test database where appropriate

Do not add irreversible or destructive migrations without clearly explaining the impact.

---

## 16. Documentation structure

Use the following documentation structure:

```text
README.md
docs/
├── product-requirements.md
├── technical-requirements.md
├── roadmap.md
├── architecture.md
└── workflows.md
```

### README.md

Keep the README concise.

It should include:

* Project name
* Brief project description
* Main technology stack
* Requirements
* Installation instructions
* Environment configuration
* Database setup
* How to start the project
* How to run tests
* How to run formatting checks
* Links to the documents in `/docs`

Do not turn the README into a complete technical specification.

### `docs/product-requirements.md`

Include:

* Product purpose
* Main concepts
* Main user flows
* Entities and relationships
* Functional rules
* Scope and non-goals

### `docs/technical-requirements.md`

Include:

* Technology choices
* Language rules
* Git and PR workflow
* Testing requirements
* Architecture principles
* Dependency policy
* Quality requirements
* Portability requirements

### `docs/roadmap.md`

Each roadmap item should contain:

* Identifier
* Title
* Phase
* Status
* Functional objective
* User value
* Laravel concepts
* Expected scope
* Acceptance criteria
* Testing expectations
* Dependencies
* Priority
* Notes where necessary

Suggested statuses:

```text
Planned
Ready
In progress
In review
Completed
Deferred
Optional
```

A feature should not be marked `Completed` until it has been merged into `main`.

Since the owner performs the merge manually, a pull request may move an item to `In review`. Completion can be reflected after the merge, normally during preparation of the next iteration or through a dedicated documentation update.

### `docs/architecture.md`

Document only architecture that actually exists.

Do not describe speculative future architecture as though it were implemented.

Include important decisions such as:

* Why Livewire was selected
* Why PostgreSQL is used
* How entities currently relate
* How external USGS data is adapted
* How external event duplication is prevented
* How authorization is structured
* How deployment portability is maintained

### `docs/workflows.md`

Summarize important workflows without documenting every screen, route or method.

Examples:

```text
Create a kaiju
Create a manual incident
Change incident status
Assign a response team
Delete a kaiju and its incidents
Create incidents from USGS events
Register and authenticate a user
```

Keep workflows concise and update them only when meaningful product flows change.

Do not create a separate long document for every feature.

---

## 17. Product domain

### 17.1 Kaiju

A `Kaiju` represents a known creature with its own identity and history.

Initial fields:

```text
name
category
threat_level
description
timestamps
```

`category` must be a PHP enum with these initial values:

```text
aquatic
terrestrial
aerial
amphibious
unknown
```

`threat_level` must be an integer from `1` to `5`.

`description` may be optional.

A kaiju may have multiple incidents.

Initial relationship:

```text
Kaiju 1 ─── N Incidents
```

Kaijus must support:

* Creation
* Listing
* Detail view
* Editing
* Deletion
* Pagination
* Search
* Filtering

Deleting a kaiju must also delete all associated incidents through a database-level cascade.

Before deletion, the interface must display an explicit warning and include the number of incidents that will also be permanently deleted.

Example warning:

```text
This kaiju has 3 associated incidents. Deleting it will also permanently delete those incidents.
```

### 17.2 Incident

An `Incident` represents a specific appearance or event involving a kaiju.

Initial fields:

```text
title
description
location
status
occurred_at
kaiju_id
timestamps
```

Every initial incident must belong to a known kaiju. Do not introduce an unidentified or unknown-kaiju workflow in the core roadmap.

`status` must be a PHP enum with these values:

```text
open
contained
closed
```

`location` should initially be free text rather than a separate entity.

Incidents must support:

* Creation
* Listing
* Detail view
* Editing
* Deletion
* Status changes
* Pagination
* Search
* Filtering

Incident deletion requires explicit confirmation.

The first implementation should not include:

* Incident timelines
* Multiple response teams
* Unknown kaijus
* Automated risk processing
* Notifications
* Scheduled processing

### 17.3 ResponseTeam

A `ResponseTeam` represents a team that may be assigned to incidents.

Initial fields:

```text
name
code
capacity
description
timestamps
```

`capacity` must:

* Be an integer from `1` to `5`
* Default to `1`

A response team may be associated with many incidents over time.

An incident may have zero or one response team.

Relationship:

```text
ResponseTeam 1 ─── N Incidents
Incident N ─── 0..1 ResponseTeam
```

A response team cannot be assigned to more non-closed incidents than its configured capacity.

Both `open` and `contained` incidents consume capacity.

A `closed` incident does not consume capacity.

The first response team implementation should not include:

* Specializations
* Team status
* Availability calendars
* Many-to-many assignment
* Personnel management

---

## 18. External USGS integration

The application will later integrate with the public USGS earthquake API.

The manual kaiju and incident prototype must be completed before external API integration begins.

The existing application domain must determine how external data is adapted. Do not design the initial domain around the USGS response format.

### Functional flow

```text
Request recent seismic events from USGS
→ Display current results without persisting them
→ Allow selecting one or more events
→ Select one existing kaiju
→ Create one incident for each selected event
```

USGS seismic events must not be stored as a local entity.

Always retrieve the current data from USGS on demand.

When creating an incident from a selected USGS event, store only the relevant source information on the incident.

Additional nullable incident fields:

```text
source
external_event_id
external_url
magnitude
latitude
longitude
depth
```

For incidents created manually, these fields remain null.

For incidents created from USGS:

* `source` identifies USGS
* `external_event_id` stores the USGS event identifier
* `external_url` stores the event detail URL
* `magnitude` stores the reported magnitude
* `latitude` and `longitude` store coordinates
* `depth` stores the reported depth
* `title` is generated from the event
* `location` is generated from the event
* `occurred_at` is generated from the event timestamp

Generated incident fields may be edited later through the normal incident edit flow.

Prevent creating duplicate incidents from the same USGS event.

Use an appropriate database uniqueness rule involving the source and external event identifier while still allowing manual incidents with null source information.

Use the Laravel HTTP client and HTTP fakes in tests.

Do not make real USGS requests during automated tests.

---

## 19. Authentication and authorization

Authentication must be introduced later in the roadmap, after the main public functionality exists.

Do not protect the initial application routes merely because the starter kit already contains authentication.

When authentication is introduced:

* Registration remains public and simple.
* New users automatically receive the `operator` role.
* `admin` users are created or assigned through a seeder or direct database update.
* Do not build a user management interface in the core roadmap.
* Do not install a third-party permissions package.

Initial roles:

```text
operator
admin
```

### Operator capabilities

An operator may:

* View kaijus
* View incidents
* View response teams
* Create incidents
* Edit incidents
* Change incident status
* Assign and remove response teams
* Query USGS
* Create incidents from selected USGS events

### Admin capabilities

An admin may do everything an operator can, plus:

* Create kaijus
* Edit kaijus
* Delete kaijus
* Create response teams
* Edit response teams
* Delete response teams
* Delete incidents

Use Laravel authorization features such as:

* Authentication middleware
* Policies
* Gates where genuinely appropriate
* Livewire authorization checks
* Route protection

Do not create configurable permission matrices.

---

## 20. Dashboard and visual refinement

Visual refinement should happen after the core application, USGS integration and authentication are substantially complete.

Add a simple dashboard rather than an advanced analytics system.

Possible dashboard information:

* Total number of kaijus
* Number of open incidents
* Number of contained incidents
* Number of closed incidents
* Response teams currently at capacity
* Response teams with remaining capacity
* Recent incidents
* Highest-threat kaijus

Use simple aggregate queries and clear Tailwind components.

Do not add complex charts unless explicitly approved.

The final visual style may suggest an emergency command center, but it must remain readable, restrained and maintainable.

---

## 21. Deployment principles

An initial Railway deployment is the next high-priority checkpoint after the
minimal Incident creation flow. It must be completed before lower-priority
product work so that production findings can influence later development.

This deployment is a temporary, production-like environment for a technical
demo. It only needs to expose the same application behavior currently verified
in local development. It is expected to remain active for approximately one
week and to use Railway trial credit with an expected cost of zero.

The confirmed initial architecture is:

* Railway connected directly to the GitHub repository
* One Railway application service
* One Railway PostgreSQL service in the same project
* A private connection from the application to PostgreSQL
* A public Railway URL for the application
* Automatic Railway builds and deployments after merges into `main`
* GitHub Actions continuing to run tests and validation without deploying

Keep the first deployment intentionally minimal:

* Use production-safe Laravel environment variables
* Generate `APP_KEY` outside the repository
* Keep every secret out of Git
* Let Railpack install dependencies, build Vite assets, and serve Laravel
  without custom build or start commands
* Set `RAILPACK_SKIP_MIGRATIONS=true`
* Use the `railway.json` pre-deploy command to run `migrate --force` and the
  current repeatable demo seeder non-interactively
* Never reset the production database
* Treat the Railway filesystem as ephemeral
* Avoid local file uploads unless that demo limitation is explicitly accepted
* Do not add Redis, queues, workers, custom domains, external storage, advanced
  observability, or other infrastructure that the current application does not
  need

Do not add Docker unless the repository audit shows that Railway cannot deploy
the application reliably without it. Do not add paid infrastructure for this
temporary environment.

The required Railway application variables are:

```text
APP_NAME=KERS
APP_ENV=production
APP_DEBUG=false
APP_KEY=<generated Laravel key>
APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
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

The current seeders use `updateOrCreate`. They do not delete unrelated data,
but each redeployment may restore the canonical values of predefined demo
records.

After the demo, review Railway usage and stop or remove both services when they
are no longer needed. This temporary Railway decision does not select a
permanent hosting provider for a later long-lived deployment.

---

## 22. Core roadmap

The roadmap must be broken down into small pull requests. The following phases describe direction, not permission to implement everything at once.

The initial Railway deployment is a temporary priority checkpoint immediately
after Phase 3's minimal Incident creation flow and before continuing with Phase
4. Its planning documentation, minimal repository changes, Railway setup, and
verification belong to one focused pull request. The detailed and current
sequence is maintained in `docs/roadmap.md`.

### Phase 1 — Project foundations and documentation

Goals:

* Inspect the generated Laravel project
* Confirm PostgreSQL configuration
* Establish documentation structure
* Establish testing and formatting commands
* Add or verify basic GitHub Actions validation
* Record the project rules
* Create initial factories and seed strategy only when needed

Laravel learning focus:

* Laravel project structure
* Environment configuration
* Artisan
* Database configuration
* Pest
* Pint
* Basic application bootstrapping

Do not combine all foundation work into a large pull request. Propose a suitable split.

### Phase 2 — Kaiju management

Suggested progression:

1. Create the `KaijuCategory` enum.
2. Add the kaijus migration and Eloquent model.
3. Add factory and representative seed data.
4. Create and list kaijus.
5. Add pagination.
6. View kaiju details.
7. Edit kaijus.
8. Delete kaijus with confirmation.
9. Add name search.
10. Add category and threat-level filters.

Potential Laravel concepts:

* Migrations
* Eloquent models
* Enums
* Model casts
* Factories
* Seeders
* Livewire components
* Validation
* Pagination
* Query building
* URL query-string synchronization
* Database assertions

Keep each pull request focused.

### Phase 3 — Incident management

Suggested progression:

1. Create the `IncidentStatus` enum.
2. Add incidents migration and model.
3. Add the kaiju-to-incidents relationship.
4. Add incident factory data.
5. Create incidents linked to a kaiju.
6. List incidents.
7. Add pagination.
8. View incident details.
9. Edit incidents.
10. Change incident status.
11. Delete incidents with confirmation.
12. Add search by title or location.
13. Add status and kaiju filters.
14. Add ordering by occurrence date.
15. Show incident history on kaiju detail pages.

Potential Laravel concepts:

* Foreign keys
* Eloquent relationships
* Eager loading
* Form validation
* Date casting
* Enums and casts
* Route model binding where appropriate
* Livewire state
* Query scopes only where they provide clear value
* Database cascade behavior

### Phase 4 — Response team management

Suggested progression:

1. Add the response teams migration and model.
2. Add factory and seed data.
3. Create and list response teams.
4. View details.
5. Edit response teams.
6. Delete response teams.
7. Add simple search or filtering if useful.

Potential Laravel concepts:

* Additional model lifecycle
* Validation
* Factories and seeders
* Reusable Livewire patterns
* Refactoring repeated UI only after repetition exists

### Phase 5 — Incident assignment and team capacity

Suggested progression:

1. Add the optional response team relationship to incidents.
2. Assign one team to an incident.
3. Remove or change the assigned team.
4. Display team workload.
5. Prevent assignments beyond team capacity.
6. Release capacity when an incident is closed.
7. Cover reassignment and state-transition edge cases.

Potential Laravel concepts:

* Nullable foreign keys
* Relationship counts
* Business validation
* Transactions where needed
* Model queries
* Domain rules
* Feature and Livewire testing

### Phase 6 — USGS integration

Suggested progression:

1. Add USGS configuration.
2. Create a minimal USGS API client using Laravel HTTP.
3. Fetch and map recent events.
4. Display recent events without local persistence.
5. Allow selecting one event.
6. Select an existing kaiju.
7. Create one incident from one event.
8. Add required source fields to incidents.
9. Prevent duplicate incident creation.
10. Allow selecting multiple events.
11. Create multiple incidents in one controlled action.
12. Improve error handling and feedback.

Potential Laravel concepts:

* HTTP client
* Configuration
* Environment variables
* Data mapping
* HTTP fakes
* External failure handling
* Database uniqueness
* Multi-selection in Livewire
* Transactions for multi-create behavior

Do not introduce jobs or queues merely because external HTTP is involved.

### Phase 7 — Authentication and authorization

Suggested progression:

1. Activate authentication requirements for relevant routes.
2. Add the user role enum or equivalent simple representation.
3. Default new users to `operator`.
4. Add seeded admin users.
5. Define policies for kaijus.
6. Define policies for incidents.
7. Define policies for response teams.
8. Apply authorization in routes and Livewire actions.
9. Add authorization tests.
10. Verify email verification behavior only where useful.

Potential Laravel concepts:

* Fortify or starter-kit authentication
* Middleware
* Policies
* Authorization in Livewire
* User model casts
* Seeded credentials for local development
* Forbidden-response testing

### Phase 8 — Dashboard and visual refinement

Suggested progression:

1. Add a simple dashboard.
2. Add summary cards.
3. Add recent incident information.
4. Add team capacity summaries.
5. Improve navigation.
6. Improve lists and details.
7. Improve empty, loading, validation and success states.
8. Apply restrained command-center styling.
9. Review accessibility and responsive behavior.

Potential Laravel concepts:

* Aggregate Eloquent queries
* Reusable Livewire or Blade components
* Tailwind composition
* Derived UI state
* Avoiding N+1 queries

### Phase 9 — Long-term deployment follow-up

Suggested progression:

1. Review the temporary Railway deployment and demo findings.
2. Stop or remove temporary services after the demo.
3. Decide whether a long-lived deployment is needed.
4. Evaluate current hosting options only if a long-lived environment is
   requested.
5. Document any production support and rollback requirements justified by that
   environment.

The initial Railway demo deployment is not deferred to this phase.

---

## 23. Optional low-priority extensions

These features are explicitly optional.

Do not implement them unless requested after the core project is stable.

### Static analysis

* Larastan
* PHPStan
* Incremental type improvements

### Jobs and queues

Only add them if a natural asynchronous workflow emerges.

Do not invent artificial background processing merely to demonstrate queues.

### Scheduler

Only add scheduled tasks when a real product need exists.

Do not schedule USGS imports unless the product requirements change.

### Events and notifications

Only introduce these when they simplify or enable a real workflow.

Do not create event and listener layers around ordinary synchronous CRUD operations.

### Incident timeline

A possible future `IncidentEvent` or activity history model may record meaningful lifecycle changes.

This should be designed only when requested.

### Multiple response teams

A future many-to-many relationship may allow multiple teams per incident.

This is not part of the core roadmap.

---

## 24. Scope-control rules

Do not implement features merely because they appear later in the roadmap.

Only implement the specifically approved iteration.

Avoid:

* Building the complete domain model in the first migration
* Adding future fields early
* Creating generic abstractions for planned entities
* Adding authentication middleware before its phase
* Implementing response teams while building kaijus
* Adding USGS fields before the approved integration work
* Adding queues, events or notifications speculatively
* Polishing the full UI during early CRUD work
* Adding deployment infrastructure outside the approved Railway checkpoint
* Installing packages proactively
* Performing broad cleanup unrelated to the feature

When unrelated issues are discovered:

1. Mention them.
2. Explain their impact.
3. Suggest a future roadmap item if necessary.
4. Do not fix them inside the current PR unless they block the approved scope.

---

## 25. Learning-oriented completion report

After implementation and before opening the pull request, provide a concise
learning summary containing:

### Implemented

Describe the functional outcome in one or two sentences.

### Learned

Explain only the Laravel or Livewire conventions that are genuinely new or
important in this iteration.

### Flow

Include one short request/data-flow sequence, for example:

```text
Livewire action
→ validation
→ Eloquent model
→ PostgreSQL
→ component re-render
```

### Review

Identify only the most important files and summarize the relevant execution
flow.

### Questions

Provide no more than three questions the owner should be able to answer after
reviewing the pull request.

---

## 26. Current instruction

Do not start implementing the entire roadmap.

First:

1. Treat the initial Railway deployment as completed and verified at
   `https://kers-production.up.railway.app/kaijus`.
2. Do not recreate or expand the temporary infrastructure unless explicitly
   requested.
3. Preserve the minimal Railpack, PostgreSQL, trusted-proxy, healthcheck, and
   pre-deploy configuration while continuing product work.
4. Review Railway usage and stop or remove both services after the temporary
   demonstration period.
5. Use `docs/roadmap.md` to propose the next focused product iteration and wait
   for approval before implementation.

---

## 27. Codex efficiency mode

### Scope and repository inspection

* Inspect only files reasonably related to the approved scope.
* Prefer files explicitly named by the owner and the closest existing feature used as a pattern.
* Do not perform repository-wide searches, architecture reviews, or unrelated cleanup unless required.
* Do not repeatedly reopen files whose relevant contents are already available in the current conversation.
* Stop and ask before expanding into unrelated areas.

### Concise approval proposals

* Keep the mandatory pre-implementation proposal.
* Use the concise structure: Objective; Laravel learning objective; Expected files or areas; Acceptance criteria; Tests; Risks or required split, only when relevant.
* Prefer one concise sentence or a few short bullets per section.
* Do not repeat general project rules already documented elsewhere.

### Implementation communication

* Make approved changes directly.
* Avoid narrating routine exploration, commands, or obvious implementation steps.
* Report only decisions, blockers, unexpected findings, or material scope changes.
* Do not repeat information already stated in the approved proposal.

### Testing strategy

* Tests remain mandatory.
* During implementation, run the smallest relevant test file or filtered test set.
* Run formatting checks only against modified files where supported.
* Do not repeatedly run the full suite after every small change.
* Before opening the pull request, run all complete quality checks required by the project.
* Targeted test runs do not replace final full validation.

### Concise completion report

* Preserve the learning-oriented completion report using: Implemented; Learned; Flow; Review; Questions.
* Keep “Implemented” to one or two sentences.
* Include one short request/data-flow sequence.
* Identify only the most important files.
* Include no more than three manual-review questions.
* Expand only when a Laravel or Livewire convention is genuinely new or important.

### Pull request descriptions

* Preserve all currently required PR sections.
* Avoid repeating the same explanation under Summary, Main changes, and Review guide.
* Mention only relevant Laravel concepts.
* Focus the review guide on important files and execution flow.
* Include final test commands and results, but not verbose logs unless explaining a failure.

### Final response

After opening the pull request, include only:

* Pull request link
* Brief functional summary
* Final checks executed
* Main files to review
* Up to three learning questions
* Any unresolved issue

Then stop.
