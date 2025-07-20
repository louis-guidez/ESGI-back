<?php

// src/Controller/Api/PhotoController.php

namespace App\Controller\Api;

use App\Entity\Photo;
use App\Repository\PhotoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    public function index(PhotoRepository $photoRepository, ParameterBagInterface $params): JsonResponse
    {
        $photos = $photoRepository->findAll();
        $endpoint = rtrim($params->get('minio_endpoint'), '/');

        // transformer les entités en tableaux simples
        $data = [];
        foreach ($photos as $photo) {
            $data[] = [
                'id' => $photo->getId(),
                'path' => $endpoint . '/fichier/' . $photo->getImageName(),
                'dateUpload' => $photo->getDateUpload()->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }

    #[OA\Post(path: '/api/secure/photos', summary: 'Create photo')]
    #[OA\Response(response: 201, description: 'Created')]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                type: 'object',
                properties: [
                    new OA\Property(property: 'imageFile', type: 'string', format: 'binary'),
                    new OA\Property(property: 'dateUpload', type: 'string', format: 'date-time')
                ]
            )
        )
    )]
    #[Route('/api/secure/photos', name: 'api_photos_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $photo = new Photo();

        /** @var UploadedFile|null $file */
        $file = $request->files->get('imageFile');
        if ($file instanceof UploadedFile) {
            $photo->setImageFile($file);
        }

        if ($request->request->get('dateUpload')) {
            $photo->setDateUpload(new \DateTime($request->request->get('dateUpload')));
        }

        $entityManager->persist($photo);
        $entityManager->flush();

        return $this->json(['id' => $photo->getId()], 201);
    }

    #[OA\Put(path: '/api/secure/photos/{id}', summary: 'Edit photo')]
    #[OA\Response(response: 200, description: 'Success')]
    #[OA\RequestBody(
        required: true,
        content: new OA\MediaType(
            mediaType: 'multipart/form-data',
            schema: new OA\Schema(
                type: 'object',
                properties: [
                    new OA\Property(property: 'imageFile', type: 'string', format: 'binary'),
                    new OA\Property(property: 'dateUpload', type: 'string', format: 'date-time')
                ]
            )
        )
    )]
    #[Route('/api/secure/photos/{id}', name: 'api_photos_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Photo $photo): JsonResponse
    {
        /** @var UploadedFile|null $file */
        $file = $request->files->get('imageFile');
        if ($file instanceof UploadedFile) {
            $photo->setImageFile($file);
        }

        if ($request->request->get('dateUpload')) {
            $photo->setDateUpload(new \DateTime($request->request->get('dateUpload')));
        }

        $entityManager->flush();

        return $this->json(['status' => 'Photo updated']);
    }

    #[OA\Delete(path: '/api/secure/photos/{id}', summary: 'Delete photo')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/secure/photos/{id}', name: 'api_photos_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Photo $photo): JsonResponse
    {
        $entityManager->remove($photo);
        $entityManager->flush();

        return $this->json(['status' => 'Photo deleted']);
    }
}
