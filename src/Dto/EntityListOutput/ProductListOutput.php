<?php

namespace App\Dto\EntityListOutput;

use App\Dto\EntityOutput\ProductOutput;
use App\Dto\LinksOutput\PaginationLink;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class ProductListOutput
{
    public int $actual_page ;
    public int $total_pages ;
    public int $total_items ;
    public int $items_per_page ;
    /**
     * @OA\Property(ref=@Model(type=PaginationLink::class))
     */
    public PaginationLink $_links ;
    /**
     * @var array|ProductOutput[]
     * @OA\Property(type="array", @OA\Items(type="object", ref=@Model(type=ProductOutput::class)))
     */
    public $_embedded;
}
