<?php

declare(strict_types=1);

namespace  App;

use Symfony\Component\HttpFoundation\Response;

final class Controller
{
    public function getRoutes(): array
    {
        return [
            '/api/products/generate' => [$this, 'generateProducts'],
        ];
    }

    public function generateProducts(): Response
    {
        return new Response('Hello products!');
    }
}
