# Work plan

Each item is a separate commit. Owner: `(me)` — written by hand, `(AI)` — delegated to an AI assistant.

- [x] 1. (AI) Environment: `docker-compose.yml` (php 8.3 + apache, mysql 8), `composer.json` with Smarty and PSR-4 autoload, `public/index.php` prints "Hello".
- [ ] 2. (me) Database schema: `database/schema.sql` — `categories`, `posts` (image, title, description, body, views, published_at), many-to-many `post_category`. Indexes to support sorting.
- [ ] 3. (AI) Seeder: `database/seed.php` — recreates the schema, generates ~5 categories and ~40 posts with placeholder images.
- [ ] 4. (me) Core: `Db` (PDO singleton configured from env), `View` (Smarty wrapper), simple `Router` (`/`, `/category/{id}`, `/post/{id}`, 404).
- [ ] 5. (me) Home page: categories that have posts, the 3 latest posts in each, an "All posts" button.
- [ ] 6. (me) Category page: name, description, post list, sorting (`?sort=views|date`), pagination (`?page=N`).
- [ ] 7. (me) Post page: full post details, view counter increment, block of 3 related posts (by shared categories).
- [ ] 8. (AI) Styles: SCSS → CSS (built in docker or via `sass`), basic responsive layout.
- [ ] 9. (AI) README: how to run, project structure, design decisions.
- [ ] 10. (me + AI review) Final review: `/code-review`, check for SQL injection and XSS, cleanup.
