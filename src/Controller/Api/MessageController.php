<?php

namespace App\Controller\Api;

use App\Entity\Message;
use App\Repository\MessageRepository;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/api/messages', name: 'api_messages', methods: ['GET'])]
    public function index(MessageRepository $messageRepository): JsonResponse
    {
        $messages = $messageRepository->findAll();

        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                'id' => $message->getId(),
                'contenu' => $message->getContenu(),
                'dateEnvoi' => $message->getDateEnvoi()?->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/conversations/{user1Id}/{user2Id}/messages', methods: ['GET'])]
    public function messagesBetweenUsers(
        int $user1Id,
        int $user2Id,
        ConversationRepository $conversationRepository,
        MessageRepository $messageRepository
    ): JsonResponse {
        $conversation = $conversationRepository->findConversationBetweenUsers($user1Id, $user2Id);

        if (!$conversation) {
            return $this->json([]);
        }

        $messages = $messageRepository->findBy(
            ['conversation' => $conversation],
            ['dateEnvoi' => 'ASC']
        );

        $data = [];
        foreach ($messages as $message) {
            $data[] = [
                'id' => $message->getId(),
                'contenu' => $message->getContenu(),
                'dateEnvoi' => $message->getDateEnvoi()?->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }

    #[Route('/api/messages', name: 'api_messages_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $message = new Message();
        $message->setContenu($data['contenu'] ?? null);
        if (isset($data['dateEnvoi'])) {
            $message->setDateEnvoi(new \DateTime($data['dateEnvoi']));
        }

        $entityManager->persist($message);
        $entityManager->flush();

        return $this->json(['id' => $message->getId()], 201);
    }

    #[Route('/api/messages/{id}', name: 'api_messages_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Message $message): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $message->setContenu($data['contenu'] ?? $message->getContenu());
        if (isset($data['dateEnvoi'])) {
            $message->setDateEnvoi(new \DateTime($data['dateEnvoi']));
        }

        $entityManager->flush();

        return $this->json(['status' => 'Message updated']);
    }

    #[Route('/api/messages/{id}', name: 'api_messages_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Message $message): JsonResponse
    {
        $entityManager->remove($message);
        $entityManager->flush();

        return $this->json(['status' => 'Message deleted']);
    }
}
