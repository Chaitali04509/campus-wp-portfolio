#!/usr/bin/env bash
# Provision a local WordPress Multisite network with sample content.
# Usage: docker compose up -d && ./bin/setup.sh
set -euo pipefail

cd "$(dirname "$0")/.."
wp() { docker compose --progress=quiet run --rm cli "$@"; }

echo "Waiting for WordPress files and database..."
for i in {1..30}; do
  if wp core version >/dev/null 2>&1 && wp db check >/dev/null 2>&1; then break; fi
  sleep 2
done

if ! wp core is-installed --network >/dev/null 2>&1; then
  echo "Installing WordPress Multisite (subdirectory)..."
  wp core multisite-install \
    --url="http://localhost" \
    --title="Campus Network (local)" \
    --admin_user=admin --admin_password=admin \
    --admin_email=admin@example.test \
    --skip-email
fi

# The official image regenerates .htaccess for single-site; multisite needs its own rules.
docker compose exec -T wordpress bash -c 'cat > /var/www/html/.htaccess' <<'HTACCESS'
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?wp-admin$ $1wp-admin/ [R=301,L]
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(wp-(content|admin|includes).*) $2 [L]
RewriteRule ^([_0-9a-zA-Z-]+/)?(.*\.php)$ $2 [L]
RewriteRule . index.php [L]
HTACCESS

echo "Installing plugins..."
wp plugin install advanced-custom-fields --activate-network || true
wp plugin activate campus-directory --network

echo "Enabling themes network-wide..."
wp theme enable coastal-lab --network
wp theme enable marine-dept-divi-child --network || true
wp theme activate coastal-lab

echo "Creating sub-sites..."
wp site create --slug=coastal-lab --title="Coastal Ecology Lab" --email=admin@example.test || true
wp site create --slug=marine-sciences --title="Department of Marine Sciences" --email=admin@example.test || true

LAB_URL="http://localhost/coastal-lab/"
wp --url="$LAB_URL" theme activate coastal-lab
wp --url="$LAB_URL" rewrite structure '/%postname%/' --hard

echo "Seeding sample content on the lab site..."
if [ "$(wp --url="$LAB_URL" post list --post_type=research_project --format=count)" = "0" ]; then
  wp --url="$LAB_URL" term create research_area "Salt Marsh Restoration" --slug=salt-marsh-restoration >/dev/null
  wp --url="$LAB_URL" term create research_area "Coastal Sensing" --slug=coastal-sensing >/dev/null

  # Replace WordPress's default widgets with lab contact details.
  wp --url="$LAB_URL" widget reset --all >/dev/null || true
  wp --url="$LAB_URL" widget add text footer-1 --title="Contact" \
    --text="Coastal Ecology Lab · Marine Science Center, Room 200 · coastal-lab@example.edu" >/dev/null || true

  PI=$(wp --url="$LAB_URL" post create --porcelain --post_type=team_member --post_status=publish \
    --post_title="Dr. Avery Lin" --post_content="Avery leads the lab's work on marsh resilience." \
    --meta_input='{"role_group":"pi","position_title":"Associate Professor & Lab Director","email":"a.lin@example.edu","office":"Marine Science Center 210","research_interests":"Salt marsh ecology, sea-level rise, restoration monitoring"}')
  STU=$(wp --url="$LAB_URL" post create --porcelain --post_type=team_member --post_status=publish \
    --post_title="Sam Okafor" --meta_input='{"role_group":"phd","position_title":"PhD Candidate","pronouns":"they/them"}')
  wp --url="$LAB_URL" post create --post_type=team_member --post_status=publish \
    --post_title="Priya Natarajan" --meta_input='{"role_group":"undergrad","position_title":"Undergraduate Researcher"}' >/dev/null

  P1=$(wp --url="$LAB_URL" post create --porcelain --post_type=research_project --post_status=publish \
    --post_title="Living Shorelines Monitoring" \
    --post_content="Five-year monitoring of restored marsh edges along the outer harbor." \
    --meta_input="{\"project_status\":\"active\",\"funding_source\":\"Sample Foundation Grant\",\"plain_summary\":\"We measure how restored marsh edges hold up against storm surge.\",\"lead_investigator\":$PI,\"project_team\":[\"$PI\",\"$STU\"]}")
  P2=$(wp --url="$LAB_URL" post create --porcelain --post_type=research_project --post_status=publish \
    --post_title="Low-Cost Tide Sensors" \
    --meta_input='{"project_status":"completed","plain_summary":"Open-hardware water level sensors that community groups can build for under $60."}')
  # Terms are set separately: WP-CLI runs without a user, so tax_input on create is ignored.
  wp --url="$LAB_URL" post term set "$P1" research_area salt-marsh-restoration --by=slug >/dev/null
  wp --url="$LAB_URL" post term set "$P2" research_area coastal-sensing --by=slug >/dev/null
  wp --url="$LAB_URL" post term set "$PI" research_area salt-marsh-restoration --by=slug >/dev/null

  # Directory people (plugin) with departments.
  wp --url="$LAB_URL" term create cd_department "Marine Sciences" >/dev/null || true
  D1=$(wp --url="$LAB_URL" post create --porcelain --post_type=cd_person --post_status=publish --post_title="Dr. Jordan Reyes" \
    --meta_input='{"cd_job_title":"Professor","cd_email":"j.reyes@example.edu","cd_phone":"(555) 010-0101","cd_office":"Marine Science Center 118"}')
  D2=$(wp --url="$LAB_URL" post create --porcelain --post_type=cd_person --post_status=publish --post_title="Dana Whitfield" \
    --meta_input='{"cd_job_title":"Graduate Program Coordinator","cd_email":"d.whitfield@example.edu"}')
  wp --url="$LAB_URL" post term set "$D1" cd_department marine-sciences --by=slug >/dev/null
  wp --url="$LAB_URL" post term set "$D2" cd_department marine-sciences --by=slug >/dev/null

  # A person on the department site, to show network-wide aggregation.
  DEPT_URL="http://localhost/marine-sciences/"
  wp --url="$DEPT_URL" term create cd_department "Administration" >/dev/null || true
  D3=$(wp --url="$DEPT_URL" post create --porcelain --post_type=cd_person --post_status=publish --post_title="Morgan Ellis" \
    --meta_input='{"cd_job_title":"Department Administrator","cd_email":"m.ellis@example.edu"}')
  wp --url="$DEPT_URL" post term set "$D3" cd_department administration --by=slug >/dev/null

  # Pages + menu.
  HOME_ID=$(wp --url="$LAB_URL" post create --porcelain --post_type=page --post_status=publish --post_title="Coastal Ecology Lab" \
    --post_content="<!-- wp:paragraph --><p>We study how salt marshes and shorelines respond to sea-level rise, and we build tools communities can use to monitor them.</p><!-- /wp:paragraph -->")
  DIR_ID=$(wp --url="$LAB_URL" post create --porcelain --post_type=page --post_status=publish --post_title="Directory" \
    --post_content="<!-- wp:shortcode -->[campus_directory]<!-- /wp:shortcode -->")
  wp --url="$LAB_URL" option update show_on_front page >/dev/null
  wp --url="$LAB_URL" option update page_on_front "$HOME_ID" >/dev/null

  wp --url="$LAB_URL" menu create "Main" >/dev/null
  wp --url="$LAB_URL" menu item add-custom main "Research" "${LAB_URL}research/" >/dev/null
  wp --url="$LAB_URL" menu item add-custom main "People" "${LAB_URL}people/" >/dev/null
  wp --url="$LAB_URL" menu item add-post main "$DIR_ID" >/dev/null
  wp --url="$LAB_URL" menu location assign main primary >/dev/null
fi

echo
echo "Done."
echo "  Network admin: http://localhost/wp-admin/network/   (admin / admin)"
echo "  Lab site:      $LAB_URL"
