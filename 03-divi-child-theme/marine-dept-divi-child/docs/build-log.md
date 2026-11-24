# Build log: Department of Marine Sciences site

A running log so anyone joining the project can see what has been decided, what is in
progress, and what is blocked. Newest entries at the top.

---

## 2026-11-20: Child theme scaffolded (v0.1.0)

- Created `marine-dept-divi-child`. Network-enabled, activated on `/marine-sciences/` only.
- Added skip link, focus styles, and mobile-menu keyboard fix (all failed in the audit).
- Hid Divi "Projects" CPT. Two stray demo projects were indexed by search engines.
- **Next:** build the global header & footer in Theme Builder.

## 2026-11-18: Discovery & kickoff

### Stakeholders
| Role | Needs from the site |
|------|---------------------|
| Department chair | Recruit grad students. Show research strength |
| Grad program coordinator | Fewer "how do I apply" emails |
| Faculty | Self-edit their own profiles |
| Communications office | Consistency with university brand |

### Current-site inventory (summary)
- 112 published pages, **41** not updated in 3+ years, **17** orphaned (in no menu, no inbound links)
- 9 top-level menu items, 3 of them named "Resources" / "Info" / "Links"
- Faculty bios hard-coded in pages, so edits require a web editor
- Full findings: [`04-ux-audit-and-docs/ux-audit-marine-sciences.md`](../../../04-ux-audit-and-docs/ux-audit-marine-sciences.md)

### Decisions
| # | Decision | Why |
|---|----------|-----|
| D1 | Keep Divi (don't switch to a custom theme) | Comms office edits pages themselves in Divi. Retraining cost is too high |
| D2 | Faculty/staff profiles come from **Campus Directory** plugin, not Divi pages | Single source of truth across lab + dept sites. Faculty can edit without the builder |
| D3 | Reduce top nav from 9 to 5 items | See IA doc. Audience-based labels |
| D4 | Child theme for all code | Divi updates safe. Changes reviewed in GitHub |

### Theme Builder plan
| Template | Applies to | Contents |
|----------|-----------|----------|
| Global Header | All pages | Logo, 5-item menu, utility links, search |
| Global Footer | All pages | Address, contact, accessibility/privacy links, social |
| Default Page | Pages without a custom body | Title + breadcrumb + post content |
| News Post | All posts | Title, date, featured image, content, related posts |

### Divi Library naming convention
`[Area] – [Section] – [Variant]`, e.g. `Home – Hero – Video`, `Global – CTA – Apply`, `Program – Requirements – Table`

### Page build order
1. Home → 2. Graduate Programs (highest traffic + most support emails) → 3. People
   (Campus Directory shortcode in a Code module: `[campus_directory scope="site" layout="list"]`)
   → 4. Research → 5. News → 6. Contact

### Open questions
- [ ] **Comms:** Is there an approved university Divi layout pack, or do we build from brand guidelines?
- [ ] **IT:** Can we get a staging copy of the site on CampusPress for the rebuild, or work on unpublished pages?
- [ ] **Coordinator:** Which program pages have legally required wording (do not rewrite)?
- [ ] **IT:** Redirect strategy for the 41 stale pages being removed
