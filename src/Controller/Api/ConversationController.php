<?php

namespace App\Controller\Api;

use App\Entity\Conversation;
use App\Entity\UtilisateurConversation;
use App\Repository\ConversationRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Conversation')]
class ConversationController extends AbstractController
{
    #[OA\Get(path: '/api/conversations', summary: 'List conversations')]
    #[OA\Response(response: 200, description: 'Success')]
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

    #[OA\Post(path: '/api/conversations', summary: 'Create conversation')]
    #[OA\Response(response: 201, description: 'Created')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'dateCreation', type: 'string', format: 'date-time'),
                new OA\Property(property: 'utilisateurIds', type: 'array', items: new OA\Items(type: 'integer'))
            ]
        )
    )]
    #[Route('/api/conversations', name: 'api_conversations_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ConversationRepository $conversationRepository, UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $ids = $data['utilisateurIds'] ?? [];

        if (count($ids) < 2) {
            return $this->json(['error' => 'At least two utilisateurs required'], 400);
        }

        $conversation = null;
        if (count($ids) === 2) {
            $conversation = $conversationRepository->findBetweenUsers($ids[0], $ids[1]);
        }

        if (!$conversation) {
            $conversation = new Conversation();
            if (isset($data['dateCreation'])) {
                $conversation->setDateCreation(new \DateTime($data['dateCreation']));
            }

            $entityManager->persist($conversation);
            foreach ($ids as $id) {
                $user = $utilisateurRepository->find($id);
                if ($user) {
                    $uc = new UtilisateurConversation();
                    $uc->setUtilisateur($user);
                    $uc->setConversation($conversation);
                    $entityManager->persist($uc);
                }
            }

            $entityManager->flush();
        }

        $participants = [];
        foreach ($conversation->getUtilisateurConversations() as $uc) {
            $user = $uc->getUtilisateur();
            if ($user) {
                $participants[] = [
                    'id' => $user->getId(),
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                ];
            }
        }

        return $this->json([
            'id' => $conversation->getId(),
            'participants' => $participants,
        ], 201);
    }

    #[OA\Put(path: '/api/conversations/{id}', summary: 'Edit conversation')]
    #[OA\Response(response: 200, description: 'Success')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'dateCreation', type: 'string', format: 'date-time')
            ]
        )
    )]
    #[Route('/api/conversations/{id}', name: 'api_conversations_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Conversation $conversation): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['dateCreation'])) {
            $conversation->setDateCreation(new \DateTime($data['dateCreation']));
        }

        $entityManager->flush();

        return $this->json(['status' => 'Conversation updated']);
    }

    #[OA\Delete(path: '/api/conversations/{id}', summary: 'Delete conversation')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/conversations/{id}', name: 'api_conversations_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Conversation $conversation): JsonResponse
    {
        $entityManager->remove($conversation);
        $entityManager->flush();

        return $this->json(['status' => 'Conversation deleted']);
    }
}
