<?php

namespace App\Controller\Api;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Utilisateur')]
class UtilisateurController extends AbstractController
{
    #[OA\Get(path: '/api/utilisateurs', summary: 'List utilisateurs')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/utilisateurs', name: 'api_utilisateurs', methods: ['GET'])]
    public function index(UtilisateurRepository $utilisateurRepository): JsonResponse
    {
        $utilisateurs = $utilisateurRepository->findAll();

        $data = [];
        foreach ($utilisateurs as $utilisateur) {
            $data[] = [
                'id' => $utilisateur->getId(),
                'email' => $utilisateur->getEmail(),
                'roles' => $utilisateur->getRoles(),
                'nom' => $utilisateur->getNom(),
                'prenom' => $utilisateur->getPrenom(),
                'dateInscription' => $utilisateur->getDateInscription()?->format('Y-m-d H:i:s'),
                'cagnotte' => $utilisateur->getCagnotte(),
                'emailIsVerified' => $utilisateur->isEmailIsVerified(),
                'adresse' => $utilisateur->getAdresse(),
                'postalCode' => $utilisateur->getPostalCode(),
                'ville' => $utilisateur->getVille(),
                'pays' => $utilisateur->getPays(),
            ];
        }

        return $this->json($data);
    }

    #[OA\Post(path: '/api/secure/utilisateurs', summary: 'Create utilisateur')]
    #[OA\Response(response: 201, description: 'Created')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string')),
                new OA\Property(property: 'password', type: 'string'),
                new OA\Property(property: 'nom', type: 'string'),
                new OA\Property(property: 'prenom', type: 'string'),
                new OA\Property(property: 'dateInscription', type: 'string', format: 'date-time'),
                new OA\Property(property: 'cagnotte', type: 'number', format: 'float'),
                new OA\Property(property: 'emailIsVerified', type: 'boolean'),
                new OA\Property(property: 'adresse', type: 'string'),
                new OA\Property(property: 'postalCode', type: 'string'),
                new OA\Property(property: 'ville', type: 'string'),
                new OA\Property(property: 'pays', type: 'string')
            ]
        )
    )]
    #[Route('/api/secure/utilisateurs', name: 'api_utilisateurs_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $utilisateur = new Utilisateur();
        $utilisateur->setEmail($data['email'] ?? '');
        $utilisateur->setRoles($data['roles'] ?? []);
        $utilisateur->setPassword($data['password'] ?? '');
        $utilisateur->setNom($data['nom'] ?? null);
        $utilisateur->setPrenom($data['prenom'] ?? null);
        if (isset($data['dateInscription'])) {
            $utilisateur->setDateInscription(new \DateTime($data['dateInscription']));
        }
        $utilisateur->setCagnotte($data['cagnotte'] ?? null);
        $utilisateur->setEmailIsVerified($data['emailIsVerified'] ?? null);
        $utilisateur->setAdresse($data['adresse'] ?? null);
        $utilisateur->setPostalCode($data['postalCode'] ?? null);
        $utilisateur->setVille($data['ville'] ?? null);
        $utilisateur->setPays($data['pays'] ?? null);

        $entityManager->persist($utilisateur);
        $entityManager->flush();

        $dataUser[] = [
            'id' => $utilisateur->getId(),
            'email' => $utilisateur->getEmail(),
            'roles' => $utilisateur->getRoles(),
            'nom' => $utilisateur->getNom(),
            'prenom' => $utilisateur->getPrenom(),
            'dateInscription' => $utilisateur->getDateInscription()?->format('Y-m-d H:i:s'),
            'cagnotte' => $utilisateur->getCagnotte(),
            'emailIsVerified' => $utilisateur->isEmailIsVerified(),
            'adresse' => $utilisateur->getAdresse(),
            'postalCode' => $utilisateur->getPostalCode(),
            'ville' => $utilisateur->getVille(),
            'pays' => $utilisateur->getPays(),
        ];

        return $this->json($dataUser, 201);
    }

    #[OA\Put(path: '/api/secure/utilisateurs/{id}', summary: 'Edit utilisateur')]
    #[OA\Response(response: 200, description: 'Success')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string')),
                new OA\Property(property: 'password', type: 'string'),
                new OA\Property(property: 'nom', type: 'string'),
                new OA\Property(property: 'prenom', type: 'string'),
                new OA\Property(property: 'dateInscription', type: 'string', format: 'date-time'),
                new OA\Property(property: 'cagnotte', type: 'number', format: 'float'),
                new OA\Property(property: 'emailIsVerified', type: 'boolean'),
                new OA\Property(property: 'adresse', type: 'string'),
                new OA\Property(property: 'postalCode', type: 'string'),
                new OA\Property(property: 'ville', type: 'string'),
                new OA\Property(property: 'pays', type: 'string')
            ]
        )
    )]
    #[Route('/api/secure/utilisateurs/{id}', name: 'api_utilisateurs_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Utilisateur $utilisateur): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $utilisateur->setEmail($data['email'] ?? '');
        $utilisateur->setRoles($data['roles'] ?? []);
        $utilisateur->setNom($data['nom'] ?? null);
        $utilisateur->setPrenom($data['prenom'] ?? null);
        $utilisateur->setPassword($utilisateur->getPassword());
//        if (isset($data['dateInscription'])) {
//            $utilisateur->setDateInscription(new \DateTime($data['dateInscription']));
//        }
        $utilisateur->setCagnotte($data['cagnotte'] ?? null);
        $utilisateur->setEmailIsVerified($data['emailIsVerified'] ?? null);
        $utilisateur->setAdresse($data['adresse'] ?? null);
        $utilisateur->setPostalCode($data['postalCode'] ?? null);
        $utilisateur->setVille($data['ville'] ?? null);
        $utilisateur->setPays($data['pays'] ?? null);

        $entityManager->persist($utilisateur);
        $entityManager->flush();

        $dataUser[] = [
            'id' => $utilisateur->getId(),
            'email' => $utilisateur->getEmail(),
            'roles' => $utilisateur->getRoles(),
            'nom' => $utilisateur->getNom(),
            'prenom' => $utilisateur->getPrenom(),
            'dateInscription' => $utilisateur->getDateInscription()?->format('Y-m-d H:i:s'),
            'cagnotte' => $utilisateur->getCagnotte(),
            'emailIsVerified' => $utilisateur->isEmailIsVerified(),
            'adresse' => $utilisateur->getAdresse(),
            'postalCode' => $utilisateur->getPostalCode(),
            'ville' => $utilisateur->getVille(),
            'pays' => $utilisateur->getPays(),
        ];

        return $this->json($dataUser, 201);
    }

    #[OA\Delete(path: '/api/secure/utilisateurs/{id}', summary: 'Delete utilisateur')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/secure/utilisateurs/{id}', name: 'api_utilisateurs_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Utilisateur $utilisateur): JsonResponse
    {
        $data[] = [
            'id' => $utilisateur->getId(),
            'email' => $utilisateur->getEmail(),
            'roles' => $utilisateur->getRoles(),
            'nom' => $utilisateur->getNom(),
            'prenom' => $utilisateur->getPrenom(),
            'dateInscription' => $utilisateur->getDateInscription()?->format('Y-m-d H:i:s'),
            'cagnotte' => $utilisateur->getCagnotte(),
            'emailIsVerified' => $utilisateur->isEmailIsVerified(),
            'adresse' => $utilisateur->getAdresse(),
            'postalCode' => $utilisateur->getPostalCode(),
            'ville' => $utilisateur->getVille(),
            'pays' => $utilisateur->getPays(),
        ];

        $entityManager->remove($utilisateur);
        $entityManager->flush();

        return $this->json($data);
    }
}
