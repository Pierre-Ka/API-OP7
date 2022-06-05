<?php

namespace App\Dto\EntityOutput;

use App\Dto\LinksOutput\UserItemLink;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class UserItemOutput
{
    /**
     * @OA\Property(type="integer"))
     */
    public int $id;
    public string $firstName;
    public string $lastName;
    public string $email;
    /**
     * @OA\Property(ref=@Model(type=UserItemLink::class))
     */
    public UserItemLink $_links;
}
