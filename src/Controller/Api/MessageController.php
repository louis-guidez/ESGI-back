<?php

namespace App\Controller\Api;

use App\Entity\Message;
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

//    #[OA\Post(path: '/api/messages', summary: 'Create message')]
//    #[OA\Response(response: 201, description: 'Created')]
//    #[OA\RequestBody(
//        content: new OA\JsonContent(
//            type: 'object',
//            properties: [
//                new OA\Property(property: 'contenu', type: 'string'),
//                new OA\Property(property: 'dateEnvoi', type: 'string', format: 'date-time')
//            ]
//        )
//    )]
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

//    #[OA\Delete(path: '/api/messages/{id}', summary: 'Delete message')]
//    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/messages/{id}', name: 'api_messages_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Message $message): JsonResponse
    {
        $entityManager->remove($message);
        $entityManager->flush();

        return $this->json(['status' => 'Message deleted']);
    }
}
