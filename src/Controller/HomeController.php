<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\View;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;

final class HomeController
{
    private const LATEST_POSTS_PER_CATEGORY = 3;

    public function __construct(
        private View $view,
        private CategoryRepository $categories,
        private PostRepository $posts,
    ) {
    }

    public function index(): void
    {
        $categories = $this->categories->findWithPosts();
        $postsByCategory = $this->posts->findLatestByCategory(self::LATEST_POSTS_PER_CATEGORY);
        $this->view->render('home.tpl', [
            'categories' => $categories,
            'postsByCategory' => $postsByCategory,
        ]);
    }
}
