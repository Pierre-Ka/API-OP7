<?php

namespace App\Controller;

use App\Entity\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ClientController extends AbstractController
{
    #[Route('/clients/{id}', name: 'app_item_client', methods: ['GET'])]
    public function show(Client $client): JsonResponse
    {
        return $this->json($client, JsonResponse::HTTP_OK, [], ['groups' => 'show_client']);
    }
}