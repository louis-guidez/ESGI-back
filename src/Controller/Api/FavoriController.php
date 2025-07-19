<?php

namespace App\Controller\Api;

use App\Entity\Annonce;
use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Favori')]
class FavoriController extends AbstractController
{
    #[OA\Post(path: '/api/secure/favoris', summary: 'Add annonce to favoris')]
    #[OA\Response(response: 201, description: 'Created')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            required: ['annonceId'],
            properties: [
                new OA\Property(property: 'annonceId', type: 'integer')
            ]
        )
    )]
    #[Route('/api/secure/favoris', name: 'api_favoris_add', methods: ['POST'])]
    public function add(Request $request, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $annonceId = $data['annonceId'] ?? null;

        if (!$annonceId) {
            return $this->json(['error' => 'annonceId is required'], 400);
        }

        $user = $security->getUser();
        if (!$user instanceof Utilisateur) {
            return $this->json(['error' => 'User not found'], 404);
        }

        $annonce = $entityManager->getRepository(Annonce::class)->find($annonceId);
        if (!$annonce) {
            return $this->json(['error' => 'Annonce not found'], 404);
        }

        $user->addFavori($annonce);
        $entityManager->flush();

        return $this->json(['status' => 'Annonce added to favoris'], 201);
    }
}
