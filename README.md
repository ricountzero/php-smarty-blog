# Blog in plain PHP + Smarty + MySQL

A small blog with categories and posts, written without frameworks.

- **Home page**: every category that has posts, with its 3 latest posts and an "All posts" button.
- **Category page**: name, description, list of posts, sorting by date or by views, pagination.
- **Post page**: the full post with its categories, a view counter and 3 related posts.
- **Seeder**: recreates the schema and fills the database with demo categories and posts.

**Stack:** PHP 8.1+ (runs on 8.3 in Docker), MySQL 8, Smarty 5, SCSS (Dart Sass), Docker Compose.

## Getting started

You need Docker with Docker Compose.

```bash
docker compose up -d --build                              # start PHP + Apache and MySQL
docker compose exec -u www-data app composer install      # install Smarty, generate the autoloader
docker compose exec app php database/seed.php             # create tables and demo data
```

Then open http://localhost:8080.

| What | Where |
|---|---|
| Site | http://localhost:8080 |
| MySQL from the host | `localhost:3307`, database `blog`, user `blog`, password `blog` |

Running the seeder again drops all tables and recreates the demo data.

### Styles

Styles are written in SCSS in `scss/` and compiled into `public/css/style.css`. The compiled file is committed, so the site works right after cloning. To rebuild it after changing the SCSS:

```bash
docker compose exec -u www-data app sass scss/style.scss public/css/style.css --no-source-map
```

Add `--watch` to rebuild on every save. The Sass compiler is the standalone Dart Sass binary installed in the `app` image, so Node.js is not required.

## Project structure

```
public/
  index.php              single entry point: wires dependencies, routes the request, handles 404
  css/style.css          compiled styles
src/                     PHP code, namespace App\ (PSR-4)
  Core/
    Db.php               PDO connection (configured from environment variables)
    Router.php           regex routes -> handler, throws NotFoundException if nothing matches
    View.php             thin wrapper around Smarty, HTML escaping enabled
    NotFoundException.php
  Controller/            read request parameters, call repositories, render a template
    HomeController.php
    CategoryController.php
    PostController.php
  Repository/            all SQL lives here
    CategoryRepository.php
    PostRepository.php
templates/               Smarty templates: layout, pages, shared post card, 404
scss/                    style sources (variables, mixins, partials per component)
database/
  schema.sql             tables and indexes
  seed.php               demo data generator
docker/                  Dockerfile and Apache config
```

### Request flow

1. Apache serves existing files (CSS) directly and sends every other request to `public/index.php`.
2. `index.php` creates the PDO connection, repositories and controllers, and registers three routes: `/`, `/category/{id}` and `/post/{id}`.
3. `Router` finds the first matching pattern and calls the controller method with the id from the URL.
4. The controller gets data from repositories and renders a template through `View`.
5. If the route, category, post or page number does not exist, a `NotFoundException` is thrown, and `index.php` responds with 404.

## Database

```
categories (id, name, description)
posts      (id, image, title, description, body, views, published_at)
post_category (category_id, post_id)    -- many-to-many, PRIMARY KEY (category_id, post_id)
```

- A post can belong to several categories, so the link is a separate table with foreign keys and `ON DELETE CASCADE`.
- `posts` has indexes on `views` and `published_at`, the two columns used for sorting.
- The composite primary key of `post_category` starts with `category_id`, so it also works as the index for "posts of a category".

## Design decisions

**No frameworks, minimal "architecture".** There are three layers: router, controllers and repositories. Dependencies are created in one place (`public/index.php`) and passed through constructors. There is no container, no ORM and no query builder.

**SQL only in repositories, always with prepared statements.** All values go through placeholders. `PDO::ATTR_EMULATE_PREPARES` is disabled, so MySQL receives real prepared statements, and integer `LIMIT` and `OFFSET` values can be bound as parameters.

**Sorting through a whitelist.** A column name cannot be bound as a placeholder, so `?sort=` is checked twice:
- the controller accepts only `date` or `views` and falls back to `date` for anything else;
- the repository maps the key to a hardcoded `ORDER BY` fragment with `match`, which throws on unknown values.

User input never becomes part of the SQL text. Every sort also has `id` as a second key, so posts with equal values keep a stable order across pages.

**Pagination.** `LIMIT` / `OFFSET` with a separate `COUNT(*)` query for the total number of pages. Invalid page numbers (`abc`, `-1`) fall back to page 1. A page past the last one returns 404.

**Latest 3 posts per category on the home page.** One query with `ROW_NUMBER() OVER (PARTITION BY category_id ...)` instead of one query per category.

**Related posts.** These are posts that share categories with the current one: more shared categories rank higher, newer posts come first among equal ones. `post_category` is joined with itself:
- one copy holds the categories of the current post;
- the other copy finds other posts in those categories.

After `GROUP BY` on the post, `COUNT(*)` equals the number of shared categories.

**View counter.** The counter is incremented with `UPDATE posts SET views = views + 1`. This is atomic in MySQL, so concurrent requests do not lose views, which a read-modify-write in PHP would. The counter is incremented before the post is loaded, so the page shows the count including the current view. Only `GET` requests are counted: `HEAD` requests (link checkers, monitoring) and `POST` requests do not change the counter.

**One URL per page.** Routes accept ids only without leading zeros (`[1-9]\d*`), so `/post/02` returns 404 instead of duplicating `/post/2`.

**Escaping.** Smarty's `escape_html` is enabled globally: every variable in a template is HTML-escaped. The post body is split into paragraphs with the `split` modifier, and each paragraph is escaped on output. `nl2br` with `nofilter` is not used, because it would turn off escaping.

**Styles.** SCSS with `@use` modules, variables, a breakpoint mixin and BEM-style class names. The layout is mobile-first: posts are shown in 1 column on phones, 2 on tablets and 3 on desktops.

## Known limitations

- Every `GET` page load counts as a view, including reloads and bots. Counting unique visitors (by session or IP) would be a separate feature.
- `OFFSET` pagination gets slower on very deep pages of large tables. For a blog of this size it is not an issue.
- Related posts are calculated on every request, without caching.
- Images are external placeholders from picsum.photos, so the demo needs internet access to show them.
