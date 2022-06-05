<?php

namespace App\Dto\EntityOutput;

use App\Dto\LinksOutput\UserListLink;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class UserOutput
{
    /**
     * @OA\Property(type="integer"))
     */
    public int $id;
    public string $firstName;
    public string $lastName;
    public string $email;
    /**
     * @OA\Property(ref=@Model(type=UserListLink::class))
     */
    public UserListLink $_links;
}
