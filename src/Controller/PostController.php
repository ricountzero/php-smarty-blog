<?php

declare(strict_types=1);

namespace App\Controller;

use App\Core\NotFoundException;
use App\Core\View;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;

final class PostController
{
    private const RELATED_POSTS = 3;

    public function __construct(
        private View $view,
        private CategoryRepository $categories,
        private PostRepository $posts,
    ) {
    }

    public function show(int $id): void
    {
        $this->posts->incrementViews($id);
        $post = $this->posts->findById($id);
        if ($post === null) {
            throw new NotFoundException("No post with id=$id");
        }

        $categories = $this->categories->findByPost($id);

        $relatedPosts = $this->posts->findRelated($id, self::RELATED_POSTS);

        $this->view->render('post.tpl', [
            'post' => $post,
            'categories' => $categories,
            'relatedPosts' => $relatedPosts,
        ]);
    }
}
