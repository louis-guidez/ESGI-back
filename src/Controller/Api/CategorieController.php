<?php

namespace App\Controller\Api;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Categorie')]
class CategorieController extends AbstractController
{
    #[OA\Get(path: '/api/categories', summary: 'List categories')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/categories', name: 'api_categories', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): JsonResponse
    {
        $categories = $categorieRepository->findAll();

        $data = [];
        foreach ($categories as $categorie) {
            $data[] = [
                'id' => $categorie->getId(),
                'label' => $categorie->getLabel(),
            ];
        }

        return $this->json($data);
    }

    #[OA\Post(path: '/api/secure/categories', summary: 'Create category')]
    #[OA\Response(response: 201, description: 'Created')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            required: ['label'],
            properties: [
                new OA\Property(property: 'label', type: 'string')
            ]
        )
    )]
    #[Route('/api/secure/categories', name: 'api_categories_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $categorie = new Categorie();
        $categorie->setLabel($data['label'] ?? null);

        $entityManager->persist($categorie);
        $entityManager->flush();

        return $this->json(['id' => $categorie->getId()], 201);
    }

    #[OA\Put(path: '/api/secure/categories/{id}', summary: 'Edit category')]
    #[OA\Response(response: 200, description: 'Success')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'label', type: 'string')
            ]
        )
    )]
    #[Route('/api/secure/categories/{id}', name: 'api_categories_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Categorie $categorie): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (array_key_exists('label', $data)) {
            $categorie->setLabel($data['label']);
        }

        $entityManager->flush();

        return $this->json(['status' => 'Categorie updated']);
    }

    #[OA\Delete(path: '/api/secure/categories/{id}', summary: 'Delete category')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/secure/categories/{id}', name: 'api_categories_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Categorie $categorie): JsonResponse
    {
        $entityManager->remove($categorie);
        $entityManager->flush();

        return $this->json(['status' => 'Categorie deleted']);
    }
}
