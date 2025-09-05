# Repository Guidelines

会話は日本語でする。

## Project Structure & Module Organization
- `app/`: Laravel application code (Controllers, Models, Policies).
- `routes/`: HTTP and API route definitions.
- `resources/`: Blade views, JS/CSS (Vite + Tailwind).
- `database/`: Migrations, seeders, SQLite file for local dev.
- `tests/`: PHPUnit tests (`Feature/`, `Unit/`).
- `public/`: Public assets and entry point.

## Build, Test, and Development Commands
- Install PHP deps: `composer update` (or `composer install`).
- Install JS deps: `npm ci` (or `npm install`).
- Run dev server (assets): `npm run dev` (Vite dev).
- Build assets for prod: `npm run build`.
- Run migrations: `php artisan migrate`.
- Run tests: `php artisan test` (uses in-memory SQLite via `phpunit.xml`).
- Format PHP: `vendor/bin/pint`.

## Coding Style & Naming Conventions
- PHP: PSR-12 via Laravel Pint; 4-space indentation.
- Classes: `StudlyCase` (e.g., `LogController`). Methods/vars: `camelCase`. DB columns: `snake_case`.
- Controllers in `app/Http/Controllers`, requests in `app/Http/Requests`, policies in `app/Policies`.
- Routes use named patterns like `logs.*` (see `routes/`).

## Testing Guidelines
- Framework: PHPUnit with Laravel testing helpers.
- Location: `tests/Feature` for HTTP/route flows; `tests/Unit` for pure units.
- Naming: Test classes end with `Test` (e.g., `LogControllerTest`). Use `/** @test */` or `test_...` methods.
- Run a focused test: `./vendor/bin/phpunit --filter LogControllerTest`.

## Commit & Pull Request Guidelines
- Commits: short, imperative summary; optional emoji/scope (e.g., `🔧 setup: add Pint`).
- Reference issues in body (`Closes #123`) and explain rationale briefly.
- PRs: include clear description, steps to verify, screenshots for UI changes, and any schema changes/migration notes.

## Security & Configuration Tips
- Never commit `.env` or secrets. Copy `./.env.example` to `.env` and set `APP_KEY` (`php artisan key:generate`).
- Local DB: prefer SQLite (`database/database.sqlite`). CI/tests use in-memory SQLite from `phpunit.xml`.
- Markdown rendering is purified (HTMLPurifier) to mitigate XSS; keep third-party libs updated via Composer.
