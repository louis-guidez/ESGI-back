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

    #[OA\Post(path: '/api/utilisateurs', summary: 'Create utilisateur')]
    #[OA\Response(response: 201, description: 'Created')]
    #[Route('/api/utilisateurs', name: 'api_utilisateurs_new', methods: ['POST'])]
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

        return $this->json(['id' => $utilisateur->getId()], 201);
    }

    #[OA\Put(path: '/api/utilisateurs/{id}', summary: 'Edit utilisateur')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/utilisateurs/{id}', name: 'api_utilisateurs_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Utilisateur $utilisateur): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) {
            $utilisateur->setEmail($data['email']);
        }
        if (isset($data['roles'])) {
            $utilisateur->setRoles($data['roles']);
        }
        if (isset($data['password'])) {
            $utilisateur->setPassword($data['password']);
        }
        if (array_key_exists('nom', $data)) {
            $utilisateur->setNom($data['nom']);
        }
        if (array_key_exists('prenom', $data)) {
            $utilisateur->setPrenom($data['prenom']);
        }
        if (isset($data['dateInscription'])) {
            $utilisateur->setDateInscription(new \DateTime($data['dateInscription']));
        }
        if (array_key_exists('cagnotte', $data)) {
            $utilisateur->setCagnotte($data['cagnotte']);
        }
        if (array_key_exists('emailIsVerified', $data)) {
            $utilisateur->setEmailIsVerified($data['emailIsVerified']);
        }
        if (array_key_exists('adresse', $data)) {
            $utilisateur->setAdresse($data['adresse']);
        }
        if (array_key_exists('postalCode', $data)) {
            $utilisateur->setPostalCode($data['postalCode']);
        }
        if (array_key_exists('ville', $data)) {
            $utilisateur->setVille($data['ville']);
        }
        if (array_key_exists('pays', $data)) {
            $utilisateur->setPays($data['pays']);
        }

        $entityManager->flush();

        return $this->json(['status' => 'Utilisateur updated']);
    }

    #[OA\Delete(path: '/api/utilisateurs/{id}', summary: 'Delete utilisateur')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/utilisateurs/{id}', name: 'api_utilisateurs_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Utilisateur $utilisateur): JsonResponse
    {
        $entityManager->remove($utilisateur);
        $entityManager->flush();

        return $this->json(['status' => 'Utilisateur deleted']);
    }
}
