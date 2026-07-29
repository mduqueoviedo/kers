# Roadmap

## How to read this roadmap

Each item is intended to produce one focused pull request. A phase describes
sequence, not permission to implement every item.

Statuses are `Planned`, `Ready`, `In progress`, `In review`, `Completed`,
`Deferred`, and `Optional`. An item becomes `Completed` only after its pull
request is merged into `main`.

For each table, the section supplies the phase. Combined columns retain the
required objective, user value, Laravel concepts, expected scope, acceptance
criteria, testing expectations, dependencies, priority, and notes.

## Phase 1 — Project foundations and documentation

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| FND-001 — Stabilize the starter scaffold | Completed | Establish trustworthy checks before domain work. | Pest, factories, Pint, PHPStan; remove inactive two-factor placeholders. | Tests contain no empty or risky cases; Pest, Pint, and PHPStan pass. | None; High; merged in PR #1. |
| FND-002 — Configure PostgreSQL foundations | Completed | Use the production database behavior from the start and document setup. | Environment configuration, PostgreSQL connections, migrations, CI services, README. | Development and test databases are separate; migrations, Pest, Pint, PHPStan, and frontend build pass. | FND-001; High; merged in PR #2. |
| FND-003 — Establish project documentation | Completed | Give reviewers a durable product, technical, roadmap, architecture, and workflow baseline. | Markdown documentation and roadmap planning; no runtime changes. | All required documents exist, links resolve, and content distinguishes current from planned behavior; existing checks pass. | FND-002; High; merged in PR #3. |
| FND-004 — Remove unused starter scaffolding | Completed | Reduce generated noise while preserving all active application behavior. | Remove unreferenced layout variants, their isolated icon, and placeholder test scaffolding; align test configuration and documentation. | Routes and visible behavior remain unchanged; Blade views compile; meaningful tests, Pint, PHPStan, and frontend build pass. | FND-003; Medium; merged in PR #4. |

## Phase 2 — Kaiju management

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| KAI-001 — Add the Kaiju model | Completed | Persist known kaijus with valid categories and threat levels. | PHP enum, migration, Eloquent model, casts, constraints. | Fields and enum cast work; threat level is constrained to 1–5; migration rollback and database behavior are tested. | FND-004; High; merged in PR #5. |
| KAI-002 — Add kaiju factory data | Completed | Provide representative records for development and tests. | Factory states and a focused seeder. | Factory produces valid categories and threat levels; seeding is repeatable and tested where meaningful. | KAI-001; Medium; merged in PR #7. |
| KAI-003A — List kaijus publicly | Completed | Let anyone review the known-creature catalogue. | Public Livewire route, single-file list component, Eloquent query, empty state, guest-safe navigation. | Guests can view empty and populated catalogues; records are ordered and display their current fields; authenticated navigation still works. | KAI-002; High; merged in PR #8. |
| KAI-003B — Simplify the public application layout | Completed | Show only implemented KERS functionality and remove confusing starter UI. | Root redirect, shared Blade layout, route and view cleanup. | The root redirects to the catalogue; its layout has simple KERS navigation; generated welcome, dashboard, settings, sidebar, and related dead components are removed; tests and frontend build pass. | KAI-003A; Medium; merged in PR #9. |
| KAI-003C — Create kaijus | In review | Let anyone register a known creature. | Single-file Livewire form, validation, enum and uniqueness rules, Eloquent creation, redirect and feedback. | Valid records persist and appear in the catalogue; required, unique-name, enum, threat-level, and optional-description cases are tested. | KAI-003B; High; this pull request; simple UI only. |
| KAI-004 — Paginate and view kaijus | Planned | Keep the catalogue usable and expose individual histories later. | Livewire pagination and route model binding or equivalent binding. | Lists paginate and detail pages render the correct kaiju; feature and Livewire tests pass. | KAI-003C; High. |
| KAI-005 — Edit kaijus | Planned | Correct an existing kaiju's details. | Model binding, Livewire form state, update validation. | Valid edits persist; invalid category and threat levels do not; tested through observable behavior. | KAI-004; High. |
| KAI-006 — Delete kaijus safely | Planned | Remove a kaiju only after explicit confirmation. | Livewire confirmation state and Eloquent deletion. | Cancellation preserves the record and confirmation deletes only the selected kaiju; Livewire and database tests pass. | KAI-005; High; incident count and cascade warning are completed after incidents exist. |
| KAI-007 — Search and filter kaijus | Planned | Find kaijus by name, category, and threat level. | Query building and Livewire query-string synchronization. | Search and filters combine correctly, reset predictably, and retain pagination validity; Livewire tests cover combinations. | KAI-004; Medium. |

## Phase 3 — Incident management

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| INC-001 — Add the Incident model | Planned | Persist incidents belonging to known kaijus. | Status enum, migration, foreign key, cascade, Eloquent relationships, date cast. | Required fields, casts, relationship, and database cascade work; PostgreSQL tests verify constraints. | KAI-001; High. |
| INC-002 — Add incident factory data | Planned | Supply realistic incident histories for tests and development. | Related factories and seeding. | Factory creates valid dated incidents linked to kaijus; representative statuses are covered. | INC-001, KAI-002; Medium. |
| INC-003 — Create incidents | Planned | Record a manual incident for an existing kaiju. | Livewire forms, select validation, relationship persistence. | Valid incidents persist with a kaiju and date; missing or invalid data is rejected; Livewire and database assertions pass. | INC-002; High. |
| INC-004 — List, paginate, and view incidents | Planned | Review incidents and their associated kaijus. | Eager loading, pagination, details, date formatting. | Lists avoid relationship query repetition, paginate, and link to correct details; feature tests pass. | INC-003; High. |
| INC-005 — Edit incidents and change status | Planned | Correct incident data and manage its lifecycle. | Enum validation, Livewire update actions, date casting. | Edits and valid status transitions persist; invalid values fail; observable behavior is tested. | INC-004; High. |
| INC-006 — Delete incidents safely | Planned | Delete an incident only after confirmation. | Confirmation state and Eloquent deletion. | Cancellation preserves data and confirmation deletes only the selected incident; Livewire and database tests pass. | INC-005; High. |
| INC-007 — Search, filter, and order incidents | Planned | Locate relevant incidents efficiently. | Conditional queries, query strings, and occurrence ordering. | Title/location search, status/kaiju filters, and ordering combine correctly; tests cover representative combinations. | INC-004; Medium. |
| INC-008 — Show kaiju history and cascade warning | Planned | Explain a kaiju's incident history and the impact of deletion. | Eager loading, relationship counts, database cascade. | Kaiju details show incidents; deletion warning gives the exact incident count; PostgreSQL tests prove cascade deletion. | INC-007, KAI-006; High. |

## Phase 4 — Response team management

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| TEM-001 — Add the ResponseTeam model | Planned | Persist teams with controlled capacity. | Migration, Eloquent model, default values, database constraints. | Capacity defaults to 1 and is limited to 1–5; migration and model behavior are tested. | FND-003; High. |
| TEM-002 — Add response-team factory data | Planned | Provide representative teams for development and tests. | Factory states and seeding. | Valid capacity variants are produced and seeding is repeatable. | TEM-001; Medium. |
| TEM-003 — Create and list response teams | Planned | Let users register teams and review available capacity. | Livewire forms, validation, Eloquent listing. | Valid teams persist and display; code/name/capacity validation and empty state are tested. | TEM-002; High. |
| TEM-004 — View and edit response teams | Planned | Review and correct an existing team's details. | Detail component, model binding, form state, update validation. | Correct details render, valid edits persist, and invalid capacity fails; feature and Livewire tests pass. | TEM-003; High. |
| TEM-005 — Delete and search response teams | Planned | Remove teams safely and find teams when the list grows. | Confirmation state and conditional queries. | Deletion requires confirmation and search returns matching teams; Livewire and database tests pass. | TEM-004; Medium; assignment deletion behavior is defined with the relationship in Phase 5. |

## Phase 5 — Incident assignment and team capacity

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| ASN-001 — Relate incidents to response teams | Planned | Allow an incident to reference zero or one team. | Nullable foreign key, Eloquent relationships, eager loading. | Assignment is optional and relationships resolve in both directions; migration and database tests pass. | INC-001, TEM-001; High. |
| ASN-002 — Assign, change, and remove a team | Planned | Let operators manage the team responding to an incident. | Livewire actions, relationship updates, validation. | Users can assign, replace, and remove a team; invalid identifiers fail; Livewire tests pass. | ASN-001, INC-005; High. |
| ASN-003 — Enforce team capacity | Planned | Prevent overcommitting active response teams. | Aggregate queries, business validation, transactions where needed. | Open and contained incidents consume capacity, closed incidents do not, and over-capacity assignments fail; edge cases are tested. | ASN-002; High. |
| ASN-004 — Display workload and release capacity | Planned | Make assignment decisions understandable and reflect incident closure. | Relationship counts and derived UI state. | Team workload is visible and closing, reopening, or reassigning incidents updates availability correctly; regression tests cover transitions. | ASN-003; High. |

## Phase 6 — USGS integration

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| USG-001 — Configure and call USGS | Planned | Retrieve recent earthquake events through one focused client. | Configuration, environment variables, Laravel HTTP client, timeouts. | Current events are fetched without persistence and failures are represented predictably; HTTP fakes cover success and failure. | INC-004; High. |
| USG-002 — Map and display recent events | Planned | Present external data independently of its raw payload shape. | Response mapping and a simple Livewire list. | Required event fields render and no local event records are created; mapper and Livewire tests use fakes. | USG-001; High. |
| USG-003 — Add incident source fields | Planned | Store only the source details needed by imported incidents. | Nullable migration fields, casts, composite uniqueness. | Manual incidents retain null source data and duplicate USGS identifiers are rejected by PostgreSQL; migration tests pass. | USG-002; High. |
| USG-004 — Create one incident from one event | Planned | Convert a selected event into an editable incident for a chosen kaiju. | Livewire selection, mapping, Eloquent creation. | Generated title, location, time, coordinates, magnitude, depth, URL, and source persist correctly; HTTP and database tests pass. | USG-003, INC-003; High. |
| USG-005 — Prevent duplicate imports | Planned | Give clear feedback instead of creating the same source incident twice. | Database exception handling and validation feedback. | Re-import is prevented under concurrent-safe uniqueness and produces understandable feedback; regression tests pass. | USG-004; High. |
| USG-006 — Import multiple selected events | Planned | Create several controlled incidents in one action. | Multi-selection, transaction, partial-failure decision. | Each unique selection creates one incident and duplicate/error behavior is explicit; transaction and Livewire tests cover mixed selections. | USG-005; Medium. |

## Phase 7 — Authentication and authorization

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| AUT-001 — Require authentication | Planned | Associate operational actions with authenticated users. | Fortify routes, authentication and verification middleware. | Selected application routes require login while registration remains public; feature tests cover guest and user access. | USG-006; High. |
| AUT-002 — Add roles and default operators | Planned | Establish the simple operator/admin model. | PHP enum or equivalent cast, user migration, registration action, seed data. | New users become operators and a local admin can be seeded; database and registration tests pass. | AUT-001; High. |
| AUT-003 — Authorize kaiju and incident actions | Planned | Restrict destructive and catalogue actions by role. | Policies, Livewire authorization, forbidden responses. | Operators and admins receive exactly the documented capabilities; policy and Livewire tests cover allowed and forbidden paths. | AUT-002, KAI-006, INC-006; High. |
| AUT-004 — Authorize team and assignment actions | Planned | Apply roles consistently to team management and assignments. | Policies and Livewire authorization checks. | Operators may assign teams but only admins manage team records; authorization tests pass. | AUT-003, ASN-004; High. |
| AUT-005 — Verify the complete access model | Planned | Close authorization gaps across routes, navigation, and actions. | Middleware/policy integration and UI visibility. | Direct requests and Livewire calls cannot bypass policy decisions; email verification behavior is tested where enabled. | AUT-004; High. |

## Phase 8 — Dashboard and visual refinement

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| UI-001 — Add operational dashboard summaries | Planned | Provide a concise view of kaijus, incident status, and team capacity. | Eloquent aggregates and derived Livewire state. | Counts and recent records are accurate without N+1 queries; feature and database tests pass. | AUT-005; Medium. |
| UI-002 — Improve navigation and shared states | Planned | Make completed workflows coherent and accessible. | Reusable Blade/Livewire components, empty/loading/success states. | Navigation reflects authorization and common states are consistent, responsive, and keyboard accessible; relevant rendering tests pass. | UI-001; Medium. |
| UI-003 — Apply restrained visual refinement | Planned | Give KERS a readable command-centre identity. | Tailwind composition, responsive and dark-mode review. | Core pages are visually consistent and accessible without complex charts; frontend build and manual responsive review pass. | UI-002; Low. |

## Phase 9 — Deployment

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| DEP-001 — Select a hosting provider | Planned | Choose a current, affordable, portable Laravel/PostgreSQL target. | Operational requirements and provider evaluation. | Cost, Laravel support, PostgreSQL, logs, runtime processes, and demo reliability are compared and the decision is documented. | UI-003; Low; requires current research. |
| DEP-002 — Configure and deploy KERS | Planned | Produce a repeatable production deployment. | Production environment, build/start commands, migrations, optional Docker if justified. | Secrets are external, assets build, PostgreSQL migrations run safely, and the deployed application opens reliably. | DEP-001; Low. |
| DEP-003 — Add operational documentation | Planned | Make the demo supportable and recoverable. | Health checks, logs, deployment and rollback workflow. | Health endpoint, log access, deployment verification, and rollback steps are documented and tested. | DEP-002; Low. |

## Optional work

Static-analysis expansion, queues, scheduling, events, notifications, incident
timelines, and multiple response teams remain `Optional`. Each requires a real
product need and a separate approved roadmap proposal.
