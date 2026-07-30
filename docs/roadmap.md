# Roadmap

## How to read this delivery record

This roadmap is retained as a historical record of the focused pull requests
used to build KERS. Active KERS delivery is closed; entries outside the final
technical-demo scope are classified as deferred or optional rather than as an
active backlog.

Final statuses are `Completed`, `Deferred`, and `Optional`. Completed items
were merged into `main`; Deferred entries explain why they fall outside the
final delivery.

For each table, the section supplies the phase. Combined columns retain the
required objective, user value, Laravel concepts, expected scope, acceptance
criteria, testing expectations, dependencies, priority, and notes.

## Final delivery status

The final technical demo includes the Kaiju and Incident workflows, English and
Spanish localization, single-event USGS import with duplicate prevention,
disposable mutation authentication, the protected demo-data reset, automated
quality checks, and the verified temporary Railway deployment. No further
application work is committed. Deferred and optional entries below preserve
the original product direction for traceability without implying continued
delivery.

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
| INC-006 — Delete incidents safely | Completed | Delete an incident only after confirmation. | Livewire confirmation state, Flux modal, guarded action, Eloquent deletion, redirect, and toast feedback. | Opening or cancelling confirmation preserves data; deletion without confirmation is ignored; confirmation deletes only the selected Incident while preserving its Kaiju and other Incidents; Livewire and database tests pass. | INC-005; High; merged in PR #26. |
| INC-007 — Search, filter, and order incidents | Completed | Locate relevant incidents efficiently. | Grouped conditional Eloquent queries, reactive Livewire state, query-string synchronization, pagination resets, and deterministic occurrence ordering. | Case-insensitive title/location search, status and Kaiju filters, and newest/oldest ordering combine correctly, restore from a URL, reset and retain pagination appropriately, clear together, and display a filtered empty state; feature and Livewire tests pass. | INC-004A; Medium; merged in PR #27. |
| INC-008 — Show incident history on kaiju details | Completed | Review a Kaiju's incidents from its existing detail page. | Eager loading, occurrence ordering, and relationship rendering. | Kaiju details show the correct incidents in occurrence order without repeated relationship queries; feature tests cover populated and empty histories. | INC-007, INC-002B; High; merged in PR #29; the cascade constraint is covered by INC-001 and its exact warning is brought forward to INC-002B. |

## Learning checkpoint — Domain orientation

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| UX-001 — Add domain orientation cues | Completed | Make Kaiju and Incident pages immediately distinguishable without starting broad visual refinement. | Route-aware shared Blade layout, active navigation semantics, restrained domain colors, labelled catalogue surfaces, and an original responsive KERS emblem with favicon variants. | Kaiju and Incident routes and catalogue records show distinct textual and color cues in light and dark modes; nested Incident records retain their identity; exactly one navigation item exposes the active page semantics; KERS replaces the Laravel icon assets; feature tests, frontend build, and manual asset inspection pass. | INC-008; Medium; merged in PR #31 before localization so LOC-001 covers the resulting interface. |

## Learning checkpoint — English and Spanish localization

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| LOC-001 — Internationalize the current KERS interface | Completed | Let users operate the current interface in English or Spanish. | Session-backed locale selection, keyed Laravel translations, locale-aware labels and dates, conventional validation and pagination translations, and parity testing. | All application-owned copy across shared, Kaiju, Incident, USGS, and current auth surfaces renders in both locales; persisted and USGS source data remain unchanged; English fallback, rendering, and translation-key parity tests pass. | UX-001; High learning priority; merged in PR #38. Only `en` and `es` are in scope. Repository code and documentation remain in English; Spanish translation resources are the explicit exception. |

## Priority checkpoint — Temporary Railway demo

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| DEP-001 — Deploy the current KERS demo to Railway | Completed | Make the current locally tested application publicly accessible before continuing lower-priority features. | Minimal `railway.json`; GitHub-connected Railway application and PostgreSQL services; private `DB_URL`; native Railpack build and start; non-destructive pre-deploy migrations plus deterministic demo-user seeding; trusted HTTPS proxy headers; `/up` healthcheck; public URL and automatic `main` deployments. | Railway runs pending migrations without dropping tables, recreates the configured demo user, and preserves Kaijus and Incidents; Vite assets load; the application and Kaiju catalogue respond publicly over HTTPS; Laravel trusts Railway's reverse proxy; `/up` succeeds. | INC-003; High; completed in PR #20 and updated by this reliability rollback after opaque Railway pre-deploy failures. The protected demo-data API is the explicit domain reset mechanism. Temporary demo: https://kers-production.up.railway.app/kaijus. Review usage and remove the services after approximately one week. |
| DEM-001 — Add a protected demo-data API | Completed | Reset the temporary demo remotely and learn Laravel controllers without adding visible UI. | API route registration, controller actions, JSON responses, custom API-key middleware, environment configuration, transactions, and Artisan invocation. | Empty configuration disables the reset route; missing or invalid Bearer keys cannot mutate data; authenticated reset deletes Kaijus and cascaded Incidents, restores the canonical dataset, and preserves users; PostgreSQL-backed feature tests pass. | DEP-001, INC-002B; Medium; merged in PR #22 and refined to the final reset-only API in PR #43; temporary demo tooling for the final delivery. |

## Phase 4 — USGS integration

USGS integration was delivered through focused iterations that first fetched
and displayed recent events without local event persistence, then added source
metadata, single-event Incident creation, and duplicate prevention.
Multi-selection remained an optional extension outside the final demo.

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| USG-001 — Configure and call USGS | Completed | Retrieve recent earthquake events through one focused client. | Configuration, environment variables, Laravel HTTP client, timeouts. | Current events are fetched without persistence and failures are represented predictably; HTTP fakes cover success and failure. | INC-004A; High; merged in PR #34; no local event persistence. |
| USG-002 — Map and display recent events | Completed | Present external data independently of its raw payload shape. | Response mapping and a simple Livewire list. | Required event fields render and no local event records are created; mapper and Livewire tests use fakes. | USG-001; High; merged in PR #35. |
| USG-003 — Add incident source fields | Completed | Store only the source details needed by imported incidents. | Nullable migration fields, casts, composite uniqueness. | Manual incidents retain null source data and duplicate USGS identifiers are rejected by PostgreSQL; migration tests pass. | USG-002; High; merged in PR #36. |
| USG-004 — Create one incident from one event | Completed | Convert a selected event into an editable incident for a chosen kaiju. | Livewire selection, server-fetched transient catalogue validation, mapping, Eloquent creation. | Generated title, location, time, coordinates, magnitude, depth, URL, and source persist correctly; unavailable events and missing selections are explained; HTTP and database tests pass. | USG-003, INC-003; High; merged in PR #37; the current catalogue is fetched again during import. |
| USG-005 — Prevent duplicate imports | Completed | Give clear feedback instead of creating the same source incident twice. | Database exception handling and validation feedback. | Re-import is prevented under concurrent-safe uniqueness and produces understandable feedback; regression tests pass. | USG-004; High; merged in PR #39. |
| USG-006 — Import multiple selected events | Optional | Create several controlled incidents in one action. | Multi-selection, transaction, partial-failure decision. | Each unique selection creates one incident and duplicate/error behavior is explicit; transaction and Livewire tests cover mixed selections. | USG-005; Medium; hypothetical extension outside the final demo scope. |

## Phase 5 — Response team management

Response Team management was part of the broader product concept but is
outside the final technical-demo scope.

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| TEM-001 — Add the ResponseTeam model | Deferred — outside final demo scope | Persist teams with controlled capacity. | Migration, Eloquent model, default values, database constraints. | Capacity defaults to 1 and is limited to 1–5; migration and model behavior are tested. | FND-003; originally High. |
| TEM-002 — Add response-team factory data | Deferred — outside final demo scope | Provide representative teams for development and tests. | Factory states and seeding. | Valid capacity variants are produced and seeding is repeatable. | TEM-001; originally Medium. |
| TEM-003 — Create and list response teams | Deferred — outside final demo scope | Let users register teams and review available capacity. | Livewire forms, validation, Eloquent listing. | Valid teams persist and display; code/name/capacity validation and empty state are tested. | TEM-002; originally High. |
| TEM-004 — View and edit response teams | Deferred — outside final demo scope | Review and correct an existing team's details. | Detail component, model binding, form state, update validation. | Correct details render, valid edits persist, and invalid capacity fails; feature and Livewire tests pass. | TEM-003; originally High. |
| TEM-005 — Delete and search response teams | Deferred — outside final demo scope | Remove teams safely and find teams when the list grows. | Confirmation state and conditional queries. | Deletion requires confirmation and search returns matching teams; Livewire and database tests pass. | TEM-004; originally Medium; assignment deletion behavior belonged with the deferred relationship work. |

## Phase 6 — Incident assignment and team capacity

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| ASN-001 — Relate incidents to response teams | Deferred — outside final demo scope | Allow an incident to reference zero or one team. | Nullable foreign key, Eloquent relationships, eager loading. | Assignment is optional and relationships resolve in both directions; migration and database tests pass. | INC-001, TEM-001; originally High. |
| ASN-002 — Assign, change, and remove a team | Deferred — outside final demo scope | Let operators manage the team responding to an incident. | Livewire actions, relationship updates, validation. | Users can assign, replace, and remove a team; invalid identifiers fail; Livewire tests pass. | ASN-001, INC-005; originally High. |
| ASN-003 — Enforce team capacity | Deferred — outside final demo scope | Prevent overcommitting active response teams. | Aggregate queries, business validation, transactions where needed. | Open and contained incidents consume capacity, closed incidents do not, and over-capacity assignments fail; edge cases are tested. | ASN-002; originally High. |
| ASN-004 — Display workload and release capacity | Deferred — outside final demo scope | Make assignment decisions understandable and reflect incident closure. | Relationship counts and derived UI state. | Team workload is visible and closing, reopening, or reassigning incidents updates availability correctly; regression tests cover transitions. | ASN-003; originally High. |

## Phase 7 — Authentication and authorization

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| AUT-001 — Add disposable demo authentication | Completed | Let one seeded demo account manage the public catalogue safely. | Fortify session login/logout, configuration-backed deterministic seeding, auth middleware, server-side Livewire guards, and guest-safe controls. | Guests retain read-only access; create/edit routes redirect to login; direct Livewire mutation calls are forbidden; login, logout, configured seeding, English/Spanish UI parity, and hidden controls are tested. | USG-005; High; merged in PR #41. Registration, roles, policies, verification, password recovery, profiles, social login, and Response Teams are outside the final demo scope. |
| AUT-002 — Add roles and default operators | Deferred — outside final demo scope | Establish the simple operator/admin model. | PHP enum or equivalent cast, user migration, registration action, seed data. | New users become operators and a local admin can be seeded; database and registration tests pass. | AUT-001; originally High. |
| AUT-003 — Authorize kaiju and incident actions | Deferred — outside final demo scope | Restrict destructive and catalogue actions by role. | Policies, Livewire authorization, forbidden responses. | Operators and admins receive exactly the documented capabilities; policy and Livewire tests cover allowed and forbidden paths. | AUT-002, KAI-006, INC-006; originally High. |
| AUT-004 — Authorize team and assignment actions | Deferred — outside final demo scope | Apply roles consistently to team management and assignments. | Policies and Livewire authorization checks. | Operators may assign teams but only admins manage team records; authorization tests pass. | AUT-003, ASN-004; originally High. |
| AUT-005 — Verify the complete access model | Deferred — outside final demo scope | Close authorization gaps across routes, navigation, and actions. | Middleware/policy integration and UI visibility. | Direct requests and Livewire calls cannot bypass policy decisions; email verification behavior is tested where enabled. | AUT-004; originally High. |

## Phase 8 — Dashboard and visual refinement

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| UI-001 — Add operational dashboard summaries | Deferred — outside final demo scope | Provide a concise view of kaijus, incident status, and team capacity. | Eloquent aggregates and derived Livewire state. | Counts and recent records are accurate without N+1 queries; feature and database tests pass. | AUT-005; originally Medium. |
| UI-002 — Improve navigation and shared states | Deferred | Make completed workflows coherent and accessible. | Reusable Blade/Livewire components, empty/loading/success states. | Navigation reflects authorization and common states are consistent, responsive, and keyboard accessible; relevant rendering tests pass. | The current interface and localization are accepted as the final demo presentation. |
| UI-003 — Apply restrained visual refinement | Deferred | Give KERS a readable command-centre identity. | Tailwind composition, responsive and dark-mode review. | Core pages are visually consistent and accessible without complex charts; frontend build and manual responsive review pass. | The current interface and localization are accepted as the final demo presentation. |

## Phase 9 — Post-demo deployment follow-up

| ID and title | Status | Objective and user value | Concepts and scope | Acceptance and testing | Dependencies, priority, and notes |
| --- | --- | --- | --- | --- | --- |
| DEP-002 — Perform the final production and demo review | Completed | Confirm the public environment reliably demonstrates the selected current workflows. | Public smoke test, representative data review, asset and log checks, CI and automatic-deployment verification. | Current demo flows work through the public URL, representative data is safe and understandable, assets load, no production error is visible, GitHub Actions passes, and the latest `main` revision is deployed. | DEP-001 and selected product work; manually verified application authentication, database behavior, assets, public pages, deployment health, and regional placement. |
| DEP-003 — Retire the temporary Railway environment | Deferred — post-demo operational action | Avoid leaving unnecessary services or trial usage active after the demo. | Usage review and explicit stop or removal of the application and database services. | Railway usage is reviewed after approximately one week and both services are stopped or removed when no longer needed. | DEP-002; retained as an operational obligation, not active application development. |
| DEP-004 — Evaluate long-term hosting if requested | Optional | Select a sustainable provider only if KERS needs a long-term production environment. | Current provider comparison, support, cost, rollback, and operational requirements. | A new proposal compares current options and documents the selected long-term architecture. | DEP-003; Low; the temporary Railway decision does not select permanent hosting. |

## Optional work

Static-analysis expansion, queues, scheduling, events, notifications, incident
timelines, and multiple-team support remain `Optional`. They are hypothetical
extensions that would require a new product need and separately approved scope.
