<?php

declare(strict_types=1);

use App\Core\View;
use App\Core\Router;
use App\Core\NotFoundException;

require __DIR__ . '/../vendor/autoload.php';

$view = new View();
$router = new Router();

$router->add('#^/$#', fn() => $view->render('home.tpl', ['heading' => 'Home']));
$router->add('#^/category/(\d+)$#', fn(int $id) => $view->render('home.tpl', ['heading' => "Category #$id"]));
$router->add('#^/post/(\d+)$#', fn(int $id) => $view->render('home.tpl', ['heading' => "Post #$id"]));

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';

try {
    $router->dispatch($path);
} catch (NotFoundException) {
    http_response_code(404);
    $view->render('404.tpl');
}
