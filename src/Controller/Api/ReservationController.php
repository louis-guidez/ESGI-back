<?php

namespace App\Controller\Api;

use App\Entity\Reservation;
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
            ];
        }

        return $this->json($data);
    }

    #[OA\Post(path: '/api/reservations', summary: 'Create reservation')]
    #[OA\Response(response: 201, description: 'Created')]
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

        $entityManager->persist($reservation);
        $entityManager->flush();

        return $this->json(['id' => $reservation->getId()], 201);
    }

    #[OA\Put(path: '/api/reservations/{id}', summary: 'Edit reservation')]
    #[OA\Response(response: 200, description: 'Success')]
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

        $entityManager->flush();

        return $this->json(['status' => 'Reservation updated']);
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
