# Local setup

## Requirements
- Docker Desktop
- Node 22+ (only for building theme assets). If you don't have Node installed:
  `docker run --rm -v "$PWD":/app -w /app node:22 npm run build`
- Port 80 free (WordPress Multisite does not allow a port number in the site URL)

## First run

```bash
git clone <repo-url> campus-wp-portfolio && cd campus-wp-portfolio

# 1. Build theme assets
(cd 01-research-lab-theme/coastal-lab && npm install && npm run build)

# 2. Start WordPress + MariaDB
docker compose up -d

# 3. Provision the multisite network, ACF, sub-sites and sample content
./bin/setup.sh
```

| URL | What |
|-----|------|
| http://localhost/wp-admin/network/ | Network admin (admin / admin) |
| http://localhost/coastal-lab/ | Project 01 theme |
| http://localhost/marine-sciences/ | Project 03 site (needs Divi installed, which is a paid theme) |
| http://localhost/wp-admin/network/settings.php?page=campus-directory | Project 02 settings |

The theme and plugin folders are bind-mounted, so code changes show up on refresh.
Run `npm start` in the theme folder for watch-mode rebuilds.

## Handy WP-CLI commands

```bash
docker compose run --rm cli site list
docker compose run --rm cli --url=http://localhost/coastal-lab/ post list --post_type=research_project
docker compose run --rm cli plugin list --network
docker compose run --rm cli cache flush
docker compose run --rm cli transient delete --all --network
```

## Reset everything

```bash
docker compose down -v   # removes database + WordPress core volumes
```
