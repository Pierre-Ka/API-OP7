<?php

namespace App\Controller;

use App\Dto\EntityListOutput\ProductListOutput;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\DisplayListData;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[Route('/api')]
class ProductController extends AbstractController
{
    #[Route('/products', name: 'product_list', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Return the phones list",
     *     @OA\JsonContent(
     *        type="object",
     *        ref=@Model(type=ProductListOutput::class)
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Pagination system",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Response(
     *     response=404,
     *     description="Return the phones list"
     * )
     * @OA\Response(
     *     response=401,
     *     description="JWT Token not found or expired"
     * )
     * @OA\Response(
     *     response=500,
     *     description="Server Error"
     * )
     * @OA\Tag(name="phone")
     * @Security(name="Bearer")
     */
    public function list(
        Request               $request,
        ProductRepository     $productRepository,
        NormalizerInterface   $normalizer,
        UrlGeneratorInterface $router,
        CacheInterface        $cache
    ): JsonResponse
    {
        $page = $request->query->get('page', 1);

        return $cache->get('list_product' . $page, function (ItemInterface $item)
            use ($page, $request, $productRepository, $normalizer, $router) {
            $item->expiresAfter(3600);
            $productsCount = $productRepository->count([]);
            $pageCount = ceil($productsCount / 12);
            if ($pageCount < $page) {
                $page = 1;
            }
            $products = $productRepository->findBy([], ['createdAt' => 'DESC'], 12, ($page - 1) * 12);
            $displayer = new DisplayListData($normalizer, $router);
            $displayData = $displayer->create($page, $pageCount, $productsCount, $products);

            return $this->json($displayData, JsonResponse::HTTP_OK, [],
                ['groups' => 'list_product']);
        }
        );
    }

    #[Route('/products/{id<\d+>}', name: 'product_show', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Return one phone",
     *     @OA\JsonContent(
     *        type="object",
     *        ref=@Model(type=Product::class)
     *     )
     * )
     * @OA\Response(
     *     response=404,
     *     description="No phone found for this id"
     * )
     * @OA\Response(
     *     response=401,
     *     description="JWT Token not found or expired"
     * )
     * @OA\Response(
     *     response=500,
     *     description="Server Error"
     * )
     * @OA\Tag(name="phone")
     * @Security(name="Bearer")
     */
    public function show(
        Product $product,
        CacheInterface $cache
    ): JsonResponse
    {

        return $cache->get('product' . $product->getId(), function () use ($product) {

            return $this->json($product, JsonResponse::HTTP_OK, [], ['groups' => 'show_product']);
        });
    }
}
