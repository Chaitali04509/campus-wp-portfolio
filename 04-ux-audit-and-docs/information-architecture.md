# Information architecture: before & after

## Before (9 top-level items)

```
Home
About
Faculty
Research
Academics
Resources ─┬─ Forms
           ├─ Handbook (PDF)
           └─ Links
Info ──────┬─ Deadlines (PDF)
           └─ Contact
Links
News
```

**Problems**
- "Resources", "Info" and "Links" don't tell users what is inside them. Tree-testing style
  question: *"Where would you find the application deadline?"* has three plausible answers.
- Organized by how the department is structured, not by what visitors are trying to do.
- Nine items wrap to two lines between 768 and 1024px.

## After (5 top-level items + utility bar)

```
Utility bar:  University home · Directory · Give · Search

Programs ────┬─ Graduate (MS, PhD): requirements, deadlines, how to apply
             ├─ Undergraduate minor
             └─ Funding & assistantships
Research ────┬─ Research areas
             ├─ Labs & centers  → links to lab sites on the network
             └─ Facilities
People ──────┬─ Faculty        → [campus_directory department="faculty"]
             ├─ Staff
             └─ Graduate students
Current Students ─┬─ Forms
                  ├─ Handbook
                  └─ Thesis & defense
About ───────┬─ News
             ├─ Events
             └─ Contact & visit
```

## Principles applied

1. **Audience and task labels.** "Current Students" collects everything that was scattered
   across Resources/Info/Links for that audience.
2. **Five plus or minus two top-level items.** Fits on one line at 1024px and is easy to scan on mobile.
3. **The most important task gets the most visible path.** "Programs" is first because
   recruiting graduate students is the site's main goal.
4. **One source of truth.** People pages pull from the Campus Directory, and lab pages link
   out to the lab sites on the network instead of copying their content.
5. **Breadcrumbs on every page below level 1**, so deep links from Google still orient people.

## Redirect map (excerpt)

| Old URL | New URL | Type |
|---------|---------|------|
| `/info/deadlines/` | `/programs/graduate/#deadlines` | 301 |
| `/resources/forms/` | `/current-students/forms/` | 301 |
| `/faculty/` | `/people/faculty/` | 301 |
| `/links/` | `/current-students/` | 301 |
