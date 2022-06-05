<?php

namespace App\Dto\EntityOutput;

use App\Dto\LinksOutput\ProductLink;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class ProductOutput
{
    /**
     * @OA\Property(type="integer"))
     */
    public int $id;
    public string $brand;
    public string $model;
    public string $reference;
    /**
     * @OA\Property(type="number", format="float"))
     */
    public float $price;
    /**
     * @OA\Property(ref=@Model(type=ProductLink::class))
     */
    public ProductLink $_links;
}
