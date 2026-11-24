# Marine Sciences: Divi child theme

**Project stage: just getting started (v0.1.0).** Discovery is done and the child theme is
scaffolded. The page build in the Divi Theme Builder is planned in [docs/build-log.md](docs/build-log.md).

Child theme for a fictional department site built with **Divi** on a CampusPress network.

## Why a child theme on a page-builder site?

Most of a Divi site lives in the database (Theme Builder templates, Divi Library layouts,
Theme Customizer settings). A child theme is still worth having:

- **Divi updates can't wipe customizations.** Nothing in `/themes/Divi` is ever edited.
- **Version control for the parts that are code:** accessibility fixes, filters, CSS
  that Divi's UI can't express.
- **One place to look.** The next developer opens this folder and the build log instead
  of hunting through 40 module "Custom CSS" boxes.

## What's in it

| File | Purpose |
|------|---------|
| `style.css` | Child theme header (`Template: Divi`) |
| `functions.php` | Enqueues `assets/css/marine.css` after Divi (priority 20), without re-enqueuing the parent CSS |
| `inc/accessibility.php` | Skip link to `#main-content`. Makes the mobile menu toggle keyboard-operable with `aria-expanded` |
| `inc/divi-tweaks.php` | Hides Divi's unused "Projects" CPT (`et_project_posttype_args`). Loads Campus Directory CSS in the Visual Builder |
| `assets/css/marine.css` | Focus styles, link underlines, touch targets, contrast fix, reduced motion |
| `docs/build-log.md` | Discovery notes, decisions, Theme Builder plan, open questions |

## Requirements

- Divi 4.10+ (parent theme, licensed separately from Elegant Themes; not included here)
- Campus Directory plugin (project 02) for the People page

## Rules for this site

1. Build global header/footer in **Theme Builder**, never per page.
2. Save reusable sections to the **Divi Library** with the naming convention in the build log.
3. Use **Global Colors** only. No one-off hex codes in modules.
4. If a module needs Custom CSS longer than three lines, move it to `marine.css` with a comment.
