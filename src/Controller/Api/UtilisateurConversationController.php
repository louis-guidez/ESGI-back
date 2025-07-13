<?php

namespace App\Controller\Api;

use App\Entity\UtilisateurConversation;
use App\Repository\UtilisateurConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/api/utilisateur-conversations', name: 'api_utilisateur_conversations_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $item = new UtilisateurConversation();
        // In a real app we would fetch related entities by ID.
        $entityManager->persist($item);
        $entityManager->flush();

        return $this->json(['id' => $item->getId()], 201);
    }

    #[Route('/api/utilisateur-conversations/{id}', name: 'api_utilisateur_conversations_edit', methods: ['PUT'])]
    public function edit(EntityManagerInterface $entityManager, UtilisateurConversation $utilisateurConversation): JsonResponse
    {
        // No editable fields available
        $entityManager->flush();

        return $this->json(['status' => 'UtilisateurConversation updated']);
    }

    #[Route('/api/utilisateur-conversations/{id}', name: 'api_utilisateur_conversations_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, UtilisateurConversation $utilisateurConversation): JsonResponse
    {
        $entityManager->remove($utilisateurConversation);
        $entityManager->flush();

        return $this->json(['status' => 'UtilisateurConversation deleted']);
    }
}
