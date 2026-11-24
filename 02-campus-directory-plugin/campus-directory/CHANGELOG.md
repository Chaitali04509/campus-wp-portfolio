# Changelog: Campus Directory

## [0.6.0]
### Added
- REST endpoint `campus-directory/v1/people`
- Theme template override: `{theme}/campus-directory/directory.php`
- "Clear cache only" button on the network settings page

### Changed
- Cache invalidation moved from deleting transients to a network-wide version number
- People sort by last name ("Dr. Jordan Reyes" sorts under R)

### Fixed
- `restore_current_blog()` now runs in `finally`, so an exception during a site query can
  no longer leave the request on the wrong blog
- Permalinks in network listings pointed at the current site instead of the person's own site

## [0.5.0]
### Added
- Network settings page to choose which sites are included
- Lazy rewrite flush after network activation

## [0.4.0]
### Added
- `cd_person` post type, `cd_department` taxonomy, meta registration, ACF field group
- `[campus_directory]` shortcode with search and department filter
