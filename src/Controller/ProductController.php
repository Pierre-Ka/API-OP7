<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_list', methods: ['GET'])]
    public function list(Request $request, ProductRepository $productRepository): JsonResponse
    {
        // niveau 3 de Richardson : pour chaque phone mettre un lien -> get item
        $page = $request->query->get('page') ?? 1 ;
        $productsCount = $productRepository->count([]);
        $pageCount = round($productsCount/12);
        if($productsCount % 12 != 0){$pageCount++;}
        if ($pageCount < $request->query->get('page')) {
            $page = 1 ;
        }
        $products = $productRepository->findBy([], ['createdAt' => 'DESC'], 12, ($page-1)*12);

        return $this->json($products, JsonResponse::HTTP_OK, [], ['groups' => 'list_product']);
    }

    #[Route('/products/{id}', name: 'product_show', methods: ['GET'])]
    public function show(Product $product): JsonResponse
    {
        return $this->json($product, JsonResponse::HTTP_OK, [], ['groups' => 'show_product']);
    }
}