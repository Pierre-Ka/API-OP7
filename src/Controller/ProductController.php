<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Services\DisplayListData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Attributes as OA;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

#[Route('/api')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_list', methods: ['GET'])]
//    #[OA\Response( response: 200,  description: 'Returns the list of all phones', ect... )]
    public function list(Request $request, ProductRepository $productRepository, NormalizerInterface $normalizer, UrlGeneratorInterface $router): JsonResponse
    {
        $page = $request->query->get('page') ?? 1 ;
        $productsCount = $productRepository->count([]);
        $pageCount = round($productsCount/12);
        if($productsCount % 12 != 0){$pageCount++;}
        if ($pageCount < $request->query->get('page')) {
            $page = 1 ;
        }
        $products = $productRepository->findBy([], ['createdAt' => 'DESC'], 12, ($page-1)*12);
        $data = new DisplayListData($normalizer, $router);
        $displayer = $data->create($page, $pageCount, $productsCount, $products);

        return $this->json($displayer, JsonResponse::HTTP_OK, [],
            ['groups' => 'list_product']);
    }

    #[Route('/products/{id}', name: 'product_show', methods: ['GET'])]
    public function show(Product $product): JsonResponse
    {
        return $this->json($product, JsonResponse::HTTP_OK, [], ['groups' => 'show_product']);
    }
}