---
name: laravel-vite-tailwind-debug
description: Diagnose and fix common Tailwind/asset-loading bugs in Laravel + Vite projects (CDN-vs-Vite conflicts, missing tokens, layout scroll issues).
source: auto-skill
extracted_at: '2026-06-25T18:35:34.474Z'
---

# Diagnosing Laravel + Vite + Tailwind Bugs

When a Laravel view renders blank-styled or throws `tailwind is not defined` / `X is not a utility class` errors, walk through these checks in order. The root cause is almost always one of three things.

## 1. Tailwind CDN vs Vite build conflict

**Symptom:** `Uncaught ReferenceError: tailwind is not defined` in console; Tailwind CDN warning `cdn.tailwindcss.com should not be used in production`. Often only appears after a Turbo/Vite HMR navigation, not on first page load.

**Why it happens:** Views pull in `<script src="https://cdn.tailwindcss.com?...">` plus an inline `<script>tailwind.config = {...}</script>`. Vite/Turbo's head-merge logic (`on.copyNewHeadScriptElements`) re-executes the inline config script *before* the CDN has finished exposing the `tailwind` global on subsequent navigations.

**Fix:**
1. Delete the `<script src="https://cdn.tailwindcss.com...">` line and the inline `<script id="tailwind-config">` block.
2. Replace both with `@vite(['resources/css/app.scss', 'resources/js/app.js'])` — same pattern used by `layouts/app.blade.php`.
3. The build pipeline (`tailwind.config.js` → `content: ['./resources/views/**/*.blade.php']`) auto-discovers classes, so no manual registration needed.

## 2. Custom tokens used in views but missing from `tailwind.config.js`

**Symptom:** A class like `p-stack-lg` or `text-headline-md` silently renders as no-op (zero styles). The page loads but spacing/typography looks wrong.

**Audit checklist** — for every Blade view, list non-standard tokens and check them against `tailwind.config.js`:
- `theme.extend.colors` — `primary-container`, `surface-container-lowest`, `on-surface`, `outline-variant`, `error-container` etc. are present if you're following Material 3 palette.
- `theme.extend.spacing` — only `topbar-height` and `sidebar-width` typically exist. Custom names like `stack-sm/md/lg`, `margin-mobile`, `gutter` rarely do.
- `theme.extend.fontSize` / `fontFamily` — names like `headline-md`, `body-md`, `font-label-md` are often MD3-specific and not present.
- Arbitrary values like `rounded-[24px]` work fine in v3+.

**Fix options (in order of preference):**
1. **Add the tokens** to `tailwind.config.js` `theme.extend` if they're project-wide reusable (e.g., MD3 design system).
2. **Map to standard utilities** in the view itself: `gap-stack-sm` → `gap-2`, `gap-stack-md` → `gap-4`, `gap-stack-lg` → `gap-8`, `text-headline-lg` → `text-[28px] leading-9 font-semibold tracking-tight`, `font-body-md text-body-md` → `text-sm leading-5`, `font-label-md text-label-md` → `text-xs font-semibold tracking-wider uppercase`, `p-stack-lg` → `p-8`, `px-margin-mobile` → `px-4`.

## 3. Sidebar/topbar scrolling with content

**Symptom:** On a long page, the sidebar and topbar scroll out of view along with the main content. They should stay pinned.

**Why it happens:** The sidebar was set with `lg:static` — meaning it becomes part of the normal flex flow on desktop and stretches / scrolls with its parent. The topbar's `sticky top-0` works only if its scroll container is set up correctly.

**Fix:** Convert the layout to a two-axis flex model:
- Outer wrapper: `flex min-h-screen w-full`
- Sidebar (`<aside>`):
  - Mobile: `fixed inset-y-0 left-0 z-50 ... lg:hidden` overlay behavior unchanged
  - Desktop: `lg:sticky lg:top-0 lg:h-screen lg:translate-x-0 lg:self-start` + `shrink-0` + `flex flex-col`
  - Inner `<nav>`: add `overflow-y-auto` for long menus
- Main column: `<div class="flex min-w-0 flex-1 flex-col">`
- Topbar (`<header>`): keep `sticky top-0 z-30` + add `shrink-0`
- `<main>`: `class="flex-1"` so it's the only scrolling region; put the content container (`mx-auto max-w-[1440px] px-4 py-6 md:px-6 lg:px-8`) inside it

**Key invariants to preserve:**
- Sidebar must keep `fixed inset-y-0 left-0 z-50` on mobile so the hamburger menu still works.
- Sidebar's `:class` Alpine binding (translate toggle) must stay.
- Toast (`#app-toast`) and confirmation modal (`#confirmOpen`) stay `fixed inset-0 z-50/60` — they're outside the flex layout, don't move them.

## Workflow

When a view shows wrong layout or asset errors:
1. Open DevTools → Console. Note exact error messages and stack traces.
2. Check the view file head for `cdn.tailwindcss.com` and inline `tailwind.config` scripts.
3. Compare non-standard Tailwind classes in the view against `tailwind.config.js` `theme.extend`.
4. Verify the layout's flex/sticky structure if the complaint is about scroll behavior.
5. Fix the most upstream issue first (CDN → build pipeline → class mappings → layout).

## Pre-flight verification

After editing, verify these files exist and are in sync:
- `public/build/manifest.json` (Vite output)
- `node_modules/tailwindcss/` (Tailwind installed)
- `tailwind.config.js` `content` glob covers the view path

Run `npm run build` (or rely on `npm run dev` HMR) to recompile Tailwind so newly-added utility classes are emitted.

## 4. Sidebar covers content (sticky inside flex row doesn't claim space)

**Symptom:** After applying section 3's fix, the sidebar correctly stays pinned on scroll — but now the main content is **hidden underneath the sidebar**, or the content sits flush against the left edge ("condong ke kiri") instead of being offset to the right of the sidebar.

**Why it happens:** Sticky elements behave like `static` for layout purposes in a flex row — they visually stay pinned but **do not claim horizontal space** in the flow. So `<aside class="lg:sticky">` next to `<div class="flex-1">` means the main column gets 100% of the row width, and the sticky sidebar paints on top of the leftmost portion.

**Fix:** Switch the sidebar to `fixed` in **all breakpoints** (mobile and desktop) and add an explicit left margin on the main content wrapper to compensate for the sidebar's width:

- Sidebar: `fixed inset-y-0 left-0 z-50 flex w-64 shrink-0 flex-col ... lg:translate-x-0`
  - Mobile: still off-canvas via the `:class` translate toggle + backdrop overlay.
  - Desktop: `lg:translate-x-0` keeps it permanently visible, `fixed` keeps it pinned without scroll.
- Main column: `<div class="flex min-w-0 flex-1 flex-col lg:ml-64">`
  - The `lg:ml-64` (256px) is the magic that gives the content wrapper enough space to sit *next to* the fixed sidebar.

**Why `lg:ml-64` instead of `lg:pl-64`:** Either works visually, but margin keeps the topbar/header background flush to the viewport edge (cleaner look for full-bleed sticky headers). Padding works too if the design wants a gutter on the main column.

## 5. Tailwind class is written correctly but missing from compiled CSS

**Symptom:** After editing a Blade view, the new utility class (e.g., `lg:ml-64`, `xl:grid-cols-2`) renders as no-op. Build runs successfully, no errors — but grepping the compiled `app-*.css` shows the class is absent.

**Why it happens:** Tailwind's JIT content scanner reads files listed in `tailwind.config.js` `content`. In rare cases — usually with classes added at the same time as a config change, or classes nested inside complex Blade conditionals — the scanner misses them, or the dev server's HMR doesn't trigger a full rebuild.

**Fix — declare the styling explicitly in `app.scss` instead of relying on utility class generation:**

```scss
// resources/css/app.scss
@layer components {
    .app-main {
        flex: 1 1 0%;
        min-width: 0;
        display: flex;
        flex-direction: column;

        @media (min-width: 1024px) {
            margin-left: 16rem; /* 256px = w-64 */
        }
    }
}
```

Then use `class="app-main"` in the layout. The class is **guaranteed** to exist in the bundle because it's literally written in the SCSS source — no JIT scan needed.

**When to reach for this:**
- A layout-critical class that absolutely cannot be missing (sidebar offset, footer alignment, print styles).
- Classes added alongside a major config change (cleanest to verify in one rebuild).
- Anywhere you find yourself writing the same one-off utility more than once.

**When NOT to use it:**
- Normal utility classes used across many views (Tailwind's scanner handles those fine).
- Anything that benefits from hover/focus/responsive variants — keep those as utilities so JIT generates them with proper variant prefixes.