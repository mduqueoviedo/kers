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

## Current delivery priority

The temporary Railway deployment, Incident catalogue, and protected demo-data
API are complete. INC-006 and INC-007 are in review as independent pull
requests.

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
| KAI-003C — Create kaijus | Completed | Let anyone register a known creature. | Single-file Livewire form, validation, enum and uniqueness rules, Eloquent creation, redirect and feedback. | Valid records persist and appear in the catalogue; required, unique-name, enum, threat-level, and optional-description cases are tested. | KAI-003B; High; merged in PR #10. |
| KAI-004A — Paginate kaijus | Completed | Keep the catalogue usable as its records grow. | Livewire pagination, URL page state, and an expanded deterministic seed catalogue. | Nine alphabetically ordered records render per page; navigation and direct page URLs work; 12 repeatable seed records support manual review; feature, Livewire, and seeder tests pass. | KAI-003C; High; merged in PR #11. |
| KAI-004B — View kaiju details | Completed | Let anyone inspect one known creature before incident history exists. | Livewire page route, route model binding, and catalogue-to-detail navigation. | Each catalogue record links to its correct detail page; current fields render and missing records return 404; feature and Livewire tests pass. | KAI-004A; High; merged in PR #12. |
| KAI-005 — Edit kaijus | Completed | Correct an existing kaiju's details. | Model binding, prefilled Livewire form state, update validation, and Eloquent timestamps. | Valid edits persist; the current name remains valid while another exact name is rejected; invalid category and threat levels do not persist; feature and Livewire tests pass. | KAI-004B; High; merged in PR #13. |
| KAI-006 — Delete kaijus safely | Completed | Remove a kaiju only after explicit confirmation. | Livewire confirmation state, Flux modal, and Eloquent deletion. | Opening or cancelling confirmation preserves the record; deletion without confirmation is ignored; confirmation deletes only the selected kaiju; Livewire and database tests pass. | KAI-005; High; merged in PR #14; incident count and cascade warning are completed after incidents exist. |
| KAI-007 — Search and filter kaijus | Completed | Find kaijus by name, category, and threat level. | Conditional Eloquent queries, reactive Livewire state, query-string synchronization, and pagination resets. | Case-insensitive name search and category/threat filters combine correctly, reset predictably, retain pagination validity, and load from a shared URL; feature and Livewire tests pass. | KAI-004A; Medium; merged in PR #15. |

## Phase 3 — Incident management

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| INC-001 — Add the Incident model | Completed | Persist incidents belonging to known kaijus. | Status enum, migration, foreign key, cascade, Eloquent relationships, and immutable UTC date cast. | Required fields, enum and date casts, both relationship directions, foreign-key integrity, and database cascade behavior are verified against PostgreSQL. | KAI-001; High; merged in PR #16. |
| INC-002A — Add the incident factory | Completed | Produce valid related incidents for focused tests without persistent development data. | Related Eloquent factory, automatic Kaiju creation, explicit existing-Kaiju association, lifecycle states, and UTC dates. | Default records satisfy database constraints; `for($kaiju)` reuses the selected Kaiju; open, contained, and closed states produce the requested enum; factory tests pass. | INC-001, KAI-002; Medium; merged in PR #17. |
| INC-002B — Seed incidents safely | Completed | Supply repeatable incident histories for development without hiding cascade impact. | Deterministic relationship seeding, status coverage, computed relationship count, pluralized deletion warning, and database cascade. | Repeated seeding produces exactly nine UTC incidents across all statuses; the Kaiju deletion confirmation reports zero, one, or multiple incidents before the cascade; cancellation and unrelated records remain safe. | INC-002A, KAI-006; Medium; merged in PR #18. |
| INC-003 — Create incidents | Completed | Record a manual incident for an existing kaiju from a general or preselected entry point. | Single-file Livewire form, URL-backed preselection, enum and existence validation, UTC parsing, and relationship persistence. | Valid incidents persist through the selected Kaiju relationship; catalogue access starts unselected, Kaiju-detail access preselects its record, empty dependencies and invalid fields are explained, and Livewire/database tests pass. | INC-002B; High; merged in PR #19. |
| INC-004A — List and paginate incidents | Completed | Review recorded incidents and their associated kaijus. | Public single-file Livewire catalogue, eager loading, configurable pagination, UTC date formatting, navigation, and configured domain badge colors. | Incidents appear newest first with status, location, date, and Kaiju; empty and paginated states work; relationship queries do not repeat; feature and Livewire tests pass. | INC-003; High; merged in PR #21; split from INC-004 to keep listing separate from route model binding and details. |
| INC-004B — View incident details | Completed | Inspect one recorded incident before managing it. | Livewire page route, route model binding, eager loading, configured status badge, UTC detail rendering, and catalogue-to-detail navigation. | Each catalogue record links to the correct detail page; current Incident and Kaiju fields render; missing records return 404; feature and Livewire tests pass. | INC-004A; High; merged in PR #24. |
| INC-005 — Edit incidents and change status | Completed | Correct incident data and manage its lifecycle. | Prefilled Livewire state, route model binding, enum and relationship validation, Eloquent updates, and explicit UTC conversion. | All current fields and the associated Kaiju can be updated; every supported status transition persists; invalid values preserve the record; feature and Livewire tests pass. | INC-004B; High; merged in PR #25; status transitions are unrestricted until a business rule requires otherwise. |
| INC-006 — Delete incidents safely | In review | Delete an incident only after confirmation. | Livewire confirmation state, Flux modal, guarded action, Eloquent deletion, redirect, and toast feedback. | Opening or cancelling confirmation preserves data; deletion without confirmation is ignored; confirmation deletes only the selected Incident while preserving its Kaiju and other Incidents; Livewire and database tests pass. | INC-005; High; implemented independently in PR #26. |
| INC-007 — Search, filter, and order incidents | In review | Locate relevant incidents efficiently. | Grouped conditional Eloquent queries, reactive Livewire state, query-string synchronization, pagination resets, and deterministic occurrence ordering. | Case-insensitive title/location search, status and Kaiju filters, and newest/oldest ordering combine correctly, restore from a URL, reset and retain pagination appropriately, clear together, and display a filtered empty state; feature and Livewire tests pass. | INC-004A; Medium; developed independently from INC-006 to allow parallel review. |
| INC-008 — Show incident history on kaiju details | Planned | Review a Kaiju's incidents from its existing detail page. | Eager loading, occurrence ordering, and relationship rendering. | Kaiju details show the correct incidents in occurrence order without repeated relationship queries; feature tests cover populated and empty histories. | INC-007, INC-002B; High; the cascade constraint is covered by INC-001 and its exact warning is brought forward to INC-002B. |

## Priority checkpoint — Temporary Railway demo

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| DEP-001 — Deploy the current KERS demo to Railway | Completed | Make the current locally tested application publicly accessible before continuing lower-priority features. | Minimal `railway.json`; GitHub-connected Railway application and PostgreSQL services; private `DB_URL`; native Railpack build and start; explicit non-interactive pre-deploy migration and repeatable seeding; trusted HTTPS proxy headers; `/up` healthcheck; public URL and automatic `main` deployments. | Railway builds successfully; Vite assets load; PostgreSQL migrations and seeders succeed; the application and Kaiju catalogue respond publicly over HTTPS; Laravel trusts Railway's reverse proxy; `/up` succeeds. | INC-003; High; completed in PR #20. Temporary demo: https://kers-production.up.railway.app/kaijus. Review usage and remove the services after approximately one week. |
| DEM-001 — Add a protected demo-data API | Completed | Reset the temporary demo remotely and learn Laravel controllers without adding visible UI. | API route registration, controller actions, JSON responses, custom API-key middleware, environment configuration, transactions, and Artisan invocation. | Empty configuration disables both routes; missing or invalid Bearer keys cannot mutate data; authenticated wipe deletes only Kaijus and cascaded Incidents; authenticated seed is repeatable; wipe then seed restores the canonical dataset; PostgreSQL-backed feature tests pass. | DEP-001, INC-002B; Medium; merged in PR #22; temporary demo tooling to remove or replace when final authorization exists. |

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

## Phase 8 — Dashboard, localization, and visual refinement

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| UI-001 — Add operational dashboard summaries | Planned | Provide a concise view of kaijus, incident status, and team capacity. | Eloquent aggregates and derived Livewire state. | Counts and recent records are accurate without N+1 queries; feature and database tests pass. | AUT-005; Medium. |
| UI-002 — Improve navigation and shared states | Planned | Make completed workflows coherent and accessible. | Reusable Blade/Livewire components, empty/loading/success states. | Navigation reflects authorization and common states are consistent, responsive, and keyboard accessible; relevant rendering tests pass. | UI-001; Medium. |
| UI-003 — Internationalize the interface | Planned | Make KERS usable in selected locales without duplicating screens. | Laravel localization files, locale selection and persistence, and fallback behavior. | User-facing copy changes with the selected locale, invalid or unavailable locales fall back to English, and feature and Livewire tests cover selection and persistence. | UI-002; Low; target locales and any required exception to the repository language policy must be approved with the implementation proposal. |
| UI-004 — Apply restrained visual refinement | Planned | Give KERS a readable command-centre identity. | Tailwind composition, responsive and dark-mode review. | Core pages are visually consistent and accessible without complex charts; frontend build and manual responsive review pass. | UI-003; Low. |

## Phase 9 — Post-demo deployment follow-up

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| DEP-002 — Perform the final production and demo review | Planned | Confirm the public environment reliably demonstrates the selected current workflows. | Public smoke test, representative data review, asset and log checks, CI and automatic-deployment verification. | Current demo flows work through the public URL, representative data is safe and understandable, assets load, no production error is visible, GitHub Actions passes, and the latest `main` revision is deployed. | DEP-001 and selected product work; High before the demonstration. |
| DEP-003 — Retire the temporary Railway environment | Planned | Avoid leaving unnecessary services or trial usage active after the demo. | Usage review and explicit stop or removal of the application and database services. | Railway usage is reviewed after approximately one week and both services are stopped or removed when no longer needed. | DEP-002; High operational priority after the demonstration. |
| DEP-004 — Evaluate long-term hosting if requested | Optional | Select a sustainable provider only if KERS needs a persistent public environment. | Current provider comparison, support, cost, rollback, and operational requirements. | A new proposal compares current options and documents the selected long-term architecture. | DEP-003; Low; the temporary Railway decision does not select permanent hosting. |

## Optional work

Static-analysis expansion, queues, scheduling, events, notifications, incident
timelines, and multiple response teams remain `Optional`. Each requires a real
product need and a separate approved roadmap proposal.
