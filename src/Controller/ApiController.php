<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ApiController
{
    private Request $request;

    private EntityManagerInterface $entityManager;

    private FormFactoryInterface $formFactory;

    public function __construct(Request $request, EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->request = $request;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
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
        $products = $this->request
            ->request
            ->get('products');

        if (!\is_array($products)) {
            throw new BadRequestHttpException('Products array is not specified');
        }

        if (empty($products)) {
            throw new BadRequestHttpException('Order must have products');
        }

        $order = new Order();

        foreach ($products as $productId) {
            $product = $this->entityManager
                ->getRepository(Product::class)
                ->find($productId);

            if (null === $product) {
                throw new NotFoundHttpException('Product not found');
            }

            $order->addProduct($product);
        }

        $this->entityManager->persist($order);
        $this->entityManager->flush();

        return new JsonResponse(['id' => $order->getId()]);
    }
}
