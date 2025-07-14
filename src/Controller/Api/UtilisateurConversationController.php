<?php

namespace App\Controller\Api;

use App\Entity\UtilisateurConversation;
use App\Repository\UtilisateurConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'UtilisateurConversation')]
class UtilisateurConversationController extends AbstractController
{
    #[OA\Get(path: '/api/utilisateur-conversations', summary: 'List utilisateur conversations')]
    #[OA\Response(response: 200, description: 'Success')]
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

    #[OA\Post(path: '/api/utilisateur-conversations', summary: 'Create utilisateur conversation')]
    #[OA\Response(response: 201, description: 'Created')]
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

    #[OA\Put(path: '/api/utilisateur-conversations/{id}', summary: 'Edit utilisateur conversation')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/utilisateur-conversations/{id}', name: 'api_utilisateur_conversations_edit', methods: ['PUT'])]
    public function edit(EntityManagerInterface $entityManager, UtilisateurConversation $utilisateurConversation): JsonResponse
    {
        // No editable fields available
        $entityManager->flush();

        return $this->json(['status' => 'UtilisateurConversation updated']);
    }

    #[OA\Delete(path: '/api/utilisateur-conversations/{id}', summary: 'Delete utilisateur conversation')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/utilisateur-conversations/{id}', name: 'api_utilisateur_conversations_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, UtilisateurConversation $utilisateurConversation): JsonResponse
    {
        $entityManager->remove($utilisateurConversation);
        $entityManager->flush();

        return $this->json(['status' => 'UtilisateurConversation deleted']);
    }
}
