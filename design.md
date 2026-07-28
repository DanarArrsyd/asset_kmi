# Design — STO Asset Inventory

A locked design system for this app. Every page redesign reads this file before
emitting code. Do not regenerate per page — extend or amend this file when the
system needs to grow.

This file defers to `CLAUDE.md` on brand colour, typography, and icon library.
Where the two disagree, `CLAUDE.md` wins and this file must be amended to match.

## Genre

`modern-minimal` — the Stripe / Linear school. Dense, data-first, quiet chrome.
Pure-white surfaces and near-zero-chroma neutrals are allowed under this genre;
they are not a slop finding here.

## Macrostructure family

- **App pages** — *Workbench*. Fixed left rail, sticky top bar, single content
  column capped at `--content-max`. Variation knobs: panel count, toolbar
  presence, table vs. detail-grid body.
- **Auth pages** — *Letter*. Centred narrow card on `--color-bg`, brand mark
  above, no chrome.
- **Print pages** — *Long Document*. No app chrome. Brand mark, subject, rule.

There is no marketing-page family. `/` redirects into the app; this product has
no public surface.

## Theme — `STO-Navy`

Values are exact conversions of the `CLAUDE.md` hex palette. Do not re-tune.

| Token | Value | Hex origin |
|---|---|---|
| `--color-primary` | `oklch(34.3% 0.077 251.6)` | `#163A5F` |
| `--color-primary-hover` | `oklch(40.0% 0.085 249.4)` | `#1E4A73` |
| `--color-primary-active` | `oklch(29.1% 0.064 250.3)` | `#0F2D4A` |
| `--color-bg` | `oklch(97.5% 0.005 258.3)` | `#F5F7FA` |
| `--color-surface` | `oklch(100% 0 0)` | `#FFFFFF` |
| `--color-border` | `oklch(90.9% 0.017 250.9)` | `#D9E2EC` |
| `--color-text` | `oklch(27.8% 0.030 256.8)` | `#1F2937` |
| `--color-text-muted` | `oklch(55.1% 0.023 264.4)` | `#6B7280` |
| `--color-success` | `oklch(62.7% 0.170 149.2)` | `#16A34A` |
| `--color-warning` | `oklch(76.9% 0.165 70.1)` | `#F59E0B` |
| `--color-danger` | `oklch(57.7% 0.215 27.3)` | `#DC2626` |

### Semantic colour — fill vs. ink

`--color-success` / `--color-warning` / `--color-danger` are **fill-only**. At
text sizes they fail WCAG on white (`#F59E0B` on `#FFF` is 2.15:1). Every
semantic *text* or *icon* use must take the `-ink` variant:

- `--color-success-ink` `oklch(45% 0.15 149.2)` — 5.1:1 on surface
- `--color-warning-ink` `oklch(47% 0.12 70.1)` — 5.9:1 on surface
- `--color-danger-ink` `oklch(48% 0.20 27.3)` — 7.0:1 on surface

Rule: **bars, pill backgrounds, and progress fills use the vivid token; every
glyph uses the `-ink` token.**

## Typography

- Display: Inter, weight 600, style **normal**
- Body: Inter, weight 400
- Mono: none — this app has no code surface
- Display tracking: `-0.01em` at `--text-2xl`, `-0.02em` at `--text-display-s`
- Type scale anchor: `--text-display-s` = `2.25rem` (KPI figures only)

Inter as both display and body is a deliberate `CLAUDE.md` lock, not a pairing
failure. Hierarchy is carried by **weight and size**, never by a second family.

All columns of numbers carry `font-variant-numeric: tabular-nums`.

## Spacing

4-point named scale, `--space-3xs` … `--space-3xl`, defined in
`public/css/tokens.css`. Pages must use named tokens (`var(--space-md)`), never
raw values. `--space-3xs` (2px) exists so hairline offsets stay on-scale.

Content column caps at `--content-max` (1400px) and centres. Forms cap at
`--form-max` (720px) or `--form-max-sm` (560px).

## Motion

- Easings: `--ease-out` `cubic-bezier(0.16, 1, 0.3, 1)`, `--ease-in`,
  `--ease-in-out`
- Durations: `--dur-fast` 120ms, `--dur-base` 180ms
- Reveal pattern: **none**. App pages do not animate on scroll or on load.
- Transitions are property-scoped. `transition: all` is banned.
- Reduced-motion fallback: durations collapse to 0.01ms, and any animation whose
  end state hides content (`.form-saved`) is disabled outright so the content
  stays visible.

## Microinteractions stance

- **Silent success** for anything the user can see happened. Inline
  `.form-saved` for form writes; `.form-status` banner for cross-page redirects.
- **Confirmation dialog only for irreversible destructive actions** — asset
  delete, user delete, master-data delete, account delete. Never for reversible
  edits.
- Focus rings appear instantly and are never transitioned.
- Hover is never the only affordance; every hover state has a focus equivalent.
- Non-interactive surfaces do not respond to hover. `.card` (a leaf tile) has no
  hover state; only `a.card` does.

## CTA voice

- Primary: solid `--color-primary` fill, `--color-accent-ink` text,
  `--radius-sm`, `min-height: --control-h` (44px), verb-first Indonesian label.
- Secondary: `--color-surface` fill, `--color-border` hairline, same geometry.
- Danger: solid `--color-danger` fill, reserved for destructive submit only.
- Icon-only: `--control-h-sm` square (40px), always carries `aria-label` and
  `title`; the `<i>` inside is `aria-hidden`.
- Button labels never wrap — `white-space: nowrap` is on `.btn`.

## Containment — one nesting layer

Two container roles, never nested inside each other:

- `.card` — a **leaf tile**. Padded box, no internal structure. KPI figures,
  short stat blocks.
- `.panel` — a **sectioned container**. `.panel__head` / `.panel__body` /
  `.panel__foot`. Tables, forms, detail blocks, history lists.

A `.panel` never contains a `.panel`. A `.card` never contains a `.card`.
Tables live in `.panel__body--flush`.

## Per-page allowances

- App pages **must not** use enrichment. Function carries the page. No hero
  illustration, no abstract background, no decorative SVG.
- Auth pages carry the brand mark only.
- Print pages are typography plus the QR bitmap.

## What pages MUST share

- The brand mark (`public/img/logo.png`) and its chip treatment on dark surfaces.
- The primary colour and its placement — chrome, primary CTA, active nav, data
  bars. Nothing else.
- Inter, and the weight ladder 400 / 500 / 600 / 700.
- The CTA voice above.
- The page rhythm: `breadcrumb → page-head → status → toolbar → panels`.
- The shared partials `partials/page-header` and `partials/table-toolbar`. A list
  page that hand-rolls its own header or toolbar is drift.

## What pages MAY differ on

- Panel count and panel body type (table / detail-grid / form / list).
- Toolbar composition — which filters a list exposes.
- Whether a page has a toolbar at all (detail and form pages do not).

Pages may **not** differ on theme, type, spacing scale, or CTA voice.

## Accessibility floor

- Every interactive element ships default / hover / `:focus-visible` / `:active`
  / `:disabled`.
- Touch targets: 44px for standard controls, 40px for dense icon buttons in
  table rows. Nothing below 40px.
- Body text ≥ 4.5:1; large text, icons, and focus rings ≥ 3:1. See the ink rule
  above.
- Decorative `<i>` icons carry `aria-hidden="true"`. Icon-only controls carry
  `aria-label`.
- Breadcrumbs are a `<nav aria-label="Breadcrumb">`.
- Menu toggles carry `aria-expanded` and keep it in sync.

## Z-index scale

Six named levels, no arbitrary values:

`--z-sticky` 100 · `--z-nav` 200 · `--z-scrim` 250 · `--z-sidebar` 300 ·
`--z-dropdown` 400 · `--z-dialog` 500

## Token discipline

Every colour and every font in `app.css` references a named token. Inline
`oklch()` / hex / `rgb()` outside the `:root` block is banned — including inside
`url("data:image/svg+xml,...")` payloads, which must be lifted into a token
(`--select-arrow`).

Blade templates carry no `style=` attributes. Layout goes through named classes.

## Exports

Drop-in formats for re-using this design system in other projects. This project
names its tokens after its own domain (`--color-bg`, `--color-surface`,
`--color-text`) rather than the generic paper/ink vocabulary; the mapping is
given below.

| Generic | This project |
|---|---|
| `--color-paper` | `--color-bg` |
| `--color-paper-2` | `--color-surface` |
| `--color-ink` | `--color-text` |
| `--color-ink-2` | `--color-text-muted` |
| `--color-rule` | `--color-border` |
| `--color-accent` | `--color-primary` |

### tokens.css

The canonical file is `public/css/tokens.css`. It is the export.

### Tailwind v4 `@theme`

```css
@theme {
  --color-paper:    oklch(97.5% 0.005 258.3);
  --color-paper-2:  oklch(100% 0 0);
  --color-ink:      oklch(27.8% 0.030 256.8);
  --color-ink-2:    oklch(55.1% 0.023 264.4);
  --color-rule:     oklch(90.9% 0.017 250.9);
  --color-accent:   oklch(34.3% 0.077 251.6);
  --font-display:   "Inter", ui-sans-serif, system-ui, sans-serif;
  --font-body:      "Inter", ui-sans-serif, system-ui, sans-serif;
  --spacing-md:     1rem;
  --text-md:        1rem;
  --ease-out:       cubic-bezier(0.16, 1, 0.3, 1);
}
```

### DTCG `tokens.json`

```json
{
  "color": {
    "paper":  { "$value": "oklch(97.5% 0.005 258.3)", "$type": "color" },
    "ink":    { "$value": "oklch(27.8% 0.030 256.8)", "$type": "color" },
    "accent": { "$value": "oklch(34.3% 0.077 251.6)", "$type": "color" }
  },
  "font": {
    "display": { "$value": "Inter", "$type": "fontFamily" },
    "body":    { "$value": "Inter", "$type": "fontFamily" }
  },
  "space": {
    "md": { "$value": "1rem", "$type": "dimension" }
  }
}
```

### shadcn/ui CSS variables

```css
:root {
  --background:         97.5% 0.005 258.3;
  --foreground:         27.8% 0.030 256.8;
  --primary:            34.3% 0.077 251.6;
  --primary-foreground: 100%  0     0;
  --muted:              90.9% 0.017 250.9;
  --muted-foreground:   55.1% 0.023 264.4;
  --border:             90.9% 0.017 250.9;
  --input:              90.9% 0.017 250.9;
  --ring:               40.0% 0.200 251.6;
  --radius:             8px;
}
```
