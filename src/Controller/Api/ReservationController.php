<?php

namespace App\Controller\Api;

use App\Entity\Reservation;
use App\Entity\Annonce;
use App\Entity\Utilisateur;
use App\Entity\Transaction;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
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
                'stripeAmount' => $reservation->getStripeAmount()
            ];
        }

        return $this->json($data);
    }

    #[OA\Get(path: '/api/reservations/annonce/{id}', summary: 'List reservations by annonce')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/reservations/annonce/{id}', name: 'api_reservations_by_annonce', methods: ['GET'])]
    public function reservationsByAnnonce(Annonce $annonce, ReservationRepository $reservationRepository): JsonResponse
    {
        $reservations = $reservationRepository->findByAnnonce($annonce);

        $data = [];
        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => $reservation->getId(),
                'dateDebut' => $reservation->getDateDebut()?->format('Y-m-d H:i:s'),
                'dateFin' => $reservation->getDateFin()?->format('Y-m-d H:i:s'),
                'statut' => $reservation->getStatut(),
                'annonceId' => $reservation->getAnnonce()?->getId(),
                'utilisateurId' => $reservation->getUtilisateur()?->getId(),
                'stripeAmount' => $reservation->getStripeAmount(),
            ];
        }

        return $this->json($data);
    }

    #[OA\Get(path: '/api/secure/utilisateurs/reservations', summary: 'List reservations by user')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/secure/utilisateurs/reservations', name: 'api_reservations_by_user', methods: ['GET'])]
    public function reservationsByUser(Security $security, ReservationRepository $reservationRepository): JsonResponse
    {
        $user = $security->getUser();
        $reservations = $user->getReservations();

        $data = [];
        foreach ($reservations as $reservation) {
            $data[] = [
                'id' => $reservation->getId(),
                'dateDebut' => $reservation->getDateDebut()?->format('Y-m-d H:i:s'),
                'dateFin' => $reservation->getDateFin()?->format('Y-m-d H:i:s'),
                'statut' => $reservation->getStatut(),
                'annonceId' => $reservation->getAnnonce()?->getId(),
                'utilisateurId' => $reservation->getUtilisateur()?->getId(),
                'stripeAmount' => $reservation->getStripeAmount()
            ];
        }

        return $this->json($data);
    }


    #[OA\Post(path: '/api/secure/reservations', summary: 'Create reservation')]
    #[OA\Response(response: 201, description: 'Created')]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'dateDebut', type: 'string', format: 'date-time'),
                new OA\Property(property: 'dateFin', type: 'string', format: 'date-time'),
                new OA\Property(property: 'statut', type: 'string'),
                new OA\Property(property: 'annonceId', type: 'integer'),
                new OA\Property(property: 'utilisateurId', type: 'integer'),
                new OA\Property(property: 'stripeIntentId', type: 'string', nullable: true)
            ]
        )
    )]
    #[Route('/api/secure/reservations', name: 'api_reservations_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $annonce = null;
        if (isset($data['annonceId'])) {
            $annonce = $entityManager->getRepository(Annonce::class)->find($data['annonceId']);
        }

        if (!$annonce) {
            return $this->json(['error' => 'Annonce not found'], 404);
        }

        $amountCreate = (int) round(((float) ($annonce->getPrix() ?? 0)) * 100);

        $intentId = $data['stripeIntentId'] ?? null;
        $paymentIntent = null;

        try {
            if ($intentId) {
                $paymentIntent = PaymentIntent::retrieve($intentId);
            } else {
                $paymentIntent = PaymentIntent::create([
                    'amount' => $amountCreate,
                    'currency' => 'eur',
                    'automatic_payment_methods' => ['enabled' => true],
                ]);
            }

            $paymentIntent->confirm();
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }

        $amountPaid = ($paymentIntent->amount ?? 0) / 100;
        $currency = $paymentIntent->currency ?? 'eur';
        $status = $paymentIntent->status ?? null;

        $reservation = new Reservation();
        if (isset($data['dateDebut'])) {
            $reservation->setDateDebut(new \DateTime($data['dateDebut']));
        }
        if (isset($data['dateFin'])) {
            $reservation->setDateFin(new \DateTime($data['dateFin']));
        }
        $reservation->setStatut($data['statut'] ?? null);

        $reservation->setAnnonce($annonce);

        if (isset($data['utilisateurId'])) {
            $utilisateur = $entityManager->getRepository(Utilisateur::class)->find($data['utilisateurId']);
            if ($utilisateur) {
                $reservation->setUtilisateur($utilisateur);
            }
        }

        $reservation->setStripeAmount($amountPaid);
        $entityManager->persist($reservation);

        $transaction = new Transaction();
        $transaction->setStripeIntentId($paymentIntent->id);
        $transaction->setAmount((string) $amountPaid);
        $transaction->setCurrency($currency);
        $transaction->setStatus($status);
        $transaction->setReservation($reservation);
        $transaction->setUtilisateur($reservation->getUtilisateur());

        $owner = $reservation->getAnnonce()?->getUtilisateur();
        if ($owner) {
            $current = (float) ($owner->getCagnotte() ?? 0);
            $owner->setCagnotte((string)($current + $amountPaid));
            $entityManager->persist($owner);
        }

        $entityManager->persist($transaction);
        $entityManager->flush();

        return $this->json([
            'id' => $reservation->getId(),
            'annonceId' => $reservation->getAnnonce()?->getId(),
            'utilisateurId' => $reservation->getUtilisateur()?->getId(),
            'stripeAmount' => $reservation->getStripeAmount(),
            'paymentIntent' => $paymentIntent->id,
            'status' => $status,
        ], 201);
    }

    #[OA\Put(path: '/api/secure/reservations/{id}', summary: 'Edit reservation')]
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
    #[Route('/api/secure/reservations/{id}', name: 'api_reservations_edit', methods: ['PUT'])]
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

    #[OA\Delete(path: '/api/secure/reservations/{id}', summary: 'Delete reservation')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/api/secure/reservations/{id}', name: 'api_reservations_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, Reservation $reservation): JsonResponse
    {
        $entityManager->remove($reservation);
        $entityManager->flush();

        return $this->json(['status' => 'Reservation deleted']);
    }
}
