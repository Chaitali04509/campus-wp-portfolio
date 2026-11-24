# Pre-launch accessibility checklist (WCAG 2.2 AA)

Run on **every template**, not just the home page: home, a standard page, an archive,
a single post/CPT, search results, 404, and any page with a form.

## Automated (catches about 30–40% of issues)
- [ ] axe DevTools: 0 critical / serious
- [ ] Lighthouse accessibility: 100
- [ ] W3C HTML validator: no duplicate IDs, no unclosed elements

## Keyboard
- [ ] Tab through the whole page. Focus is always visible (2.4.7) and never hidden behind sticky headers (2.4.11)
- [ ] Skip link is the first focusable element and moves focus to main content (2.4.1)
- [ ] Menus, sub-menus, accordions, tabs and modals all work with Tab / Enter / Space / Esc (2.1.1)
- [ ] No keyboard traps (2.1.2)

## Screen reader (VoiceOver on macOS: ⌘F5)
- [ ] One `<h1>` per page. Headings don't skip levels (1.3.1)
- [ ] Landmarks present: banner, navigation (labeled if more than one), main, contentinfo
- [ ] Images: meaningful ones have alt text, decorative ones have `alt=""` (1.1.1)
- [ ] Links make sense out of context, with no bare "Read more" (2.4.4)
- [ ] Dynamic updates (filters, search counts) announced via `role="status"` / `aria-live` (4.1.3)
- [ ] Form fields have visible `<label>`s. Errors are announced and described in text (3.3.1, 3.3.2)

## Visual
- [ ] Text contrast ≥ 4.5:1 (large text ≥ 3:1). UI components and focus rings ≥ 3:1 (1.4.3, 1.4.11)
- [ ] Links in body text are underlined or otherwise distinguishable without color (1.4.1)
- [ ] Zoom to 200%: no loss of content. At 320px width, no horizontal scroll (1.4.4, 1.4.10)
- [ ] Touch targets ≥ 24×24px minimum (2.5.8). We aim for 44×44
- [ ] Nothing auto-plays or moves for more than 5s without a pause control (2.2.2)
- [ ] `prefers-reduced-motion` respected

## Content
- [ ] Page `<title>` is unique and descriptive (2.4.2)
- [ ] `lang` attribute set (3.1.1)
- [ ] PDFs: is there an HTML version of anything important? If not, the PDF must be tagged
- [ ] Videos have captions. Audio has transcripts (1.2.x)
