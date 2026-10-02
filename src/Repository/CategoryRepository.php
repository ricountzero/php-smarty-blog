<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class CategoryRepository
{
    public function __construct(private PDO $pdo) {}

    /** @return list<array{id: int, name: string, description: ?string}> */
    public function findWithPosts(): array
    {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT c.id, c.name, c.description
            FROM categories c
            WHERE EXISTS (
                SELECT 1 FROM post_category pc WHERE pc.category_id = c.id
            )
            ORDER BY c.name
            SQL);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /** @return array{id: int, name: string, description: ?string}|null */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT c.id, c.name, c.description
            FROM categories c
            WHERE c.id = :id
            SQL);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }
}
