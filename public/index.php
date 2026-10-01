<?php

declare(strict_types=1);

use App\Core\View;
use App\Core\Router;
use App\Core\NotFoundException;
use App\Core\Db;
use App\Controller\HomeController;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;

require __DIR__ . '/../vendor/autoload.php';

$view = new View();
$router = new Router();

$pdo = Db::connection();

$homeController = new HomeController($view, new CategoryRepository($pdo), new PostRepository($pdo));

$router->add('#^/$#', [$homeController, 'index']);
$router->add('#^/category/(\d+)$#', fn(int $id) => $view->render('placeholder.tpl', ['heading' => "Category #$id"]));
$router->add('#^/post/(\d+)$#', fn(int $id) => $view->render('placeholder.tpl', ['heading' => "Post #$id"]));

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

try {
    $router->dispatch($path);
} catch (NotFoundException) {
    http_response_code(404);
    $view->render('404.tpl');
}
