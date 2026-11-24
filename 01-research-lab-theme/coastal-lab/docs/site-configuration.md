# Site configuration: Coastal Ecology Lab

This page records everything about the live site that is **not** stored in code, so the
next person can rebuild or troubleshoot it without guesswork. Update it whenever a setting changes.

_Last updated: 2026-11-23_

## Environment

| Item | Value |
|------|-------|
| Platform | CampusPress (WordPress Multisite), sub-site `/coastal-lab/` |
| WordPress | 6.7.x (managed by CampusPress) |
| PHP | 8.2 (managed) |
| Theme | Coastal Lab `1.0.0-rc.2` (network-enabled, activated on this site only) |
| Repo | `campus-wp-portfolio` → `01-research-lab-theme/coastal-lab` |

## Plugins (site-level)

| Plugin | Why | Notes |
|--------|-----|-------|
| Advanced Custom Fields | Project & profile fields | Field groups live in code (`inc/acf.php`). Do **not** edit them in the UI on production |
| Campus Directory | Department-wide people directory | Network-activated by IT. See project 02 |
| Redirection | Old URL → new URL mapping | Pending IT decision (see CHANGELOG) |

## Settings

| Screen | Setting | Value | Reason |
|--------|---------|-------|--------|
| Settings → Reading | Homepage displays | Static page: "Home" | Hero copy is editable page content |
| Settings → Reading | Posts page | "News" | |
| Settings → Permalinks | Structure | `/%postname%/` | Clean URLs; required for `/research/` and `/people/` |
| Settings → Discussion | Comments | Off | Lab site has no comment moderation owner |

## Menus

| Location | Menu name | Items |
|----------|-----------|-------|
| Primary | Main | Research · People · Publications · News · Join Us · Contact |
| Utility | Utility | Department home · Give · Directory |
| Footer | Footer | Accessibility · Privacy · Site login |

## Content rules for editors

- **Project summaries**: one or two plain-language sentences, 280 characters max (the field enforces this).
- **Headshots**: square, at least 400×400. The theme crops to `coastal-lab-portrait`.
- **Team order**: use *Page Attributes → Order* on each person (lower numbers appear first in their group).
- **Alumni**: change *Group* to "Alumni". Don't delete the profile, because old links keep working.

## Deploying theme changes

1. Branch from `main` → make changes → `npm run build` → open a PR (CI runs lint + build).
2. After merge, tag a release (`v1.0.x`). CI attaches the built theme zip.
3. **CampusPress:** custom themes are installed by CampusPress support or network admins,
   not uploaded by site admins. Send the release zip plus changelog to the IT contact
   (template: `04-ux-audit-and-docs/it-support-ticket-template.md`).
