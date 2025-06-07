<?php

namespace App\Controller\Api;

use App\Repository\ConversationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ConversationController extends AbstractController
{
    #[Route('/api/conversations', name: 'api_conversations', methods: ['GET'])]
    public function index(ConversationRepository $conversationRepository): JsonResponse
    {
        $conversations = $conversationRepository->findAll();

        $data = [];
        foreach ($conversations as $conversation) {
            $data[] = [
                'id' => $conversation->getId(),
                'dateCreation' => $conversation->getDateCreation()?->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }
}
