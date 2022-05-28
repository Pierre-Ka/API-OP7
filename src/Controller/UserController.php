<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class UserController extends AbstractController
{
    #[Route('/users', name: 'user_list', methods: ['GET'])]
    public function list(Request $request, UserRepository $userRepository): JsonResponse
    {
        $page = $request->query->get('page') ?? 1 ;
        $usersCount = $userRepository->count(['client' => $this->getUser()]);
        $pageCount = round($usersCount/12);
        if($usersCount % 12 != 0){$pageCount++;}
        if ($pageCount < $request->query->get('page')) {
            $page = 1 ;
        }
        $users = $userRepository->findBy(['client' => $this->getUser()], ['id' => 'ASC'], 12, ($page-1)*12);
        return $this->json($users, JsonResponse::HTTP_OK, [], ['groups' => 'list_user']);
    }

    #[Route('/users/{user_id}', name: 'user_show', methods: ['GET'])]
    #[Entity('user', options: ['id' => 'user_id'])]
    public function show(User $user): JsonResponse
    {
        if ($user->getClient() !== $this->getUser())
        {
            return $this->json(null, JsonResponse::HTTP_BAD_REQUEST);
        }
        return $this->json($user, JsonResponse::HTTP_OK, [], ['groups' => 'show_user']);
    }

    #[Route('/users', name: 'user_create', methods: ['POST'])]
    public function create(
        SerializerInterface $serializer,
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator): JsonResponse
    {
        $externalData = $request->getContent();
        try {
            $user = $serializer->deserialize($externalData, User::class, 'json');
            $user->setClient($this->getUser());
            $errors = $validator->validate($user);
            if($errors->count()>0) {
                return $this->json($errors, JsonResponse::HTTP_BAD_REQUEST);
            }
            $em->persist($user);
            $em->flush();

            return $this->json($user, JsonResponse::HTTP_CREATED, [], ['groups' => 'show_user']);
        }
        catch (NotEncodableValueException $e) {

            return $this->json([
                'statut' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    #[Route('/users/{user_id}', name: 'user_delete', methods: ['DELETE'])]
    #[Entity('user', options: ['id' => 'user_id'])]
    public function delete(User $user, EntityManagerInterface $em): JsonResponse
    {
        if ($user->getClient() !== $this->getUser())
        {
            return $this->json(null, JsonResponse::HTTP_BAD_REQUEST);
        }
        $em->remove($user);
        $em->flush();

        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
