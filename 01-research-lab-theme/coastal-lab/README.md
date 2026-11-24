# Coastal Lab: custom WordPress theme

**Project stage: near completion (v1.0.0-rc.2).** Core templates, content model and build
pipeline are done. The remaining work is QA and launch tasks, listed in [CHANGELOG.md](CHANGELOG.md).

A from-scratch theme for a fictional university research lab ("Coastal Ecology Lab"). It
demonstrates:

- **PHP in a WordPress context**: template hierarchy, custom `WP_Query`, `pre_get_posts`,
  `get_template_part()` with `$args`, escaping on output (`esc_html`, `esc_url`, `wp_kses_post`)
- **Custom post types + taxonomy**: `research_project`, `team_member`, `research_area`
- **Advanced Custom Fields**: field groups registered in PHP, ACF JSON sync, free-version
  compatible, graceful fallback when ACF is inactive
- **Build process**: `@wordpress/scripts` (webpack + Sass + PostCSS), auto-generated
  `*.asset.php` manifests for cache busting, ESLint + Stylelint with WordPress configs
- **UX & accessibility**: skip link, keyboard-operable disclosure nav, 44px touch targets,
  AA color contrast, reduced-motion support, progressive-enhancement filters
- **Block editor support**: `theme.json` palette/typography locked to brand values,
  matching editor styles

## Quick start

```bash
npm install
npm run build      # production build -> build/
npm start          # watch mode while developing
npm run lint:js
npm run lint:css
```

Then activate **Coastal Lab** in *Appearance → Themes* (or network-enable it on multisite)
and install **Advanced Custom Fields**.

## File map

| Path | Purpose |
|------|---------|
| `functions.php` | Bootstrap only: loads files from `inc/` |
| `inc/setup.php` | Theme supports, menus, image sizes, `pre_get_posts` |
| `inc/enqueue.php` | Loads `build/` assets using the webpack manifest |
| `inc/post-types.php` | CPTs + taxonomy, rewrite flush on activation |
| `inc/acf.php` | ACF field groups, JSON save/load, options page (PRO only) |
| `inc/template-tags.php` | Escaped output helpers, ACF-optional `coastal_lab_field()` |
| `template-parts/` | Cards, hero, empty state |
| `src/js/` | `navigation.js` (accessible menu), `filter.js` (research filter) |
| `src/scss/` | Tokens, base, layout, header, components, footer, editor |
| `theme.json` | Editor palette, fonts, layout widths |
| `acf-json/` | ACF writes UI-created field groups here; commit them |
| `docs/site-configuration.md` | How the live site is configured (menus, settings, plugins) |

## Key decisions

1. **Field groups in PHP, not only the database.** They can be code-reviewed in a pull
   request and deploy the same way to staging and production.
2. **ACF free compatible.** No repeaters or ACF Blocks are required, so the theme runs
   on a CampusPress network regardless of which ACF tier is licensed there.
3. **People grouped by role.** The task analysis in the UX audit found that visitors look
   for "who runs the lab" and "who are the students", not an A–Z list. See
   [../../04-ux-audit-and-docs](../../04-ux-audit-and-docs).
4. **No jQuery.** Vanilla JS is about 2.6 KB minified. The script is deferred and has
   no runtime dependencies.
