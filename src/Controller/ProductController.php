<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_collection_product', methods: ['GET'])]
    public function list(ProductRepository $productRepository): JsonResponse
    {
        $products = $productRepository->findAll();
        return $this->json($products, JsonResponse::HTTP_OK);
    }

    #[Route('/products/{id}', name: 'app_item_product', methods: ['GET'])]
    public function show(Product $product): JsonResponse
    {
        return $this->json($product, JsonResponse::HTTP_OK);
    }
}