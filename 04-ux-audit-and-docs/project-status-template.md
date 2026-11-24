# Project status: picking up an in-progress site

> Fill this in during your **first day** on an inherited project, before changing anything.
> It tells you (and your supervisor) what state things are in, and it turns into the handoff
> doc when you leave. Example below is filled in for the Coastal Lab site.

---

## Coastal Ecology Lab: status as of 2026-11-23

| | |
|---|---|
| **Stage** | Near completion: release candidate `1.0.0-rc.2` |
| **Site URL** | `/coastal-lab/` on the CampusPress network |
| **Repo / branch** | `campus-wp-portfolio` · `main` (protected) · work branch `feature/launch-qa` |
| **Stakeholder** | Lab PI (content approval), department comms (brand) |
| **IT contact** | Web services queue (see ticket template) |
| **Target launch** | Before spring semester |

### 1. What exists (verified, not assumed)
- [x] Theme builds cleanly from a fresh clone (`npm install && npm run build`)
- [x] All templates render with sample content (home, research archive/single, people archive/single, 404, search)
- [x] ACF field groups load from code. No unsynced groups in ACF → Field Groups
- [x] Menus assigned to all three locations
- [ ] `screenshot.png` missing
- [ ] Some project summaries exceed 280 characters (imported before the limit existed)

### 2. Known issues
| Issue | Severity | Owner | Status |
|-------|----------|-------|--------|
| Old `/projects/*` URLs 404 | High | Me + IT | Ticket open, waiting on redirect method decision |
| Two alumni profiles have no photo | Low | Lab manager | Requested |

### 3. Decisions already made (don't relitigate)
- Field groups live in PHP, not the database
- People page grouped by role, not A–Z
- No jQuery

### 4. What I'm doing next (in order)
1. Finish launch checklist in `CHANGELOG.md`
2. Run accessibility checklist on all templates
3. Content pass with PI → tag `v1.0.0`

### 5. Questions for supervisor
- Who gives final sign-off for launch: PI or department comms?
- Is there a maintenance window for adding redirects?
