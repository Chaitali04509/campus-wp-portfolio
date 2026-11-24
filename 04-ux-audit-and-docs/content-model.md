# Content model across the network

Structured content goes in custom post types + ACF fields, not in free-form page layouts.
That way it can be reused, filtered and displayed consistently, and editors fill in a
form instead of fighting a layout.

## Content types

| Content | Where it lives | Post type | Taxonomy | ACF fields |
|---------|----------------|-----------|----------|------------|
| Directory person (faculty/staff) | Any site (plugin, network-wide) | `cd_person` | `cd_department` | `cd_job_title`, `cd_email`, `cd_phone`, `cd_office`, `cd_profile_url`, `cd_expertise` |
| Lab team member | Lab site (theme) | `team_member` | `research_area` | `role_group`, `position_title`, `pronouns`, `email`, `office`, `research_interests`, `website`, `orcid` |
| Research project | Lab site (theme) | `research_project` | `research_area` | `project_status`, `start_date`, `funding_source`, `lead_investigator` (post object), `project_team` (relationship), `plain_summary`, `external_link` |
| News | Every site | `post` | `category` | (core fields) |
| Program info, policies | Dept site | `page` | (none) | (Divi layouts) |

## Field design rules

| Rule | Example |
|------|---------|
| Use the most specific field type | `email` / `url` fields validate input. `select` instead of free text for status |
| Constrain length where display depends on it | `plain_summary` max 280 chars, so cards never overflow |
| Write instructions for the editor, not the developer | "One or two sentences a non-specialist can understand" |
| Relationships over copy-paste | A project links to `team_member` posts, so a name change updates everywhere |
| Return IDs, not objects, from relationship fields | Smaller payloads. Templates fetch only what they display |
| Keep field groups in code | Registered with `acf_add_local_field_group()` and reviewed in pull requests. ACF JSON stays enabled for UI-created groups |
| Plugin data must not depend on ACF | `register_post_meta()` is the source of truth. ACF is the UI |

## Why two "person" types?

`team_member` (lab theme) and `cd_person` (network plugin) look similar but serve different jobs:

- `team_member` includes students and alumni, is grouped by lab role, and links to projects. It is lab-specific.
- `cd_person` is the official department/unit directory. It is network-wide and maintained by department admins.

**Planned (v1.1):** a "Linked directory profile" field on `team_member` so a lab profile can
pull title/email/office from `cd_person` and the details aren't kept in two places.
