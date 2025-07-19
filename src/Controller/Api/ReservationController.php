<?php

namespace App\Controller\Api;

use App\Entity\Reservation;
use App\Entity\Annonce;
use App\Entity\Utilisateur;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Reservation')]
class ReservationController extends AbstractController
{
    #[OA\Get(path: '/api/reservations', summary: 'List reservations')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/reservations', name: 'api_reservations', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): JsonResponse
    {
        $reservations = $reservationRepository->findAll();

        $data = [];
        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => $reservation->getId(),
                'dateDebut' => $reservation->getDateDebut()?->format('Y-m-d H:i:s'),
                'dateFin' => $reservation->getDateFin()?->format('Y-m-d H:i:s'),
                'statut' => $reservation->getStatut(),
                'annonceId' => $reservation->getAnnonce()?->getId(),
                'utilisateurId' => $reservation->getUtilisateur()?->getId(),
            ];
        }

        return $this->json($data);
    }

    #[OA\Post(path: '/api/reservations', summary: 'Create reservation')]
    #[OA\Response(response: 201, description: 'Created')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'dateDebut', type: 'string', format: 'date-time'),
                new OA\Property(property: 'dateFin', type: 'string', format: 'date-time'),
                new OA\Property(property: 'statut', type: 'string'),
                new OA\Property(property: 'annonceId', type: 'integer'),
                new OA\Property(property: 'utilisateurId', type: 'integer')
            ]
        )
    )]
    #[Route('/api/reservations', name: 'api_reservations_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $reservation = new Reservation();
        if (isset($data['dateDebut'])) {
            $reservation->setDateDebut(new \DateTime($data['dateDebut']));
        }
        if (isset($data['dateFin'])) {
            $reservation->setDateFin(new \DateTime($data['dateFin']));
        }
        $reservation->setStatut($data['statut'] ?? null);

        if (isset($data['annonceId'])) {
            $annonce = $entityManager->getRepository(Annonce::class)->find($data['annonceId']);
            if ($annonce) {
                $reservation->setAnnonce($annonce);
            }
        }

        if (isset($data['utilisateurId'])) {
            $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($data['utilisateurId']);
            if ($utilisateur) {
                $reservation->setUtilisateur($utilisateur);
            }
        }

        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->json([
            'id' => $reservation->getId(),
            'annonceId' => $reservation->getAnnonce()?->getId(),
            'utilisateurId' => $reservation->getUtilisateur()?->getId(),
        ], 201);
    }

    #[OA\Put(path: '/api/reservations/{id}', summary: 'Edit reservation')]
    #[OA\Response(response: 200, description: 'Success')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'dateDebut', type: 'string', format: 'date-time'),
                new OA\Property(property: 'dateFin', type: 'string', format: 'date-time'),
                new OA\Property(property: 'statut', type: 'string'),
                new OA\Property(property: 'annonceId', type: 'integer'),
                new OA\Property(property: 'utilisateurId', type: 'integer')
            ]
        )
    )]
    #[Route('/api/reservations/{id}', name: 'api_reservations_edit', methods: ['PUT'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, Reservation $reservation): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['dateDebut'])) {
            $reservation->setDateDebut(new \DateTime($data['dateDebut']));
        }
        if (isset($data['dateFin'])) {
            $reservation->setDateFin(new \DateTime($data['dateFin']));
        }
        $reservation->setStatut($data['statut'] ?? $reservation->getStatut());

        if (isset($data['annonceId'])) {
            $annonce = $entityManager->getRepository(Annonce::class)->find($data['annonceId']);
            if ($annonce) {
                $reservation->setAnnonce($annonce);
            }
        }

        if (isset($data['utilisateurId'])) {
            $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($data['utilisateurId']);
            if ($utilisateur) {
                $reservation->setUtilisateur($utilisateur);
            }
        }

        $entityManager->flush();

        return $this->json([
            'id' => $reservation->getId(),
            'annonceId' => $reservation->getAnnonce()?->getId(),
            'utilisateurId' => $reservation->getUtilisateur()?->getId(),
            'status' => 'Reservation updated',
        ]);
    }

    #[OA\Delete(path: '/api/reservations/{id}', summary: 'Delete reservation')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/reservations/{id}', name: 'api_reservations_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Reservation $reservation): JsonResponse
    {
        $entityManager->remove($reservation);
        $entityManager->flush();

        return $this->json(['status' => 'Reservation deleted']);
    }
}
