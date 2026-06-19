# Golem15.Backend

Golem15-branded backend for Winter CMS. Replaces the stock backend login page with a
Golem15-branded auth layout, ships sensible brand defaults (app name, tagline, colours,
logo, favicon, menu mode), and keeps a custom LESS theme for the backend UI in sync with
the Winter `BrandSetting`. Drop it in and the admin panel is branded — no manual brand
configuration required. Requires PHP 8.4+.

## Features

- **Branded login layout** — Extends the core `Backend\Controllers\Auth` controller to
  swap the login/auth screen layout for a Golem15-branded one (`views/layouts/auth`).
- **Brand defaults** — Merges `config/brand.php` defaults into the `brand` config namespace:
  app name, tagline, primary/secondary/accent colours, logo and favicon paths, and the
  inline menu mode.
- **Backend LESS theme** — A `customLessPath`-pointed LESS stylesheet
  (`assets/less/backend.less`) themes the backend chrome (top nav, sidebar, buttons,
  form inputs, tabs, lists, flash messages, scrollbars) on-brand.
- **Automatic style sync** — On each backend request the plugin compares the LESS file's
  modified time against a cached timestamp and writes the file into `BrandSetting.custom_css`
  only when it actually changed, so editing the LESS file updates the live backend without a
  manual publish step and without a DB write on every request.
- **Backend-only boot** — All branding work runs only when the request is in the backend
  (`runningInBackend()`), so it adds nothing to front-end requests.

## Installation

This is a Winter CMS plugin. Install it into the plugins tree and run migrations:

```bash
# place the plugin at plugins/golem15/backend (e.g. as a git submodule)
php artisan winter:up
```

There is no console interaction required — the brand defaults and the LESS theme apply
automatically on the next backend request.

### Dependencies

This plugin has no external Composer dependencies and declares no required Winter plugins
(`$require` is empty). It only uses Winter CMS core (`Backend\Controllers\Auth`,
`Backend\Models\BrandSetting`) and the Winter Storm `File` facade.

## Customisation

- Edit `config/brand.php` to change the default app name, tagline, colours, logo, or favicon.
  These are merged as defaults — a deployment can still override them via the backend
  Branding settings.
- Edit `assets/less/backend.less` to restyle the backend chrome; the change is picked up on
  the next backend request via the mtime-based sync.

## License

Released under the **[MIT License](LICENSE.md)** — © 2026 Jakub Zych / Golem15. Part of the
Golem15 Winter CMS stack.
