# Git workflow

Small, reviewable changes. `main` is always deployable.

## Branches

| Branch | Purpose |
|--------|---------|
| `main` | Protected. Only updated by merged pull requests. CI must pass |
| `feature/<short-name>` | New functionality, e.g. `feature/directory-pagination` |
| `fix/<short-name>` | Bug fixes, e.g. `fix/card-term-links` |
| `docs/<short-name>` | Documentation only |
| `release/<version>` | Optional: final QA before tagging a release |

## Day-to-day

```bash
git switch main && git pull
git switch -c fix/card-term-links

# ...make changes...
npm run lint:js && npm run lint:css && npm run build   # in the theme folder
git add -p                                             # review each hunk before staging
git commit -m "fix(theme): keep research-area links clickable inside cards"
git push -u origin fix/card-term-links
# open a pull request on GitHub, fill in the template, request review
```

## Commit messages ([Conventional Commits](https://www.conventionalcommits.org/))

```
<type>(<scope>): <what changed, imperative mood>

<optional body: why, and anything a reviewer should know>
```

| type | use for |
|------|---------|
| `feat` | new feature |
| `fix` | bug fix |
| `docs` | documentation only |
| `style` | CSS / formatting, no logic change |
| `refactor` | code change with no behavior change |
| `chore` | build, deps, config |

Scopes in this repo: `theme`, `directory`, `divi`, `docs`, `ci`.

**Examples**
```
feat(directory): add REST endpoint for network people listing
fix(directory): restore_current_blog in finally block
style(divi): restore focus outlines on Divi buttons
docs(theme): record menu and reading settings in site-configuration.md
chore(theme): bump @wordpress/scripts to 30.x
```

## Pull request checklist
- [ ] Linked to an issue, or describes the problem it solves
- [ ] Screenshots before/after for any visual change (desktop + mobile)
- [ ] `CHANGELOG.md` updated under *Unreleased*
- [ ] Accessibility: keyboard tested, no new axe errors
- [ ] Config changes recorded in `docs/site-configuration.md`

## Releases
1. Move *Unreleased* entries under a new version heading in `CHANGELOG.md`
2. Bump the version in `style.css` / plugin header / `package.json`
3. `git tag -a v1.0.0 -m "Coastal Lab 1.0.0" && git push --tags`
4. Create a GitHub Release from the tag and attach the built zip
