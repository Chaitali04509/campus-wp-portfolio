# Changelog: Coastal Lab theme

Format follows [Keep a Changelog](https://keepachangelog.com/). Versions follow SemVer.

## [Unreleased]: launch checklist

- [x] Add `screenshot.png` (1200×900) for the Themes screen
- [ ] Final content pass with lab PI: project summaries ≤ 280 chars
- [ ] Run axe DevTools on home, research archive, single project, people, profile
- [ ] Set up 301 redirects from old URLs (`/projects/*` → `/research/*`). Ask IT whether
      redirects belong at the server level or in the Redirection plugin
- [ ] Lighthouse mobile ≥ 90 performance / 100 accessibility
- [ ] Tag `v1.0.0` and attach the built zip to the GitHub release

## [1.0.0-rc.2]

### Added
- Client-side research-area filter with `?area=` deep links and live-region result count
- `theme.json` locking the editor palette to brand colors (no custom colors)
- 404 template that suggests next steps instead of a dead end

### Changed
- People archive is grouped by role (PI → alumni) instead of a single alphabetical grid
- Menu toggle keeps its visible "Menu" label as the accessible name (WCAG 2.5.3) and
  no longer swaps `aria-label`

### Fixed
- Status badges stretched to full card width (flex child needed `align-self`)
- White gap above the front-page hero
- Person grid fell back to one oversized column on phones
- Card term links were covered by the stretched card link and could not be clicked
- Editor styles were double-scoped under `.editor-styles-wrapper`

## [1.0.0-rc.1]

### Added
- `research_project` and `team_member` CPTs, `research_area` taxonomy
- ACF field groups for project details and team profiles
- `@wordpress/scripts` build with ESLint/Stylelint
- Accessible primary navigation with sub-menu disclosure buttons
