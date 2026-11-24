# IT support ticket template

A good ticket gets solved on the first reply. Include everything the IT tech would
otherwise have to ask you for.

---

**Subject:** `[Site name] Short description of the problem or request`
_e.g. "[Coastal Lab] Request: 301 redirects for 14 old /projects/ URLs"_

**Type:** Bug · Access request · Plugin/theme install · Configuration change · Question

**Site(s) affected:** full URL(s), e.g. `https://sites.example.edu/coastal-lab/`

**Urgency:** Blocking launch · Affects visitors now · Can wait. Include the date you need it by and why.

**What I'm trying to do:**
One or two sentences on the goal, not only the symptom.

**What happens instead** (bugs only):
- Steps to reproduce: 1… 2… 3…
- Expected:
- Actual:
- Browser/OS where seen:
- Screenshot / console error / exact error message (copy-paste the text, don't paraphrase)

**What I've already checked:**
- e.g. Cleared cache, tested logged-out in a private window, deactivated plugin X on staging, confirmed it's not happening on site Y

**What I need from you:**
Be specific: "Please install release v1.0.2 of the Coastal Lab theme (zip attached) on the network and enable it for /coastal-lab/ only."

**Attachments / links:**
GitHub release, changelog, redirect CSV, screenshots

---

### Example

> **Subject:** [Coastal Lab] Request: 301 redirects for 14 old /projects/ URLs
> **Type:** Configuration change · **Urgency:** Blocking launch, needed by Dec 10
>
> **Goal:** The new theme moves research projects from `/projects/<slug>/` to
> `/research/<slug>/`. Old URLs are linked from grant reports and Google results.
>
> **Need:** 14 permanent (301) redirects, CSV attached (old URL → new URL). Should these
> go in server config, or may I install the Redirection plugin on this site? Happy to do it
> myself if the plugin is approved.
>
> **Checked:** All 14 new URLs return 200 on the current site.
