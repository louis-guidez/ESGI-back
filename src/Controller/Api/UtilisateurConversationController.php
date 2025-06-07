<?php

namespace App\Controller\Api;

use App\Repository\UtilisateurConversationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurConversationController extends AbstractController
{
    #[Route('/api/utilisateur-conversations', name: 'api_utilisateur_conversations', methods: ['GET'])]
    public function index(UtilisateurConversationRepository $repository): JsonResponse
    {
        $items = $repository->findAll();

        $data = [];
        foreach ($items as $item) {
            $data[] = [
                'id' => $item->getId(),
                'utilisateurId' => $item->getUtilisateur()?->getId(),
                'conversationId' => $item->getConversation()?->getId(),
            ];
        }

        return $this->json($data);
    }
}
