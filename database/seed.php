<?php

declare(strict_types=1);

/**
 * Recreates the schema and fills the database with demo categories and posts.
 *
 * Usage: docker compose exec app php database/seed.php
 */

const POST_COUNT = 40;
const MAX_CATEGORIES_PER_POST = 3;

// The last category intentionally gets no posts: the home page must hide it.
const CATEGORIES = [
    ['PHP', 'News, tips and best practices for modern PHP.'],
    ['Databases', 'MySQL, indexes, query optimization and data modeling.'],
    ['Frontend', 'HTML, CSS, SCSS and everything that runs in the browser.'],
    ['DevOps', 'Docker, deployment, CI/CD and server administration.'],
    ['Career', 'Interviews, growth and life in the software industry.'],
    ['Drafts', 'A category without posts — hidden on the home page.'],
];

const WORDS = [
    'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit',
    'sed', 'do', 'eiusmod', 'tempor', 'incididunt', 'ut', 'labore', 'et', 'dolore',
    'magna', 'aliqua', 'enim', 'ad', 'minim', 'veniam', 'quis', 'nostrud',
    'exercitation', 'ullamco', 'laboris', 'nisi', 'aliquip', 'ex', 'ea', 'commodo',
    'consequat', 'duis', 'aute', 'irure', 'in', 'reprehenderit', 'voluptate',
    'velit', 'esse', 'cillum', 'fugiat', 'nulla', 'pariatur',
];

function connect(): PDO
{
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', getenv('DB_HOST'), getenv('DB_NAME'));

    return new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
}

/**
 * Runs schema.sql statement by statement: a multi-statement exec()
 * would silently ignore errors after the first statement.
 */
function recreateSchema(PDO $pdo): void
{
    $sql = file_get_contents(__DIR__ . '/schema.sql');
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        $pdo->exec($statement);
    }
}

function sentence(int $minWords, int $maxWords): string
{
    $words = [];
    $count = mt_rand($minWords, $maxWords);
    for ($i = 0; $i < $count; $i++) {
        $words[] = WORDS[array_rand(WORDS)];
    }

    return ucfirst(implode(' ', $words)) . '.';
}

function paragraphs(int $count): string
{
    $paragraphs = [];
    for ($i = 0; $i < $count; $i++) {
        $sentences = [];
        for ($j = mt_rand(4, 7); $j > 0; $j--) {
            $sentences[] = sentence(8, 16);
        }
        $paragraphs[] = implode(' ', $sentences);
    }

    return implode("\n\n", $paragraphs);
}

/**
 * @return int[] ids of the inserted categories
 */
function seedCategories(PDO $pdo): array
{
    $insert = $pdo->prepare('INSERT INTO categories (name, description) VALUES (:name, :description)');

    $ids = [];
    foreach (CATEGORIES as [$name, $description]) {
        $insert->execute(['name' => $name, 'description' => $description]);
        $ids[] = (int) $pdo->lastInsertId();
    }

    return $ids;
}

/**
 * @param int[] $categoryIds categories that posts can be attached to
 */
function seedPosts(PDO $pdo, array $categoryIds): void
{
    $insertPost = $pdo->prepare(
        'INSERT INTO posts (image, title, description, body, views, published_at)
         VALUES (:image, :title, :description, :body, :views, :published_at)'
    );
    $insertLink = $pdo->prepare('INSERT INTO post_category (category_id, post_id) VALUES (:category_id, :post_id)');

    for ($i = 1; $i <= POST_COUNT; $i++) {
        $insertPost->execute([
            // picsum.photos returns the same picture for the same seed
            'image' => "https://picsum.photos/seed/post-$i/800/450",
            'title' => rtrim(sentence(3, 7), '.'),
            'description' => sentence(12, 20),
            'body' => paragraphs(mt_rand(3, 6)),
            'views' => mt_rand(0, 5000),
            'published_at' => date('Y-m-d H:i:s', time() - mt_rand(0, 180 * 24 * 3600)),
        ]);
        $postId = (int) $pdo->lastInsertId();

        // array_rand() returns distinct keys, so a post never gets the same category twice
        $keys = (array) array_rand($categoryIds, mt_rand(1, MAX_CATEGORIES_PER_POST));
        foreach ($keys as $key) {
            $insertLink->execute(['category_id' => $categoryIds[$key], 'post_id' => $postId]);
        }
    }
}

// A fixed seed makes every run produce the same texts, views and categories
mt_srand(42);

$pdo = connect();
recreateSchema($pdo);

$pdo->beginTransaction();
$categoryIds = seedCategories($pdo);
seedPosts($pdo, array_slice($categoryIds, 0, -1));
$pdo->commit();

printf("Seeded %d categories and %d posts.\n", count($categoryIds), POST_COUNT);
