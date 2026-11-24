# Working on CampusPress vs. self-hosted WordPress

CampusPress is managed WordPress Multisite for education. Day-to-day editing works the
same as WordPress, but some things work differently, and they affect how code in this
repo is written and deployed.

> Policies vary by institution and contract. Confirm the specifics with your IT team or
> CampusPress support before relying on them.

| Topic | Self-hosted WordPress | CampusPress (typical) | What it means for this repo |
|-------|----------------------|------------------------|-----------------------------|
| Installing plugins/themes | Any admin can upload | Site admins choose from a curated, network-enabled list. Custom code is installed by network admins / CampusPress after review | Code follows WordPress Coding Standards (CI runs PHPCS), so it passes review quickly. Changes ship as tagged GitHub releases with a changelog |
| Roles | Administrator is top | **Super Admin** (network) vs. site **Administrator**. Site admins can't edit theme/plugin files | Site-specific config is done through the UI and recorded in `site-configuration.md` |
| Network vs. site activation | N/A | Plugins can be network-activated (all sites) or activated per site | Campus Directory is built to be network-activated. The theme is network-*enabled* and activated per site |
| Server access | SSH/SFTP, database | Usually none | No code depends on server config. Redirects and cron go through IT tickets |
| Updates | You manage them | Core/plugins updated by the host | Never edit parent themes (hence the Divi child theme). Test against the latest WordPress version |
| Caching | Your choice | Host-level page cache and possibly object cache | Directory caching uses transients, which work with or without a persistent object cache |
| Staging | Your choice | Ask, or clone a site within the network | Develop locally with `docker compose` (multisite), then deploy a release |

## Multisite gotchas these projects handle

- **`switch_to_blog()` doesn't switch plugins or post types.** Only data and options switch. The
  directory CPT is registered on every site so permalinks resolve correctly after switching.
- **Always pair `switch_to_blog()` with `restore_current_blog()`**, ideally in `finally`.
- **Options vs. site options:** `get_option()` is per site. `get_site_option()` is network-wide.
  The directory's included-sites setting and cache version are network-wide.
- **Rewrite flushing on network activation** can't loop through every site synchronously at scale.
  The plugin flags sites and flushes lazily on the next `init`.
- **Uploads are per site** (`/wp-content/uploads/sites/<id>/`), so image URLs must be generated
  while switched to the right blog.
