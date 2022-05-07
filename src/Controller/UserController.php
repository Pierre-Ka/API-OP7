<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    #[Route('/clients/{id}/users', name: 'app_collection_user', methods: ['GET'])]
    public function list(Client $client): JsonResponse
    {
        $users = $client->getUsers();
        return $this->json($users, JsonResponse::HTTP_OK, [], ['groups' => 'list_user']);
    }

    #[Route('/clients/{id}/users/{user_id}', name: 'app_item_user', methods: ['GET'])]
    #[Entity('user', options: ['id' => 'user_id'])]
    public function show(Client $client, User $user): JsonResponse
    {
        if ($user->getClient() !== $client)
        {
            return $this->json(null, JsonResponse::HTTP_BAD_REQUEST);
        }
        return $this->json($user, JsonResponse::HTTP_OK, [], ['groups' => 'show_user']);
    }

    #[Route('/clients/{id}/users', name: 'app_create_user', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, Client $client, ValidatorInterface $validator): JsonResponse
    {
        $data = $request->toArray();
        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setClient($client);
        $errors = $validator->validate($user);
        if($errors->count()>0) {
            return $this->json($errors, JsonResponse::HTTP_BAD_REQUEST);
        }
        $em->persist($user);
        $em->flush();

        return $this->json($user, JsonResponse::HTTP_CREATED, [], ['groups' => 'show_user']);
    }

    #[Route('/clients/{id}/users/{user_id}', name: 'app_delete_user', methods: ['DELETE'])]
    #[Entity('user', options: ['id' => 'user_id'])]
    public function delete(Client $client, User $user, EntityManagerInterface $em): JsonResponse
    {
        if ($user->getClient() !== $client)
        {
            return $this->json(null, JsonResponse::HTTP_BAD_REQUEST);
        }
        $em->remove($user);
        $em->flush();

        return $this->json(null, JsonResponse::HTTP_NO_CONTENT);
    }


}