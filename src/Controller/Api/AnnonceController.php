<?php

namespace App\Controller\Api;

use App\Entity\Annonce;
use App\Entity\Categorie;
use App\Entity\Photo;
use App\Entity\Utilisateur;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Annonce')]
class AnnonceController extends AbstractController
{
    #[OA\Get(path: '/api/annonces', summary: 'List annonces')]
    #[OA\Parameter(name: 'q', in: 'query', description: 'Search term', required: false, schema: new OA\Schema(type: 'string'))]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/annonces', name: 'api_annonces', methods: ['GET'])]
    public function index(Request $request, AnnonceRepository $annonceRepository, ParameterBagInterface $params): JsonResponse
    {
        $search = $request->query->get('q');
        if ($search) {
            $annonces = $annonceRepository->searchByTerm($search);
        } else {
            $annonces = $annonceRepository->findAll();
        }
        $endpoint = rtrim($params->get('minio_endpoint'), '/'); // évite les //

        $data = [];
        foreach ($annonces as $annonce) {
            $photos = [];
            foreach ($annonce->getPhotos() as $photo) {
                $photos[] = $endpoint . '/fichier/' . $photo->getImageName();
            }

            $categories = [];
            foreach ($annonce->getCategorie() as $categorie) {
                $categories[] = $categorie->getLabel();
            }

            $data[] = [
                'id' => $annonce->getId(),
                'titre' => $annonce->getTitre(),
                'description' => $annonce->getDescription(),
                'categories' => $categories,
                'prix' => $annonce->getPrix(),
                'statut' => $annonce->getStatut(),
                'dateCreation' => $annonce->getDateCreation()?->format('Y-m-d H:i:s'),
                'photos' => $photos,
                'user' => [
                        'id' => $annonce->getUtilisateur()->getId(),
                        'prenom' => $annonce->getUtilisateur()->getPrenom(),
                        'nom' => $annonce->getUtilisateur()->getNom(),
                        'email' => $annonce->getUtilisateur()->getEmail(),
                        'adresse' => $annonce->getUtilisateur()->getAdresse(),
                        'postalCode' => $annonce->getUtilisateur()->getPostalCode(),
                        'ville' => $annonce->getUtilisateur()->getVille(),
                    ],
                ];
        }

        return $this->json($data);
    }

    #[OA\Get(path: '/api/annonces/{id}', summary: 'Show annonce')]
    #[OA\Response(response: 200, description: 'Success')]
    #[OA\Response(response: 404, description: 'Not found')]
    #[Route('/api/annonces/{id}', name: 'api_annonces_show', methods: ['GET'])]
    public function show(int $id, AnnonceRepository $annonceRepository, ParameterBagInterface $params): JsonResponse
    {
        $annonce = $annonceRepository->find($id);

        if (!$annonce) {
            return $this->json(['error' => 'Annonce not found'], 404);
        }

        $endpoint = rtrim($params->get('minio_endpoint'), '/');

        $photos = [];
        foreach ($annonce->getPhotos() as $photo) {
            $photos[] = $endpoint . '/fichier/' . $photo->getImageName();
        }

        $categories = [];
        foreach ($annonce->getCategorie() as $categorie) {
            $categories[] = $categorie->getLabel();
        }

        $data = [
            'id' => $annonce->getId(),
            'titre' => $annonce->getTitre(),
            'description' => $annonce->getDescription(),
            'categories' => $categories,
            'prix' => $annonce->getPrix(),
            'statut' => $annonce->getStatut(),
            'dateCreation' => $annonce->getDateCreation()?->format('Y-m-d H:i:s'),
            'photos' => $photos,
            'user' => [
                'id' => $annonce->getUtilisateur()->getId(),
                'prenom' => $annonce->getUtilisateur()->getPrenom(),
                'nom' => $annonce->getUtilisateur()->getNom(),
                'email' => $annonce->getUtilisateur()->getEmail(),
            ],
        ];

        return $this->json($data);
    }

    #[OA\Post(path: '/api/secure/annonces', summary: 'Create annonce')]
    #[OA\Response(response: 201, description: 'Created')]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                type: 'object',
                properties: [
                    new OA\Property(property: 'titre', type: 'string'),
                    new OA\Property(property: 'description', type: 'string'),
                    new OA\Property(property: 'prix', type: 'number', format: 'float'),
                    new OA\Property(property: 'statut', type: 'string'),
                    new OA\Property(property: 'dateCreation', type: 'string', format: 'date-time'),
                    new OA\Property(
                        property: 'categorieIds',
                        type: 'array',
                        items: new OA\Items(type: 'integer')
                    ),
                    new OA\Property(property: 'userId', type: 'integer'),
                    new OA\Property(
                        property: 'photos',
                        type: 'array',
                        items: new OA\Items(type: 'string', format: 'binary')
                    )
                ]
            )
        )
    )]
    #[Route('/api/secure/annonces', name: 'api_annonces_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): JsonResponse
    {
        $annonce = new Annonce();
        $annonce->setTitre($request->request->get('titre'));
        $annonce->setDescription($request->request->get('description'));
        $annonce->setPrix($request->request->get('prix'));
        $annonce->setStatut($request->request->get('statut'));

        if ($request->request->get('dateCreation')) {
            $annonce->setDateCreation(new \DateTime($request->request->get('dateCreation')));
        }

        $userId = $request->request->get('userId');
        $utilisateur = null;
        if ($userId) {
            $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($userId);
        } else {
            $utilisateur = $security->getUser();
        }
        if ($utilisateur instanceof Utilisateur) {
            $annonce->setUtilisateur($utilisateur);
        }

        $categorieIds = $request->request->all('categorieIds');
        if (!is_array($categorieIds)) {
            $categorieIds = $categorieIds ? [$categorieIds] : [];
        }
        foreach ($categorieIds as $categorieId) {
            $categorie = $entityManager->getRepository(Categorie::class)->find($categorieId);
            if ($categorie) {
                $annonce->addCategorie($categorie);
            }
        }

        /** @var UploadedFile[] $files */
        $files = $request->files->get('photos');

        if ($files && is_array($files)) {
            foreach ($files as $file) {
                $photo = new Photo();
                $photo->setImageFile($file); // VichUploader handles the upload
                $photo->setDateUpload(new \DateTime());
                $photo->setAnnonce($annonce);
                $entityManager->persist($photo);
                $annonce->addPhoto($photo);
            }
        }

        $entityManager->persist($annonce);
        $entityManager->flush();

        $photoUrls = [];
        foreach ($annonce->getPhotos() as $photo) {
            $photoUrls[] = $photo->getImageName();
        }

        return $this->json([
            'id' => $annonce->getId(),
            'photos' => $photoUrls,
        ], 201);
    }

    #[OA\Put(path: '/api/secure/annonces/{id}', summary: 'Edit annonce')]
    #[OA\Response(response: 200, description: 'Success')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'titre', type: 'string'),
                new OA\Property(property: 'description', type: 'string'),
                new OA\Property(property: 'prix', type: 'number', format: 'float'),
                new OA\Property(property: 'statut', type: 'string'),
                new OA\Property(property: 'dateCreation', type: 'string', format: 'date-time'),
                new OA\Property(property: 'categorieIds', type: 'array', items: new OA\Items(type: 'integer')),
                new OA\Property(property: 'userId', type: 'integer')
            ]
        )
    )]
    #[Route('/api/secure/annonces/{id}', name: 'api_annonces_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Annonce $annonce, Security $security): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $annonce->setTitre($data['titre'] ?? $annonce->getTitre());
        $annonce->setDescription($data['description'] ?? $annonce->getDescription());
        $annonce->setPrix($data['prix'] ?? $annonce->getPrix());
        $annonce->setStatut($data['statut'] ?? $annonce->getStatut());
        if (isset($data['dateCreation'])) {
            $annonce->setDateCreation(new \DateTime($data['dateCreation']));
        }

        if (isset($data['userId'])) {
            $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($data['userId']);
        } else {
            $utilisateur = $security->getUser();
        }
        if ($utilisateur instanceof Utilisateur) {
            $annonce->setUtilisateur($utilisateur);
        }

        if (isset($data['categorieIds']) && is_array($data['categorieIds'])) {
            $annonce->getCategorie()->clear();
            foreach ($data['categorieIds'] as $categorieId) {
                $categorie = $entityManager->getRepository(Categorie::class)->find($categorieId);
                if ($categorie) {
                    $annonce->addCategorie($categorie);
                }
            }
        }

        $entityManager->flush();

        return $this->json(['status' => 'Annonce updated']);
    }

    #[OA\Delete(path: '/api/secure/annonces/{id}', summary: 'Delete annonce')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/secure/annonces/{id}', name: 'api_annonces_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Annonce $annonce): JsonResponse
    {
        $entityManager->remove($annonce);
        $entityManager->flush();

        return $this->json(['status' => 'Annonce deleted']);
    }
}
