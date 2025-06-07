<?php

namespace App\Controller\Api;

use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    #[Route('/api/annonces', name: 'api_annonces', methods: ['GET'])]
    public function index(AnnonceRepository $annonceRepository): JsonResponse
    {
        $annonces = $annonceRepository->findAll();

        $data = [];
        foreach ($annonces as $annonce) {
            $data[] = [
                'id' => $annonce->getId(),
                'titre' => $annonce->getTitre(),
                'description' => $annonce->getDescription(),
                'prix' => $annonce->getPrix(),
                'statut' => $annonce->getStatut(),
                'dateCreation' => $annonce->getDateCreation()?->format('Y-m-d H:i:s'),
            ];
        }

        return $this->json($data);
    }
}
