<?php

namespace App\Controller\Api;

use App\Repository\MessageRepository;
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
}
