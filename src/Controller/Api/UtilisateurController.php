<?php

namespace App\Controller\Api;

use App\Repository\UtilisateurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
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
}
