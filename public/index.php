<?php

declare(strict_types=1);

use App\Controller\CategoryController;
use App\Controller\HomeController;
use App\Controller\PostController;
use App\Core\Db;
use App\Core\NotFoundException;
use App\Core\Router;
use App\Core\View;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;

require __DIR__ . '/../vendor/autoload.php';

$view = new View();

try {
    $router = new Router();

    $pdo = Db::connection();

    $categoryRepository = new CategoryRepository($pdo);
    $postRepository = new PostRepository($pdo);

    $homeController = new HomeController($view, $categoryRepository, $postRepository);
    $categoryController = new CategoryController($view, $categoryRepository, $postRepository);
    $postController = new PostController($view, $categoryRepository, $postRepository);

    $router->add('#^/$#', [$homeController, 'index']);
    $router->add('#^/category/(\d+)$#', [$categoryController, 'show']);
    $router->add('#^/post/(\d+)$#', [$postController, 'show']);

    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
    $router->dispatch($path);
} catch (NotFoundException) {
    http_response_code(404);
    $view->render('404.tpl');
} catch (Throwable $e) {
    http_response_code(500);
    error_log((string) $e);
    $view->render('500.tpl');
}
