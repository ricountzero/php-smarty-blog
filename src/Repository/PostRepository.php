<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class PostRepository
{
    public function __construct(private PDO $pdo) {}

    /** @return array<int, list<array<string, mixed>>> */
    public function findLatestByCategory(int $limit): array
    {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT ranked.category_id, ranked.id, ranked.image, ranked.title,
                ranked.description, ranked.views, ranked.published_at
            FROM (
                SELECT pc.category_id, p.id, p.image, p.title, p.description, p.views, p.published_at,
                    ROW_NUMBER() OVER (
                        PARTITION BY pc.category_id
                        ORDER BY p.published_at DESC, p.id DESC
                    ) AS rn
                FROM post_category pc
                JOIN posts p ON p.id = pc.post_id
            ) ranked
            WHERE ranked.rn <= :limit
            ORDER BY ranked.category_id, ranked.rn
            SQL);

        $stmt->execute(['limit' => $limit]);

        $result = [];

        foreach ($stmt->fetchAll() as $post) {
            $result[$post['category_id']][] = $post;
        }

        return $result;
    }

    public function countByCategory(int $categoryId): int
    {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT COUNT(*)
            FROM post_category pc
            WHERE pc.category_id = :category_id
            SQL);
        $stmt->execute(['category_id' => $categoryId]);

        return (int) $stmt->fetchColumn();
    }

    /** @return list<array<string, mixed>> */
    public function findByCategory(int $categoryId, string $sort, int $limit, int $offset): array
    {
        $orderBy = match ($sort) {
            'views' => 'p.views DESC',
            'date' => 'p.published_at DESC',
        };

        $stmt = $this->pdo->prepare(<<<SQL
            SELECT p.id, p.image, p.title, p.description, p.views, p.published_at
            FROM post_category pc
            JOIN posts p ON p.id = pc.post_id
            WHERE pc.category_id = :category_id
            ORDER BY $orderBy, p.id DESC
            LIMIT :limit OFFSET :offset
            SQL);
        $stmt->execute(['category_id' => $categoryId, 'limit' => $limit, 'offset' => $offset]);

        return $stmt->fetchAll();
    }

    /** @return array{id: int, image: string, title: string, description: string, body: string, views: int, published_at: string}|null */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT p.id, p.image, p.title, p.description, p.body, p.views, p.published_at
            FROM posts p
            WHERE p.id = :id
            SQL);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public function incrementViews(int $id): void
    {
        $stmt = $this->pdo->prepare(<<<SQL
            UPDATE posts
            SET views = views + 1
            WHERE id = :id
            SQL);
        $stmt->execute(['id' => $id]);
    }

    /** @return list<array<string, mixed>> */
    public function findRelated(int $postId, int $limit): array
    {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT p.id, p.image, p.title, p.description, p.views, p.published_at
            FROM post_category cur
            JOIN post_category pc ON pc.category_id = cur.category_id AND pc.post_id <> cur.post_id
            JOIN posts p ON p.id = pc.post_id
            WHERE cur.post_id = :post_id
            GROUP BY p.id
            ORDER BY COUNT(*) DESC, p.published_at DESC, p.id DESC
            LIMIT :limit
            SQL);
        $stmt->execute(['post_id' => $postId, 'limit' => $limit]);

        return $stmt->fetchAll();
    }
}
