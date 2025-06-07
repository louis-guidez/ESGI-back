<?php

// src/Controller/Api/UserController.php

namespace App\Controller\Api;

use App\Repository\PhotoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class PhotoController extends AbstractController
{
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
}
