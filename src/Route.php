<?php

declare(strict_types=1);

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route as BaseRoute;

final class Route extends BaseRoute
{
    public static function post(string $path, callable $controller): self
    {
        return (new self($path, [
            '_controller' => $controller,
        ]))
            ->setMethods([Request::METHOD_POST]);
    }

    public static function put(string $path, callable $controller): self
    {
        return (new self($path, [
            '_controller' => $controller,
        ]))
            ->setMethods([Request::METHOD_PUT]);
    }
}
