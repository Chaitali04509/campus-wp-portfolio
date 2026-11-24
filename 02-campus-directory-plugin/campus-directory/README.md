# Campus Directory: Multisite network plugin

**Project stage: mid-build (v0.6.0).** Content model, network aggregation, caching, settings
page, shortcode and REST endpoint all work. Open items are in the roadmap at the bottom.

A people directory for a **WordPress Multisite / CampusPress** network. Each department or
lab site manages its own people. A single `[campus_directory]` shortcode can show one site
or the **whole network**, with search and a department filter.

## What it demonstrates

| Skill | Where |
|-------|-------|
| Multisite architecture | `Network: true` header, `switch_to_blog()` / `restore_current_blog()` in a `try/finally`, `get_sites()`, `*_site_option`, `*_site_transient` |
| Network admin UI | `includes/class-network-settings.php`: `network_admin_menu`, `network_admin_edit_{action}`, nonces, `manage_network_options` capability |
| ACF | `includes/class-fields.php`: ACF field group as the editing UI over meta registered with `register_post_meta()` (typed, sanitized, REST-visible, works without ACF) |
| Performance | One cached, flattened list per site set. Version-number cache invalidation across the whole network. Lazy per-site rewrite flush after network activation |
| Security | Sanitize on input, escape on output, capability checks, nonce on writes, safe redirects, only public fields exposed over REST |
| Theme-friendliness | Template override (`{theme}/campus-directory/directory.php`). CSS is only loaded when the shortcode renders |
| Platform fit | Shortcode (not a custom block), so it works in the block editor, the classic editor and **Divi** |

## Install

1. Copy `campus-directory/` to `wp-content/plugins/`.
2. **Network Admin → Plugins → Network Activate**.
3. (Optional) Activate **Advanced Custom Fields** for the editing UI.
4. On any site, go to **Directory → Add Person**.
5. Add `[campus_directory]` to a page.
6. **Network Admin → Settings → Campus Directory** chooses which sites appear in network listings.

## Shortcode

| Attribute | Values | Default |
|-----------|--------|---------|
| `scope` | `network` · `site` | `network` |
| `department` | department slug (hides the dropdown) | (none) |
| `search` | `yes` · `no` | `yes` |
| `layout` | `grid` · `list` | `grid` |

## REST API

```
GET /wp-json/campus-directory/v1/people?scope=network&department=marine-sciences&search=reyes
```

## Architecture notes

```
 Site A (lab)      Site B (dept)     Site C (center)
   cd_person          cd_person          cd_person
       \                  |                 /
        \_________ switch_to_blog() _______/
                          |
          flattened array (name, title, email, url, departments...)
                          |
       set_site_transient( cd_people_{version}_{sites_hash} )
                          |
         filter by department / search in PHP
                 /                     \
       [campus_directory]       /wp-json/campus-directory/v1/people
```

- **Why flatten while switched?** `get_permalink()` and image URLs must be generated while
  the correct blog is active. After `restore_current_blog()` they would point to the wrong site.
- **Why a version number, not deleting transients?** Deleting every cached key across a
  network requires knowing all of the keys. Bumping one integer makes every old key
  unreachable at once, and old entries expire on their own.
- **Why `register_post_meta` plus ACF?** If ACF is ever deactivated, or a site's licence
  changes, the data and REST API keep working. ACF is only the UI.

## Roadmap (next tasks)

- [ ] Pagination for directories over ~200 people (currently all results render)
- [ ] Optional "featured" ordering per department
- [ ] WP-CLI command: `wp campus-directory import people.csv --site=<id>`
- [ ] PHPUnit tests using the WordPress multisite test suite
- [ ] Ask IT whether persistent object caching (Redis/Memcached) is available on
      CampusPress. If it is, cache per-site results separately
