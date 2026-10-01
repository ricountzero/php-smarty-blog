<?php

declare(strict_types=1);

namespace App\Core;

final class Router
{
    /** @var array<string, callable> */
    private array $routes = [];

    public function add(string $pattern, callable $handler): void
    {
        $this->routes[$pattern] = $handler;
    }

    public function dispatch(string $path): void
    {
        foreach ($this->routes as $pattern => $handler) {
            if (preg_match($pattern, $path, $matches) === 1) {
                $params = array_map('intval', array_slice($matches, 1));
                $handler(...$params);
                return;
            }
        }

        throw new NotFoundException("No route for $path");
    }
}
