<?php

namespace App\Controller\Api;

use App\Entity\Conversation;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/api/conversations', name: 'api_conversations_new', methods: ['POST'])]
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

    #[Route('/api/conversations/{id}', name: 'api_conversations_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Conversation $conversation): JsonResponse
    {
        $entityManager->remove($conversation);
        $entityManager->flush();

        return $this->json(['status' => 'Conversation deleted']);
    }
}
