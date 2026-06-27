---
name: laravel-favicon-apache-404
description: Diagnose why a Laravel app's favicon does not appear in the browser tab even though public/favicon.ico exists and the Blade layout has a <link rel="icon"> tag. Covers browser-cache busting, Apache vhost document-root mismatches, and Laravel route fallback.
source: auto-skill
extracted_at: '2026-06-25T19:38:10.999Z'
---

# Diagnosing Why Favicon.ico Does Not Show in Laravel on Apache/Laragon

Symptom: User adds `<link rel="icon" href="{{ asset('favicon.ico') }}">` to `resources/views/layouts/app.blade.php`, confirms `public/favicon.ico` exists, rebuilds Vite — but the browser tab still shows the old / Internet favicon (often the generic Laravel logo or the previous cached icon).

Walk these checks in order. The fix is almost always one of the items below — not a Blade or Vite issue.

## 1. Confirm the file is actually served, not just present on disk

**Symptom:** `public/favicon.ico` exists with valid content (15 KB, ICO magic bytes `00 00 01 00`), but `curl -sI http://localhost/<project>/favicon.ico` returns `404 Not Found` while `curl -sI http://localhost/<project>/public/favicon.ico` returns `200 OK`.

**Why it happens:** The project is served from a subfolder under Apache's document root (e.g. `http://localhost/sistem-koperasi/`), but Laravel's `public/` folder is *not* the document root — Apache is not configured to forward `/favicon.ico` to `public/favicon.ico`. Laravel's `public/.htaccess` rewrite rules only kick in when requests reach that folder, which they don't, because Apache never forwards them. This is common with Laragon's default `www/` setup when the project is accessed as `localhost/<project>/` instead of via a per-project virtual host.

**Fixes — pick one:**

**(a) Laravel route fallback (works regardless of Apache config):** Add a top-level route in `routes/web.php` that serves the file directly:

```php
Route::get('/favicon.ico', function () {
    $path = public_path('favicon.ico');
    if (!file_exists($path)) {
        abort(404);
    }
    return response()->file($path, [
        'Content-Type' => 'image/x-icon',
        'Cache-Control' => 'public, max-age=86400',
    ]);
});
```

This works **only** if Apache forwards `/favicon.ico` to Laravel. If the project is at `http://localhost/<project>/` and Apache returns 404 for every Laravel path (even `/<project>/index.php`), the route won't help — Apache isn't reaching Laravel at all.

**(b) Alias in Apache vhost (most robust):** Edit the project's Apache vhost (`C:\laragon\etc\apache2\sites-enabled\auto.<project>.conf` on Laragon, or equivalent) and add inside `<VirtualHost>`:

```apache
Alias /favicon.ico "C:/laragon/www/<project>/public/favicon.ico"
```

Then `apachectl restart` (or restart Laragon). This is the cleanest because browser requests `/favicon.ico` at the document root regardless of where the link tag points.

**(c) Mirror the file outside the project:** Copy `public/favicon.ico` to the Apache document root (e.g. `C:\laragon\www\favicon.ico`). Quickest fix, but pollutes folders outside the project and won't survive re-clones.

**Diagnostic command to disambiguate (a) vs (b)/(c):**

```bash
curl -sI http://localhost/<project>/favicon.ico    # → 404 means Apache not forwarding
curl -sI http://localhost/favicon.ico              # → 404 means no alias and no mirror
curl -sI http://localhost/<project>/public/favicon.ico   # → 200 confirms file IS served when path is direct
```

If `/<project>/public/favicon.ico` returns 200 but `/<project>/favicon.ico` returns 404, the issue is purely path-routing — neither (a) nor (b) will help unless Apache starts forwarding to Laravel.

## 2. Browser cache still serving the old icon

**Symptom:** Even after fixing the 404, the browser still shows the old favicon.

**Why it happens:** Browsers cache favicons aggressively, keyed by URL. Adding a query-string version to the link tag is the standard cache-bust:

```blade
<link rel="icon" href="{{ asset('favicon.ico') }}?v=2" type="image/x-icon">
<link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v=2" type="image/x-icon">
```

Bump `?v=N` each time the favicon file changes. Also hard-refresh (Ctrl+Shift+R) and clear the browser's favicon cache (in DevTools → Application → Storage → "Clear site data", or restart the browser).

## 3. The link tag is in a layout that the page does not use

**Symptom:** Favicon doesn't appear on `/login` even though `public/favicon.ico` exists.

**Why it happens:** This project has two layouts — `resources/views/layouts/app.blade.php` (authenticated pages) and `resources/views/layouts/guest.blade.php` (login/register). Adding the `<link rel="icon">` only to `app.blade.php` leaves guest pages without one.

**Fix:** Add the favicon link to every layout the user-facing site uses. Grep to verify:

```bash
grep -l 'x-app-layout\|<x-guest-layout\|@extends' resources/views/**/*.blade.php
```

For each layout referenced, ensure the favicon link is present.

## 4. ICO file is invalid / not actually an ICO

**Symptom:** Tag is correct, file is served (200 OK), but browser shows a broken-image icon or generic fallback.

**Why it happens:** The file is a PNG/other format renamed to `.ico`, or it's corrupted. Browsers will not auto-convert.

**Validation:**

```bash
powershell -Command "Get-Content public\favicon.ico -TotalCount 4 -Encoding Byte | ForEach-Object { '{0:X2}' -f $_ }"
# Valid ICO starts with: 00 00 01 00
# Valid PNG starts with: 89 50 4E 47
```

If the file is a PNG, either rename it back to `favicon.png` and link `<link rel="icon" type="image/png" href="...png">`, or convert it with an actual ICO encoder (`magick convert favicon.png favicon.ico`, or any free online converter that produces multi-resolution ICO).

## Workflow

When a user reports favicon doesn't show:

1. **Confirm file is valid:** check size > 0 and ICO magic bytes (`00 00 01 00`).
2. **Confirm file is served:** `curl -sI` against the actual URL the browser will request. Distinguish between "file exists" and "file is served at this URL".
3. **Check the link tag in every layout** — `app.blade.php`, `guest.blade.php`, any other.
4. **Add cache-bust query string** to all link tags.
5. **If curl returns 404**, pick one of: route fallback in `routes/web.php`, Apache Alias in vhost, or mirror outside project. Whichever matches the project's serving setup.
6. **If curl returns 200** but browser still shows old icon, hard-refresh and clear browser favicon cache.

## What NOT to change

- Do **not** delete `public/favicon.ico` thinking it's the placeholder — it's part of Laravel's default scaffold and harmless to keep.
- Do **not** assume `php artisan view:clear` will refresh the browser — favicon caching is browser-side, not Laravel-side.
- Do **not** add `<link rel="icon">` inside page templates — always put it in the layout's `<head>` so every page inherits it.