<?php

namespace App\Controller\Api;

use App\Entity\Annonce;
use App\Repository\AnnonceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/api/annonces', name: 'api_annonces_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $annonce = new Annonce();
        $annonce->setTitre($data['titre'] ?? null);
        $annonce->setDescription($data['description'] ?? null);
        $annonce->setPrix($data['prix'] ?? null);
        $annonce->setStatut($data['statut'] ?? null);
        if (isset($data['dateCreation'])) {
            $annonce->setDateCreation(new \DateTime($data['dateCreation']));
        }

        $entityManager->persist($annonce);
        $entityManager->flush();

        return $this->json(['id' => $annonce->getId()], 201);
    }

    #[Route('/api/annonces/{id}', name: 'api_annonces_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Annonce $annonce): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $annonce->setTitre($data['titre'] ?? $annonce->getTitre());
        $annonce->setDescription($data['description'] ?? $annonce->getDescription());
        $annonce->setPrix($data['prix'] ?? $annonce->getPrix());
        $annonce->setStatut($data['statut'] ?? $annonce->getStatut());
        if (isset($data['dateCreation'])) {
            $annonce->setDateCreation(new \DateTime($data['dateCreation']));
        }

        $entityManager->flush();

        return $this->json(['status' => 'Annonce updated']);
    }

    #[Route('/api/annonces/{id}', name: 'api_annonces_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Annonce $annonce): JsonResponse
    {
        $entityManager->remove($annonce);
        $entityManager->flush();

        return $this->json(['status' => 'Annonce deleted']);
    }
}
