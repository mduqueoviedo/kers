# Product Requirements

## Purpose

KERS (Kaiju Emergency Response System) is a learning-oriented Laravel
application for managing fictional kaijus and the incidents they cause. The
original broader product concept also included response-team assignment.

The fictional theme supports an approachable project, while the delivered
scope was implemented as a serious and maintainable application.

## Final technical-demo scope

- Maintain a catalogue of known kaijus.
- Record and manage incidents associated with known kaijus.
- Browse recent USGS earthquake events and convert one selected event into an
  editable fictional Incident.
- Protect mutations with one disposable authenticated demo account while
  keeping catalogue and detail reads public.
- Provide the interface in English and Spanish.

The original broader product concept also considered response teams and
capacity, granular operator and administrator roles, public registration, an
operational dashboard, and multi-event USGS import. Those capabilities are
deliberately outside the final technical-demo scope.

## Original role model — outside final scope

Catalogue and detail pages are public. Kaiju and Incident mutations and USGS
import require the disposable demo login. The final demo has no registration,
granular roles, policies, or user-management interface.

The following roles describe the original broader product concept and were not
delivered:

### Original operator role

An operator may view all domain records, manage incidents, assign response
teams, query USGS, and create incidents from USGS events.

### Original administrator role

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
Title, description, location, status, occurrence time, and kaiju are required.
Every initial incident belongs to a known kaiju. Occurrence times are stored
and interpreted in UTC.

Incidents support creation, listing, pagination, detail views, editing,
deletion with confirmation, status changes, title or location search, status
and kaiju filters, and ordering by occurrence date.

### Response team — outside final scope

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

The broader concept would support response-team creation, listing, detail
views, editing, deletion, and simple search or filtering where useful.

## USGS integration

USGS earthquake events are retrieved on demand and are never stored as a
separate local entity.

The user flow is:

```text
Request recent seismic events
→ review current results
→ select one event
→ select an existing kaiju
→ create one incident
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
data. Those generated incident values can be edited through the normal
Incident workflow.

The database prevents duplicate incidents for the same source and external
event identifier while continuing to allow manual incidents with null source
values.

## Dashboard — outside final scope

The original broader product concept considered a dashboard showing:

- Total kaijus
- Incident counts by status
- Teams at or below capacity
- Recent incidents
- Highest-threat kaijus

The dashboard would use simple aggregate queries and restrained UI components.
It would not require advanced analytics or complex charts.

## Delivered product flows

- Create, review, update, search, filter, and delete kaijus.
- Create and manage incidents belonging to known kaijus.
- Change incident status and review incidents on a kaiju detail page.
- Review recent USGS events and create one Incident from a selected event.
- Authenticate with the disposable demo account to perform mutations.

## Delivery approach

- Domain functionality was introduced incrementally in reviewable iterations.
- Manual Kaiju and Incident workflows preceded USGS integration.
- A temporary Railway deployment followed the first usable manual Incident
  flow so the application could be exercised through a public demo URL.
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
- Infrastructure beyond the needs of the temporary technical demo
- Redis, queues, workers, custom domains, external storage, or advanced
  observability for the temporary Railway deployment

Response teams, capacity enforcement, granular roles and policies,
registration, the operational dashboard, and multi-event USGS import are also
outside the final scope. Any future extension would require a new, explicitly
approved product scope.
