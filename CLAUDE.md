# Blog in plain PHP + Smarty + MySQL (test assignment)

Original spec: kept locally, not committed. Work plan: `docs/PLAN.md`.

## Stack and constraints
- PHP 8.1+ (code must stay 8.1-compatible), MySQL 8, Smarty 5 via Composer.
- **No frameworks** (Laravel, Symfony, Slim, etc.). Composer is allowed only for Smarty and PSR-4 autoloading.
- Database access via PDO and prepared statements only. No ORM, no query builders.
- Styles in SCSS, compiled into `public/css/`.
- Environment: Docker Compose (php + apache, mysql).

## Structure
- `public/` — document root; single entry point `public/index.php`.
- `src/` — PHP code (namespace `App\`): `Controller/`, `Repository/`, `Core/` (Router, Db, View).
- `templates/` — Smarty templates.
- `database/` — `schema.sql` and the seeder.
- `scss/` — style sources.

## Code rules
- `declare(strict_types=1);` in every file; type all parameters and return values.
- Thin controllers: SQL lives only in repositories.
- Everything rendered in templates is escaped (Smarty `escape_html` enabled).
- Simplicity over "architecture": the author must be able to explain every line in an interview.
- Code, comments, commit messages and docs are written in English.

## Commands
- Start the environment: `docker compose up -d`
- Apply schema and seed: `docker compose exec app php database/seed.php`
- Syntax check: `find src public -name '*.php' -exec php -l {} \;`

## Workflow
- One task from `docs/PLAN.md` at a time; after each task, verify in the browser/curl and commit.
- For larger changes: plan first, then code.
- Items marked `(me)` in `docs/PLAN.md` are written by the author by hand — the AI must not write their code, only explain, review and answer questions.
