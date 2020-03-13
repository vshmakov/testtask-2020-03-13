<?php

declare(strict_types=1);

namespace App;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

final class Controller
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getRoutes(): array
    {
        return [
            '/api/products/generate' => [$this, 'generateProducts'],
        ];
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
}
