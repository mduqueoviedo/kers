# Product Requirements

## Purpose

KERS (Kaiju Emergency Response System) is a learning-oriented Laravel
application for managing fictional kaijus, the incidents they cause, and the
response teams assigned to those incidents.

The fictional theme supports an approachable project, while the product rules
must be implemented as a serious and maintainable application.

## Product goals

- Maintain a catalogue of known kaijus.
- Record and manage incidents associated with known kaijus.
- Maintain response teams and assign at most one team to an incident.
- Prevent response teams from exceeding their active-incident capacity.
- Convert selected live USGS earthquake events into fictional incidents.
- Introduce authentication, role-based authorization, and a simple operational
  dashboard after the public domain workflows are complete.

## Users and roles

The initial domain features remain publicly accessible while they are being
built. Authentication and authorization are introduced later.

Once authorization is active, new registered users receive the `operator`
role. Administrators are assigned through seed data or a direct database
update; the core product does not include user management.

### Operator

An operator may view all domain records, manage incidents, assign response
teams, query USGS, and create incidents from USGS events.

### Administrator

An administrator may perform every operator action and may also create, edit,
and delete kaijus and response teams, and delete incidents.

## Domain concepts

### Kaiju

A kaiju is a known creature with its own identity and incident history.

Fields:

- `name`
- `category`
- `threat_level`
- `description`
- timestamps

Categories are `aquatic`, `terrestrial`, `aerial`, `amphibious`, and
`unknown`. Names are unique using exact PostgreSQL string comparison. Threat
level is an integer from 1 to 5. Description is optional.

A kaiju has many incidents. Deleting a kaiju must cascade to its incidents at
the database level. The confirmation must state how many incidents will also
be permanently deleted.

Kaijus support creation, listing, pagination, detail views, editing, deletion,
name search, and category and threat-level filters.

### Incident

An incident is a specific event involving one known kaiju.

Fields:

- `title`
- `description`
- `location`
- `status`
- `occurred_at`
- `kaiju_id`
- timestamps

Statuses are `open`, `contained`, and `closed`. Location begins as free text.
Every initial incident belongs to a known kaiju.

Incidents support creation, listing, pagination, detail views, editing,
deletion with confirmation, status changes, title or location search, status
and kaiju filters, and ordering by occurrence date.

### Response team

A response team may be assigned to incidents over time.

Fields:

- `name`
- `code`
- `capacity`
- `description`
- timestamps

Capacity is an integer from 1 to 5 and defaults to 1. A response team has many
incidents; an incident has zero or one response team.

Open and contained incidents consume capacity. Closed incidents do not. A team
cannot be assigned beyond its configured capacity.

Response teams support creation, listing, detail views, editing, deletion, and
simple search or filtering where useful.

## USGS integration

USGS earthquake events are retrieved on demand and are never stored as a
separate local entity.

The user flow is:

```text
Request recent seismic events
→ review current results
→ select one or more events
→ select an existing kaiju
→ create one incident for each selected event
```

USGS incidents add nullable source fields:

- `source`
- `external_event_id`
- `external_url`
- `magnitude`
- `latitude`
- `longitude`
- `depth`

The event provides the incident title, location, occurrence time, and source
data. Those generated incident values may later be edited normally.

The database must prevent duplicate incidents for the same source and external
event identifier while continuing to allow manual incidents with null source
values.

## Dashboard

The final core dashboard may show:

- Total kaijus
- Incident counts by status
- Teams at or below capacity
- Recent incidents
- Highest-threat kaijus

The dashboard uses simple aggregate queries and restrained UI components. It
does not require advanced analytics or complex charts.

## Primary product flows

- Create, review, update, search, filter, and delete kaijus.
- Create and manage incidents belonging to known kaijus.
- Change incident status and review incidents on a kaiju detail page.
- Create and manage response teams.
- Assign, replace, or remove an incident's response team.
- Enforce team capacity across open and contained incidents.
- Review recent USGS events and create one or more incidents from selections.
- Register, authenticate, and perform actions permitted by the user's role.

## Scope rules

- Domain functionality is introduced incrementally in reviewable iterations.
- Manual kaiju and incident workflows precede USGS integration.
- Authentication follows the main public domain functionality.
- Visual refinement follows the domain, integration, and authorization work.
- PostgreSQL is used for development, testing, and deployment.

## Core non-goals

- Unknown-kaiju incident workflows
- Incident timelines or activity history
- Multiple response teams per incident
- Team specializations, personnel, calendars, or availability states
- Automated risk processing
- Notifications
- Scheduled USGS imports
- Background jobs without a real asynchronous need
- Configurable permission matrices
- User-management screens
- Complex charts
- Provider-specific deployment design before the deployment phase

Optional extensions may be considered only after the core roadmap is stable.
