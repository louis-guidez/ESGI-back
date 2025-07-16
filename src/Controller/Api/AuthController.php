<?php

namespace App\Controller\Api;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Auth')]
class AuthController extends AbstractController
{
    #[OA\Post(path: '/api/register', summary: 'Register new user')]
    #[OA\Response(response: 201, description: 'Created')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'password', type: 'string'),
                new OA\Property(property: 'roles', type: 'array', items: new OA\Items(type: 'string')),
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
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
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

    #[OA\Post(path: '/api/login', summary: 'User login')]
    #[OA\Response(response: 200, description: 'Success')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string'),
                new OA\Property(property: 'password', type: 'string')
            ]
        )
    )]
    #[Route('/api/login', name: 'custom_login', methods: ['POST'])]
    public function login(Request $request, UtilisateurRepository $repo, JWTTokenManagerInterface $jwt, UserPasswordHasherInterface $hasher): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $repo->findOneBy(['email' => $data['email'] ?? null]);

        if (!$user || !$hasher->isPasswordValid($user, $data['password'] ?? '')) {
            return $this->json(['error' => 'Invalid credentials'], 401);
        }

        return $this->json([
            'token' => $jwt->create($user),
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles()
            ]
        ]);
    }
}
