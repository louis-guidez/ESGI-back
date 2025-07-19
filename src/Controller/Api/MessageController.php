<?php

namespace App\Controller\Api;

use App\Entity\Message;
use App\Entity\Utilisateur;
use App\Repository\MessageRepository;
use App\Repository\ConversationRepository;
use App\Entity\Conversation;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Cookie;
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
    #[Route('/api/secure/messages', name: 'send_message', methods: ['POST'])]
    public function sendMessage(
        Request $request,
        EntityManagerInterface $em,
        HttpClientInterface $http,
        ParameterBagInterface $params,
        Security $security,
        ConversationRepository $conversationRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $sender = $security->getUser();
        if (!$sender) {
            return $this->json(['error' => 'Receiver not found'], 404);
        }
        $receiverId = $data['to'] ?? null;
        $contenu = $data['contenu'] ?? null;

        if (!$contenu || !$receiverId) {
            return $this->json(['error' => 'Missing data'], 400);
        }

        $receiver = $em->getRepository(Utilisateur::class)->find($receiverId);
        if (!$receiver) {
            return $this->json(['error' => 'Receiver not found'], 404);
        }

        $message = new Message();
        $message->setContenu($contenu);
        $message->setSender($sender);
        $message->setReceiver($receiver);
        $message->setDateEnvoi(new \DateTime());

        $conversation = $conversationRepository->findByParticipants($sender, $receiver);
        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->setDateCreation(new \DateTime());
            $conversation->setUtilisateurA($sender);
            $conversation->setDateCreation($receiver);
            $conversation->addMessage($message);
            $em->persist($conversation);
        }

        $message->setConversation($conversation);

        $em->persist($message);
        $em->flush();

        // Mercure
        $topic = 'https://chat.mercure/conversation/' . $this->generateTopic($sender->getId(), $receiver->getId());

        $jwt = $this->createMercureJwt([
            'publish' => ['*'],
        ], $params->get('mercure.jwt'));

        $publisher = new Publisher(
            $params->get('mercure.internal_url'),
            new StaticTokenProvider($jwt),
            $http
        );

        $update = new Update(
            $topic,
            json_encode([
                'id' => $message->getId(),
                'contenu' => $message->getContenu(),
                'from' => $sender->getId(),
                'to' => $receiver->getId(),
                'date' => $message->getDateEnvoi()->format('Y-m-d H:i')
            ])
        );

        try {
            $publisher($update);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => 'Erreur Mercure',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }

        return $this->json([
            'from' => $sender->getId(),
            'to' => $receiver->getId(),
            'contenu' => $contenu,
            'date' => $message->getDateEnvoi()->format('Y-m-d H:i:s'),
            'topic' => $topic,
            'mercure_payload' => json_decode($update->getData(), true),
        ]);

    }

    private function generateTopic(int $id1, int $id2): string
    {
        return $id1 < $id2 ? "$id1-$id2" : "$id2-$id1";
    }

    #[Route('/api/secure/messages/conversation/{id}', name: 'get_conversation_messages', methods: ['GET'])]
    public function getConversationMessages(
        int $id,
        Security $security,
        UtilisateurRepository $utilisateurRepository,
        ConversationRepository $conversationRepository,
        EntityManagerInterface $em,
    ): JsonResponse {
        $currentUser = $security->getUser();

        if (!$currentUser) {
            return $this->json(['error' => 'Utilisateur non connecté'], 401);
        }

        $otherUser = $utilisateurRepository->find($id);
        if (!$otherUser) {
            return $this->json(['error' => 'Utilisateur introuvable'], 404);
        }

        $conversation = $conversationRepository->findByParticipants($currentUser, $otherUser);

            if (!$conversation) {
                $conversation = new Conversation();
                $conversation->setDateCreation(new \DateTime());
                $conversation->setUtilisateurA($currentUser);
                $conversation->setUtilisateurB($otherUser);
                $em->persist($conversation);
                $em->flush();
            }
            $messages = $conversation->getMessages();


        $data = array_map(static function (Message $message) {
            return [
                'id' => $message->getId(),
                'contenu' => $message->getContenu(),
                'from' => $message->getSender()?->getId(),
                'to' => $message->getReceiver()?->getId(),
                'date' => $message->getDateEnvoi()?->format('Y-m-d H:i'),
            ];
        }, $messages->toArray());

        return $this->json($data);
    }


    #[OA\Put(path: '/api/secure/messages/{id}', summary: 'Edit message')]
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
    #[Route('/api/secure/messages/{id}', name: 'api_messages_edit', methods: ['PUT'])]
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
    #[Route('/api/secure/messages/{id}', name: 'api_messages_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Message $message): JsonResponse
    {
        $entityManager->remove($message);
        $entityManager->flush();

        return $this->json(['status' => 'Message deleted']);
    }

    #[Route('/api/secure/mercure/token', name: 'mercure_token')]
    public function mercureToken(Security $security, ParameterBagInterface $params): Response
    {
        $user = $security->getUser();
        $userId = $user->getId();

        $topic = "https://chat.mercure/conversation/{$userId}-*";

        $jwt = $this->createMercureJwt([$topic], $params);

        $response = new Response('JWT Mercure généré');

        $cookie = Cookie::create('mercureAuthorization', "Bearer $jwt")
            ->withHttpOnly(true)
            ->withSecure(true)
            ->withSameSite('Strict')
            ->withPath('/.well-known/mercure');

        if ($_ENV['APP_ENV'] !== 'prod') {
            $cookie = $cookie->withSecure(false);
        }

        $response->headers->setCookie($cookie);

        return $response;
    }

    private function createMercureJwt(array $claims, string $secret): string
    {
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            \Lcobucci\JWT\Signer\Key\InMemory::plainText($secret)
        );

        $now = new \DateTimeImmutable();

        return $config->builder()
            ->issuedAt($now)
            ->expiresAt($now->modify('+1 hour'))
            ->withClaim('mercure', $claims) // ← supporte publish et/ou subscribe
            ->getToken($config->signer(), $config->signingKey())
            ->toString();
    }
}
