<?php

namespace App\Controller\Api;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        UtilisateurRepository $utilisateurRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        if (!$email || !$password) {
            return $this->json(['error' => 'Email and password are required'], 400);
        }

        if ($utilisateurRepository->findOneBy(['email' => $email])) {
            return $this->json(['error' => 'Email already in use'], 400);
        }

        $utilisateur = new Utilisateur();
        $utilisateur->setEmail($email);
        $utilisateur->setPassword(
            $passwordHasher->hashPassword($utilisateur, $password)
        );
        $utilisateur->setRoles($data['roles'] ?? []);
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

    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        UtilisateurRepository $utilisateurRepository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        if (!$email || !$password) {
            return $this->json(['error' => 'Invalid credentials'], 400);
        }

        $utilisateur = $utilisateurRepository->findOneBy(['email' => $email]);
        if (!$utilisateur || !$passwordHasher->isPasswordValid($utilisateur, $password)) {
            return $this->json(['error' => 'Invalid credentials'], 401);
        }

        return $this->json([
            'message' => 'Login successful',
            'userId' => $utilisateur->getId(),
        ]);
    }
}
