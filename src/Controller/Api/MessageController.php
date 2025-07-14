<?php

namespace App\Controller\Api;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Message')]
class MessageController extends AbstractController
{
    #[OA\Get(path: '/api/messages', summary: 'List messages')]
    #[OA\Response(response: 200, description: 'Success')]
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

    #[OA\Post(path: '/api/messages', summary: 'Create message')]
    #[OA\Response(response: 201, description: 'Created')]
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

    #[OA\Put(path: '/api/messages/{id}', summary: 'Edit message')]
    #[OA\Response(response: 200, description: 'Success')]
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

    #[OA\Delete(path: '/api/messages/{id}', summary: 'Delete message')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/messages/{id}', name: 'api_messages_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Message $message): JsonResponse
    {
        $entityManager->remove($message);
        $entityManager->flush();

        return $this->json(['status' => 'Message deleted']);
    }
}
