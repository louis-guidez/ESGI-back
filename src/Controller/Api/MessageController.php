<?php

namespace App\Controller\Api;

use App\Entity\Message;
use App\Entity\Utilisateur;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Hub;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[OA\Tag(name: 'Message')]
class MessageController extends AbstractController
{

    #[OA\Post(path: '/api/messages', summary: 'Create and publish a new message')]
    #[OA\Response(response: 201, description: 'Message created and broadcasted')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'contenu', type: 'string'),
                new OA\Property(property: 'receiver_id', type: 'integer')
            ]
        )
    )]
    #[Route('/api/messages', name: 'api_messages_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        HttpClientInterface $httpClient,
        ParameterBagInterface $params
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $sender = $this->getUser();
        $receiverId = $data['receiver_id'] ?? null;
        $contenu = $data['contenu'] ?? null;

        if (!$contenu || !$receiverId || !$sender) {
            return $this->json(['error' => 'Invalid data or unauthenticated.'], Response::HTTP_BAD_REQUEST);
        }

        $receiver = $entityManager->getRepository(Utilisateur::class)->find($receiverId);

        if (!$receiver) {
            return $this->json(['error' => 'Receiver not found.'], Response::HTTP_NOT_FOUND);
        }

        $message = new Message();
        $message->setContenu($contenu);
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setDateEnvoi(new \DateTime());

        $entityManager->persist($message);
        $entityManager->flush();

        // Generate topic name
        $topic = 'https://chat.mercure/messages/' . $this->generateTopic($sender->getId(), $receiver->getId());

        // Create publisher and JWT
        $jwt = $params->get('mercure.jwt');
        $mercureUrl = $params->get('mercure.internal_url');

        $publisher = new Publisher($mercureUrl, new StaticTokenProvider($jwt), $httpClient);

        $update = new Update(
            $topic,
            json_encode([
                'id' => $message->getId(),
                'contenu' => $message->getContenu(),
                'dateEnvoi' => $message->getDateEnvoi()->format('Y-m-d H:i:s'),
                'from' => $sender->getId(),
                'to' => $receiver->getId()
            ])
        );

        $publisher($update);

        return $this->json(['status' => 'Message sent', 'id' => $message->getId()], Response::HTTP_CREATED);
    }

    private function generateTopic(int $id1, int $id2): string
    {
        return $id1 < $id2 ? "$id1-$id2" : "$id2-$id1";
    }

    #[OA\Put(path: '/api/messages/{id}', summary: 'Edit message')]
    #[OA\Response(response: 200, description: 'Success')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'contenu', type: 'string'),
                new OA\Property(property: 'dateEnvoi', type: 'string', format: 'date-time')
            ]
        )
    )]
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
