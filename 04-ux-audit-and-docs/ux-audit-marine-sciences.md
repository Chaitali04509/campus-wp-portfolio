# UX audit: Department of Marine Sciences website

**Scope:** Home, Graduate Programs, People, Research, Contact (the five sections that
account for most traffic).
**Method:** Nielsen's 10 usability heuristics, a task walk-through for three primary
audiences, a WCAG 2.2 AA spot check (axe DevTools + keyboard-only + VoiceOver), and a
mobile check at 375px.
**Severity scale:** 3 = blocks a primary task · 2 = slows users down / causes errors · 1 = cosmetic

---

## 1. Audiences and top tasks

| Audience | Top task | Success today? |
|----------|----------|----------------|
| Prospective grad student | Find application deadline and requirements | ❌ Deadline is in a PDF linked from paragraph 4 of "Resources" |
| Prospective grad student | Find a faculty advisor in their area | ⚠️ Possible, but faculty list is alphabetical with no research areas |
| Current student | Find the form for a thesis committee change | ⚠️ Under "Info → Links → Forms", 3 clicks, ambiguous labels |
| Collaborator / press | Find who works on a topic + contact details | ❌ Emails are images (not selectable or readable by screen readers) |

## 2. Findings

| # | Finding | Heuristic / WCAG | Sev | Recommendation |
|---|---------|------------------|-----|----------------|
| F1 | Application deadline only in a PDF | Visibility of system status; 2.4.4 | **3** | Put deadlines in HTML on the Graduate Programs page, at the top, as a short table. Keep the PDF as a secondary download |
| F2 | Top nav labels "Resources", "Info", "Links" overlap | Match between system & real world | **3** | Replace with audience/task labels (see IA doc) |
| F3 | Faculty emails rendered as images | 1.1.1 Non-text content | **3** | Use Campus Directory plugin: real text, `mailto:`, obfuscated with `antispambot()` |
| F4 | No skip link. Focus outline removed on menu & buttons | 2.4.1, 2.4.7 | **3** | Child theme adds skip link and focus-visible styles (done in v0.1.0) |
| F5 | Mobile menu toggle not keyboard-operable | 2.1.1 Keyboard | **3** | Add `role="button"`, `tabindex`, Enter/Space, `aria-expanded` (done in v0.1.0) |
| F6 | Faculty list can't be filtered by research area | Flexibility & efficiency | 2 | Department taxonomy + keyword search in directory |
| F7 | 17 orphaned and 41 stale pages show in site search | Aesthetic & minimalist design | 2 | Content audit: archive or redirect. Owner sign-off per page |
| F8 | Body text #666 on #f7f7f7 = 5.2:1, but #999 captions = 2.6:1 | 1.4.3 Contrast | 2 | Captions to #4a5a65 (7.0:1) |
| F9 | Links distinguished only by color in body copy | 1.4.1 Use of color | 2 | Underline links in text modules |
| F10 | Hero slider auto-advances every 4s, no pause | 2.2.2 Pause, stop, hide | 2 | Replace slider with a single static hero. Sliders get very few clicks after slide 1 |
| F11 | Page titles all "Marine Sciences – University" | 2.4.2 Page titled | 1 | Use `Page – Department – University` pattern |
| F12 | Inconsistent button styles (6 variants) | Consistency & standards | 1 | Two button styles as Divi global presets |

## 3. Prioritized plan

| Phase | Items | Effort | Impact |
|-------|-------|--------|--------|
| **Now** (week 1) | F4, F5 (done), F8, F9, F11 | Small, all CSS/child theme | Fixes all keyboard/contrast blockers |
| **Next** (weeks 2–3) | F1, F2, F3, F6 | Medium, IA change + directory | Fixes the two failing top tasks |
| **Later** (weeks 4+) | F7, F10, F12 | Needs content-owner decisions | Cleanup, consistency |

## 4. How we'll know it worked

- Grad coordinator tracks "how do I apply" emails for 6 weeks before/after launch
- Five-person task test (find deadline, find advisor, find form) with success rate and time on task
- axe DevTools: 0 critical/serious issues on the five templates
- Site search: no stale pages in the top 10 results for "apply", "deadline", "forms"
