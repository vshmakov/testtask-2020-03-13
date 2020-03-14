<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use App\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ApiController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): iterable
    {
        yield Route::post('/api/product/generate', [$this, 'generateProducts']);

        yield Route::post('/api/order/create', [$this, 'createOrder']);
    }

    public function generateProducts(): Response
    {
        for ($number = 1; $number <= 20; ++$number) {
            $product = new Product();
            $product->setName(sprintf('Product #%s', $number));
            //$number * 100$ in cents
            $product->setPrice($number * 100 * 100);

            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();

        return new Response();
    }

    public function createOrder(): JsonResponse
    {
        return new JsonResponse(['id' => 1]);
    }
}
