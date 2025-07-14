<?php

// src/Controller/Api/PhotoController.php

namespace App\Controller\Api;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Photo')]
class PhotoController extends AbstractController
{
    #[OA\Get(path: '/api/photos', summary: 'List photos')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/photos', name: 'api_photos', methods: ['GET'])]
    public function index(PhotoRepository $photoRepository): JsonResponse
    {
        $photos = $photoRepository->findAll();

        // transformer les entités en tableaux simples
        $data = [];
        foreach ($photos as $photo) {
            $data[] = [
                'id' => $photo->getId(),
                'urlChemin' => $photo->getUrlChemin(),
                'dateUpload' => $photo->getDateUpload()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }

    #[OA\Post(path: '/api/photos', summary: 'Create photo')]
    #[OA\Response(response: 201, description: 'Created')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'urlChemin', type: 'string'),
                new OA\Property(property: 'dateUpload', type: 'string', format: 'date-time')
            ]
        )
    )]
    #[Route('/api/photos', name: 'api_photos_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $photo = new Photo();
        $photo->setUrlChemin($data['urlChemin'] ?? null);
        if (isset($data['dateUpload'])) {
            $photo->setDateUpload(new \DateTime($data['dateUpload']));
        }

        $entityManager->persist($photo);
        $entityManager->flush();

        return $this->json(['id' => $photo->getId()], 201);
    }

    #[OA\Put(path: '/api/photos/{id}', summary: 'Edit photo')]
    #[OA\Response(response: 200, description: 'Success')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'urlChemin', type: 'string'),
                new OA\Property(property: 'dateUpload', type: 'string', format: 'date-time')
            ]
        )
    )]
    #[Route('/api/photos/{id}', name: 'api_photos_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Photo $photo): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $photo->setUrlChemin($data['urlChemin'] ?? null);
        if (isset($data['dateUpload'])) {
            $photo->setDateUpload(new \DateTime($data['dateUpload']));
        }

        $entityManager->flush();

        return $this->json(['status' => 'Photo updated']);
    }

    #[OA\Delete(path: '/api/photos/{id}', summary: 'Delete photo')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/photos/{id}', name: 'api_photos_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Photo $photo): JsonResponse
    {
        $entityManager->remove($photo);
        $entityManager->flush();

        return $this->json(['status' => 'Photo deleted']);
    }
}
