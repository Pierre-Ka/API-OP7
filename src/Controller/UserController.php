<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/client/{id}/users', name: 'app_collection_user', methods: ['GET'])]
    public function list(UserRepository $userRepository, Client $client): JsonResponse
    {
        $users = $userRepository->findBy([ "client" => $client ]);
        return $this->json($users, JsonResponse::HTTP_OK);
    }

    #[Route('/client/{id}/users/{user_id}', name: 'app_item_user', methods: ['GET'])]
//    #[Entity('user', expr: 'repository.find(user_id)')]
    #[Entity('user', options: ['id' => 'user_id'])]
    public function show(Client $client, User $user): JsonResponse
    {
        return $this->json($user, JsonResponse::HTTP_OK);
    }

    #[Route('/client/{id}/users', name: 'app_create_user', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, Client $client): JsonResponse
    {
        $data = $request->toArray();
        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setClient($client);
        $em->persist($user);
        $em->flush();

        return $this->json($user, JsonResponse::HTTP_CREATED);
    }

    #[Route('/client/{id}/users/{user_id}', name: 'app_delete_user', methods: ['DELETE'])]
//    #[Entity('user', expr: 'repository.find(user_id)')]
    #[Entity('user', options: ['id' => 'user_id'])]
    public function delete(Client $client, User $user, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($user);
        $em->flush();

        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }


}