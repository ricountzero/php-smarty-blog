<?php

declare(strict_types=1);

use App\Core\View;

require __DIR__ . '/../vendor/autoload.php';

$view = new View();
$view->render('home.tpl', ['heading' => 'Hello from Smarty', 'test' => '<b>test</b>']);
