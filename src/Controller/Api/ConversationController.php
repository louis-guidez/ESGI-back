<?php

namespace App\Controller\Api;

use App\Entity\Conversation;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Conversation')]
class ConversationController extends AbstractController
{
    #[OA\Get(path: '/api/secure/conversations', summary: 'Create conversation')]
    #[Route('/api/secure/conversations', name: 'get_all_conversations', methods: ['GET'])]
    public function getAllConversations(
        Security $security,
        ConversationRepository $conversationRepository
    ): JsonResponse {
        $currentUser = $security->getUser();

        if (!$currentUser) {
            return $this->json(['error' => 'Utilisateur non connecté'], 401);
        }

        // Récupère toutes les conversations où l'utilisateur est A ou B
        $conversations = $conversationRepository->findByUser($currentUser);

        $data = array_map(static function (Conversation $conversation) use ($currentUser) {
            $otherUser = $conversation->getUtilisateurA() === $currentUser
                ? $conversation->getUtilisateurB()
                : $conversation->getUtilisateurA();

            return [
                'conversationId' => $conversation->getId(),
                'with' => [
                    'id' => $otherUser->getId(),
                    'nom' => $otherUser->getNom(),
                    'prenom' => $otherUser->getPrenom(),
                    'email' => $otherUser->getEmail(),
                ],
//                'lastMessage' => $conversation->getMessages()->last()?->getContenu(),
//                'lastDate' => $conversation->getMessages()->last()?->getDateEnvoi()?->format('Y-m-d H:i'),
            ];
        }, $conversations);

        return $this->json($data);
    }


    #[OA\Post(path: '/api/secure/conversations', summary: 'Create conversation')]
    #[OA\Response(response: 201, description: 'Created')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'dateCreation', type: 'string', format: 'date-time')
            ]
        )
    )]
    #[Route('/api/secure/conversations', name: 'api_conversations_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $conversation = new Conversation();
        if (isset($data['dateCreation'])) {
            $conversation->setDateCreation(new \DateTime($data['dateCreation']));
        }

        $entityManager->persist($conversation);
        $entityManager->flush();

        return $this->json(['id' => $conversation->getId()], 201);
    }

    #[OA\Put(path: '/api/secure/conversations/{id}', summary: 'Edit conversation')]
    #[OA\Response(response: 200, description: 'Success')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'dateCreation', type: 'string', format: 'date-time')
            ]
        )
    )]
    #[Route('/api/secure/conversations/{id}', name: 'api_conversations_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Conversation $conversation): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['dateCreation'])) {
            $conversation->setDateCreation(new \DateTime($data['dateCreation']));
        }

        $entityManager->flush();

        return $this->json(['status' => 'Conversation updated']);
    }

    #[OA\Delete(path: '/api/secure/conversations/{id}', summary: 'Delete conversation')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/secure/conversations/{id}', name: 'api_conversations_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Conversation $conversation): JsonResponse
    {
        $entityManager->remove($conversation);
        $entityManager->flush();

        return $this->json(['status' => 'Conversation deleted']);
    }
}
