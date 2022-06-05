<?php

namespace App\Controller;

use App\Dto\EntityListOutput\UserListOutput;
use App\Dto\EntityOutput\CreateUserOutput;
use App\Dto\EntityOutput\UserItemOutput;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\DisplayListData;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[Route('/api')]
class UserController extends AbstractController
{
    #[Route('/users', name: 'user_list', methods: ['GET'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Return the users list",
     *     @OA\JsonContent(
     *        type="object",
     *        ref=@Model(type=UserListOutput::class)
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Pagination system",
     *     @OA\Schema(type="integer")
     * )
     *  @OA\Response(
     *     response=404,
     *     description="No Users yet on the list"
     * )
     *  @OA\Response(
     *     response=401,
     *     description="JWT Token not found or expired"
     * )
     *  @OA\Response(
     *     response=500,
     *     description="Server Error"
     * )
     * @OA\Tag(name="user")
     * @Security(name="Bearer")
     */
    public function list(
        Request               $request,
        UserRepository        $userRepository,
        NormalizerInterface   $normalizer,
        UrlGeneratorInterface $router,
        CacheInterface        $cache
    ): JsonResponse
    {
        $page = $request->query->get('page') ?? 1 ;
//        $result = $cache->get('list_user'.$page, function(ItemInterface $item) use($page, $request, $userRepository, $normalizer, $router)
//        {
//            $item->expiresAfter(3600);
        $usersCount = $userRepository->count(['client' => $this->getUser()]);
        $pageCount = round($usersCount/12);
        if($usersCount % 12 != 0){$pageCount++;}
        if ($pageCount < $request->query->get('page')) {
            $page = 1 ;
        }
        $users = $userRepository->findBy(['client' => $this->getUser()], ['id' => 'ASC'], 12, ($page-1)*12);
        $displayer = new DisplayListData($normalizer, $router);
        $displayData = $displayer->create($page, $pageCount, $usersCount, $users);
//            return $displayData;
//        });
        return $this->json($displayData, JsonResponse::HTTP_OK, [], ['groups' => 'list_user']);
    }

    #[Route('/users/{user_id<[0-9]+>}', name: 'user_show', methods: ['GET'])]
    #[Entity('user', options: ['id' => 'user_id'])]
    /**
     * @OA\Response(
     *     response=200,
     *     description="Return one user",
     *     @OA\JsonContent(
     *        type="object",
     *        ref=@Model(type=UserItemOutput::class)
     *     )
     * )
     *  @OA\Response(
     *     response=404,
     *     description="No Users yet on the list"
     * )
     *  @OA\Response(
     *     response=403,
     *     description="Access Forbidden"
     * )
     *  @OA\Response(
     *     response=401,
     *     description="JWT Token not found or expired"
     * )
     *  @OA\Response(
     *     response=500,
     *     description="Server Error"
     * )
     * @OA\Tag(name="user")
     * @Security(name="Bearer")
     */
    public function show(User $user, CacheInterface $cache): JsonResponse
    {
        $this->denyAccessUnlessGranted('view', $user);
        return $cache->get('user'.$user->getId(), function() use($user){
            return $this->json($user, JsonResponse::HTTP_OK, [], ['groups' => 'show_user']);
        });
    }

    #[Route('/users', name: 'user_create', methods: ['POST'])]
    /**
     * @OA\Response(
     *     response=201,
     *     description="User has been created successfully",
     *     @OA\JsonContent(
     *        type="object",
     *        ref=@Model(type=UserItemOutput::class)
     *     )
     * )
     * @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *        required={"email", "firstName", "lastName"},
     *        type="object",
     *        ref=@Model(type=CreateUserOutput::class)
     *      )
     * )
     *  @OA\Response(
     *     response=404,
     *     description="No Users yet on the list"
     * )
     *  @OA\Response(
     *     response=401,
     *     description="JWT Token not found or expired"
     * )
     *  @OA\Response(
     *     response=500,
     *     description="Server Error"
     * )
     * @OA\Tag(name="user")
     * @Security(name="Bearer")
     */
    public function create(
        SerializerInterface    $serializer,
        Request                $request,
        EntityManagerInterface $em,
        ValidatorInterface     $validator
    ): JsonResponse
    {
        $externalData = $request->getContent();
        $user = $serializer->deserialize($externalData, User::class, 'json');
        $user->setClient($this->getUser());
        $errors = $validator->validate($user);
        if($errors->count()>0) {
            return $this->json($errors, JsonResponse::HTTP_BAD_REQUEST);
        }
        $em->persist($user);
        $em->flush();
        $location = $this->generateUrl('user_show', ['user_id'=> $user->getId()]);

        return $this->json($user, JsonResponse::HTTP_CREATED, ['Location'=>$location], ['groups' => 'show_user']);
    }

    #[Route('/users/{user_id<[0-9]+>}', name: 'user_delete', methods: ['DELETE'])]
    #[Entity('user', options: ['id' => 'user_id'])]
    /**
     * @OA\Response(
     *     response=204,
     *     description="User has been deleted successfully",
     * )
     *  @OA\Response(
     *     response=404,
     *     description="User not found"
     * )
     *  @OA\Response(
     *     response=401,
     *     description="JWT Token not found or expired"
     * )
     *  @OA\Response(
     *     response=500,
     *     description="Server Error"
     * )
     * @OA\Tag(name="user")
     * @Security(name="Bearer")
     */
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('delete', $user);
        $em->remove($user);
        $em->flush();

        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
