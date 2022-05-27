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
    #[Route('/users', name: 'app_collection_user', methods: ['GET'])]
    public function list(UserRepository $userRepository): JsonResponse
    {
        /* faire la pagination , et niveau 3 de Richardson ( negociation ?? ) */
        $users = $userRepository->findBy(['client' => $this->getUser()]);
        return $this->json($users, JsonResponse::HTTP_OK, [], ['groups' => 'list_user']);
    }

    #[Route('/users/{user_id}', name: 'app_item_user', methods: ['GET'])]
    #[Entity('user', options: ['id' => 'user_id'])]
    public function show(User $user): JsonResponse
    {
        if ($user->getClient() !== $this->getUser())
        {
            return $this->json(null, JsonResponse::HTTP_BAD_REQUEST);
        }
        return $this->json($user, JsonResponse::HTTP_OK, [], ['groups' => 'show_user']);
    }

    #[Route('/users', name: 'app_create_user', methods: ['POST'])]
    public function create(
        SerializerInterface $serializer,
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator): JsonResponse
    {
//        $data = $request->toArray();
//        $user = new User();
//        $user->setEmail($data['email']);
//        $user->setFirstName($data['firstName']);
//        $user->setLastName($data['lastName']);

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

    #[Route('/users/{user_id}', name: 'app_delete_user', methods: ['DELETE'])]
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
