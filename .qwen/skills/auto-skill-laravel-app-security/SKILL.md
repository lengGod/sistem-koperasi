---
name: laravel-app-security
description: Harden this Laravel 11 koperasi admin app against external access and common attack vectors — secure security headers (CSP, HSTS, X-Frame-Options, Referrer-Policy, Permissions-Policy), forced HTTPS in production, session/cookie hardening, route hiding in production, login throttling, mass-assignment protection on form requests, and the `.env` baseline required for safe deployment on Laragon/Apache.
source: auto-skill
extracted_at: '2026-06-26T00:00:00.000Z'
---

# Securing the Koperasi Laravel App

This skill applies security hardening to the `sistem-koperasi` Laravel 11 + Alpine + Tailwind project. It targets the most likely external-attack surfaces for an admin portal served from Laragon/Apache: hostile browser requests, CSRF on forms, brute-force on `/login`, exposed routes/env, debug leakage, and mass-assignment bugs.

Always apply hardening at three layers — **HTTP headers**, **session/cookie config**, and **`.env` baseline** — then verify with the verification commands at the end. Skip a layer only with a clear reason.

---

## 1. Security headers middleware

Create `app/Http/Middleware/SecurityHeaders.php` that sets the headers below on **every** response. Then register it globally in `bootstrap/app.php` so it cannot be forgotten on new routes.

Headers to set:

| Header | Value | Reason |
|---|---|---|
| `X-Frame-Options` | `SAMEORIGIN` | Blocks clickjacking via `<iframe>`. |
| `X-Content-Type-Options` | `nosniff` | Stops MIME-sniffing of uploaded JS/CSS. |
| `Referrer-Policy` | `strict-origin-when-cross-origin` | Limits referrer leakage to internal links. |
| `Permissions-Policy` | `camera=(), microphone=(), geolocation=(), payment=()` | Disables powerful browser APIs the app does not need. |
| `Cross-Origin-Opener-Policy` | `same-origin` | Mitigates cross-window attacks (Spectre-class). **Only emit when the request origin is trustworthy (HTTPS or `localhost`/`127.0.0.1`/`[::1]`) — otherwise browsers ignore it and print a console warning.** |
| `Cross-Origin-Resource-Policy` | `same-origin` | Stops other origins from embedding assets. **Same trustworthy-origin gate as COOP.** |
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains` | Forces HTTPS for one year on supporting browsers. **Skip this header in `local` env** — Laragon dev uses plain HTTP and HSTS there will break the dev site. |
| `Content-Security-Policy` | see below | Blocks inline script/style injection unless explicitly needed. |

### CSP for this project

The app uses Alpine.js (`x-*` directives are evaluated from inline JS in `resources/js/app.js`) and Tailwind from Vite. It also loads Google Fonts (Inter + Material Symbols Outlined) via `<link rel="stylesheet" href="https://fonts.googleapis.com/...">` in `resources/views/layouts/app.blade.php` and `resources/views/auth/login.blade.php`. The font CSS lives on `fonts.googleapis.com`; the actual font files come from `fonts.gstatic.com`.

The CSP below allows self + Vite/Alpine inline scripts and Tailwind utility classes **plus the Google Fonts hosts**. If you later add external CDNs or analytics, update `script-src`/`style-src`/`img-src`. If you self-host the fonts instead, drop the two `https://fonts.*` origins.

```
default-src 'self';
script-src 'self' 'unsafe-inline' 'unsafe-eval';
style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
style-src-elem 'self' 'unsafe-inline' https://fonts.googleapis.com;
img-src 'self' data: blob:;
font-src 'self' data: https://fonts.gstatic.com;
connect-src 'self';
object-src 'none';
base-uri 'self';
form-action 'self';
frame-ancestors 'self';
```

Notes on this CSP:
- **`script-src 'unsafe-inline' 'unsafe-eval'`** is required by Alpine's runtime. Do not remove them unless you migrate to the Alpine CSP build.
- **`style-src-elem`** is set explicitly because modern browsers distinguish `<style>` (`style-src`) from `<link rel="stylesheet">` (`style-src-elem`); without it, only `style-src` is consulted and external font CSS gets blocked. Both must list `fonts.googleapis.com` for the Google Fonts `<link>` to load.
- **`font-src`** lists `fonts.gstatic.com` (and `data:` for any inline base64 fonts). Without this, the WOFF2 files themselves get blocked even if the CSS loads.
- **`preconnect`** to `https://fonts.googleapis.com` (and `https://fonts.gstatic.com`) does **not** need a CSP directive — it is a connection hint, not a resource fetch.

### Middleware skeleton (Laravel 11 style)

```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = [
            'X-Frame-Options' => 'SAMEORIGIN',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=(), payment=()',
        ];

        if (!app()->environment('local')) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        // COOP and CORP only apply on trustworthy origins (HTTPS, localhost,
        // 127.0.0.1, ::1). On plain-HTTP .test vhosts the browser ignores them
        // and prints a console warning — better to omit than to ship a noisy header.
        $trustworthy = $request->isSecure() || in_array(strtolower((string) $request->getHost()), ['localhost', '127.0.0.1', '[::1]', '::1'], true);
        if ($trustworthy) {
            $headers['Cross-Origin-Opener-Policy'] = 'same-origin';
            $headers['Cross-Origin-Resource-Policy'] = 'same-origin';
        }

        $headers['Content-Security-Policy'] = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
            "style-src 'self' 'unsafe-inline'",
            "img-src 'self' data: blob:",
            "font-src 'self' data:",
            "connect-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ]);

        foreach ($headers as $name => $value) {
            $response->headers->set($name, $value);
        }

        return $response;
    }
}
```

### Register globally in `bootstrap/app.php`

In `->withMiddleware(function (Middleware $middleware): void { ... })` add:

```php
$middleware->append(\App\Http\Middleware\SecurityHeaders::class);
```

Use `append` so it runs *after* other middleware (it wraps the response). Never put this inside a route group — every response, including login/error pages, must carry these headers.

---

## 2. Force HTTPS in production

This Laravel 11 build's `Illuminate\Foundation\Configuration\Middleware` does **not** expose `urlForceScheme()` / `forceScheme()` — reflection confirms only `trustHosts` and `trustProxies` are available. Do not call those methods in `bootstrap/app.php` or the app will crash on boot with `Call to undefined method ...::urlForceScheme()`.

Instead, perform the HTTPS redirect inside the `SecurityHeaders` middleware (added in §1), at the very top of `handle()`, **before** `$response = $next($request);`:

```php
if (! app()->environment('local') && ! $request->isSecure()) {
    return redirect()->secure($request->getRequestUri(), 301);
}
```

`app()->environment('local')` is safe here because the middleware runs at request time (after Laravel is fully booted), unlike inside `withMiddleware()`. **Never enable this redirect in `local`** — Laragon serves plain HTTP and force-scheme will break the dev site and clear `APP_URL`.

---

## 3. Session & cookie hardening in `config/session.php`

Update these values (or override via `.env`):

| Key | Required value | Why |
|---|---|---|
| `secure` | `env('SESSION_SECURE_COOKIE', true)` in production, `false` in local | Cookie only sent over HTTPS in prod. |
| `http_only` | `true` | Cookie not readable from JS — blocks XSS session theft. |
| `same_site` | `'lax'` | Mitigates CSRF on cross-site form posts while keeping normal nav working. |
| `encrypt` | `true` | Encrypted session cookie contents. |
| `lifetime` | `120` (or lower for admin) | Reduces stolen-cookie usefulness. |

`SESSION_SECURE_COOKIE` must be set explicitly to `false` in `APP_ENV=local`. Leaving it `true` locally will silently log users out on every refresh.

---

## 4. Hide routes in production

In `routes/web.php` wrap the debug-only routes (and any `Route::resource(...)->only(['index','show'])` list endpoints exposed publicly) with `if (!app()->environment('production'))`. Critically, do **not** expose:

- `phpinfo()` or `dd()`/`dump()` anywhere reachable by a route.
- `/_debugbar/*` if Debugbar is installed — disable it in production via `DEBUGBAR_ENABLED=false`.
- `Route::get('/telescope/*', ...)` — Telescope must be `TELESCOPE_ENABLED=false` in prod.

Also: add a `Route::fallback(fn () => abort(404))` so unknown URLs do not leak framework errors.

---

## 5. Login throttling (already provided by Breeze/Jetstream, verify it)

`App\Http\Controllers\Auth\AuthenticatedSessionController` ships with Laravel Breeze and already calls `RateLimiter::tooManyAttempts()` and `ensureIsNotRateLimited()` on `store()`. **Do not delete or weaken this.** Verification:

```bash
grep -n "ensureIsNotRateLimited\|throttle:" app/Http/Controllers/Auth/AuthenticatedSessionController.php
```

If using a custom login controller that does **not** throttle, copy the pattern from the Breeze controller verbatim — five failed attempts inside the same IP must return HTTP 429.

---

## 6. Mass-assignment on form requests

Every store/update form request in `app/Http/Requests/*` must `authorize()` and define a strict `rules()` whitelist. Verify:

```bash
grep -L "authorize" app/Http/Requests/*.php
```

Returns nothing — every request file must `authorize()` and return either `true` or a policy check. Public-form requests (e.g. registration) should `return true`; update requests must call `$this->user()->can('update', $this->route('member'))`.

Forbidden patterns:
- `protected $guarded = []` on any model — remove it; rely on `$fillable` instead.
- `request()->all()` passed to `Model::create()` or `->update()` — replace with the validated `$request->validated()`.
- `Validator::make()` inside a controller when a FormRequest class already exists — promote to the FormRequest so the same rules apply to all entry paths.

---

## 7. CSRF on every form

This project already has `@csrf` in all forms (verified across 26 blade matches). Continue this rule for any new form. For AJAX/JSON requests, use the global `csrf-token` meta tag that Breeze sets in `layouts/app.blade.php` — Alpine/Alpine-AJAX calls must read `document.querySelector('meta[name="csrf-token"]').content` and include it as `X-CSRF-TOKEN`.

---

## 8. `.env` baseline for production

`.env.example` ships with insecure defaults for dev. Before deploying or sharing with a teammate, ensure the deployed `.env` has:

```
APP_ENV=production
APP_DEBUG=false
APP_KEY=<generated via php artisan key:generate>
APP_URL=https://your-domain

LOG_CHANNEL=stack
LOG_LEVEL=warning

SESSION_DRIVER=database
SESSION_SECURE_COOKIE=true
SESSION_LIFETIME=120
SANCTUM_STATEFUL_DOMAINS=your-domain

BCRYPT_ROUNDS=12
```

`APP_DEBUG=true` in production prints full SQL + stack traces + env values on every error — the single most common leak. Same for `APP_ENV=local` outside localhost.

---

## 9. Apache / Laragon vhost hardening

When serving behind Laragon Apache, add to the vhost `conf` block:

```
Header always set X-Frame-Options "SAMEORIGIN"
Header always set X-Content-Type-Options "nosniff"
Header always set Referrer-Policy "strict-origin-when-cross-origin"

<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{HTTPS} !=on
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
</IfModule>

<Files ".env">
    Require all denied
</Files>
```

If you only apply headers in Apache, the Laravel middleware is still needed — both layers protect against different attack classes (Apache layer protects assets served directly outside `public/`, Laravel layer protects dynamically generated responses).

---

## Verification commands

Run after applying changes:

```bash
# Headers present on a normal page response
curl -sI http://sistem-koperasi.test/dashboard | grep -iE "x-frame|x-content|referrer|permissions|csp|strict-transport"

# CSRF tokens present in every form view
grep -c "@csrf" resources/views/**/*.blade.php

# No mass-assignment holes
grep -rn "protected \$guarded = \[\]" app/Models/
grep -rn "::create(request()->all\|::create(\$request->all" app/Http/Controllers/

# Login throttling present
grep -n "ensureIsNotRateLimited" app/Http/Controllers/Auth/AuthenticatedSessionController.php

# Debug disabled
php artisan config:show app.debug   # must be false in prod

# HTTPS forced in prod only
grep -n "redirect()->secure\|request()->isSecure" app/Http/Middleware/SecurityHeaders.php
```

All checks must pass before reporting the hardening done.

---

## Common traps

- **Don't set `SESSION_SECURE_COOKIE=true` in local** — Laragon serves HTTP, browsers reject the cookie, and you'll think sessions are broken. Branch on `app()->environment('local')`.
- **Don't remove `'unsafe-inline' 'unsafe-eval'` from CSP** unless you migrate to Alpine CSP build. Alpine evaluates expressions in attribute strings — `unsafe-eval` is mandatory.
- **Don't put the security middleware inside a route group** — use `$middleware->append(...)` in `bootstrap/app.php`. A grouped middleware is the #1 reason "headers are missing on `/login`" tickets appear.
- **Don't trust `Request::all()` for input** — every controller method must take a FormRequest that whitelists fields. A single `request()->all()` is enough for an attacker to flip `is_admin` on a profile-update form.
- **Don't ship `.env`** — it's already in `.gitignore`, but verify with `git check-ignore .env` before committing.