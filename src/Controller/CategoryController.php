<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\NotFoundException;
use App\Core\View;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;

final class CategoryController
{
    private const POSTS_PER_PAGE = 6;
    private const SORTS = ['date', 'views'];
    private const DEFAULT_SORT = 'date';

    public function __construct(
        private View $view,
        private CategoryRepository $categories,
        private PostRepository $posts,
    ) {
    }

    public function show(int $id): void
    {
        $category = $this->categories->findById($id);
        if ($category === null) {
            throw new NotFoundException("No category with id=$id");
        }

        $sort = $_GET['sort'] ?? self::DEFAULT_SORT;
        if (!in_array($sort, self::SORTS, true)) {
            $sort = self::DEFAULT_SORT;
        }

        $page = max(1, (int) ($_GET['page'] ?? 1));
        $total = $this->posts->countByCategory($id);
        $totalPages = (int) ceil($total / self::POSTS_PER_PAGE);

        if ($page > max(1, $totalPages)) {
            throw new NotFoundException("No page $page for category id=$id");
        }

        $offset = ($page - 1) * self::POSTS_PER_PAGE;
        $posts = $this->posts->findByCategory($id, $sort, self::POSTS_PER_PAGE, $offset);

        $this->view->render('category.tpl', [
            'category' => $category,
            'page' => $page,
            'totalPages' => $totalPages,
            'sort' => $sort,
            'posts' => $posts,
        ]);
    }
}
