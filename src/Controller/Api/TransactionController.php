<?php

namespace App\Controller\Api;

use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Transaction')]
class TransactionController extends AbstractController
{
    #[OA\Get(path: '/api/secure/transactions/user/{id}', summary: 'List transactions by user')]
    #[OA\Response(response: 200, description: 'Success')]
    #[OA\Response(response: 404, description: 'Not found')]
    #[Route('/api/secure/transactions/user/{id}', name: 'api_transactions_user', methods: ['GET'])]
    public function byUser(int $id, UtilisateurRepository $utilisateurRepository, TransactionRepository $transactionRepository): JsonResponse
    {
        $utilisateur = $utilisateurRepository->find($id);
        if (!$utilisateur) {
            return $this->json(['error' => 'Utilisateur not found'], 404);
        }

        $transactions = $transactionRepository->findBy(['utilisateur' => $utilisateur]);

        $data = array_map(static function (Transaction $transaction) {
            return [
                'id' => $transaction->getId(),
                'stripeIntentId' => $transaction->getStripeIntentId(),
                'amount' => $transaction->getAmount(),
                'currency' => $transaction->getCurrency(),
                'status' => $transaction->getStatus(),
                'createdAt' => $transaction->getCreatedAt()?->format('Y-m-d H:i:s'),
                'reservationId' => $transaction->getReservation()?->getId(),
                'utilisateurId' => $transaction->getUtilisateur()?->getId(),
            ];
        }, $transactions);

        return $this->json($data);
    }

    #[OA\Get(path: '/api/secure/transactions/reservation/{id}', summary: 'List transactions by reservation')]
    #[OA\Response(response: 200, description: 'Success')]
    #[OA\Response(response: 404, description: 'Not found')]
    #[Route('/api/secure/transactions/reservation/{id}', name: 'api_transactions_reservation', methods: ['GET'])]
    public function byReservation(int $id, ReservationRepository $reservationRepository, TransactionRepository $transactionRepository): JsonResponse
    {
        $reservation = $reservationRepository->find($id);
        if (!$reservation) {
            return $this->json(['error' => 'Reservation not found'], 404);
        }

        $transactions = $transactionRepository->findBy(['reservation' => $reservation]);

        $data = array_map(static function (Transaction $transaction) {
            return [
                'id' => $transaction->getId(),
                'stripeIntentId' => $transaction->getStripeIntentId(),
                'amount' => $transaction->getAmount(),
                'currency' => $transaction->getCurrency(),
                'status' => $transaction->getStatus(),
                'createdAt' => $transaction->getCreatedAt()?->format('Y-m-d H:i:s'),
                'reservationId' => $transaction->getReservation()?->getId(),
                'utilisateurId' => $transaction->getUtilisateur()?->getId(),
            ];
        }, $transactions);

        return $this->json($data);
    }
}

