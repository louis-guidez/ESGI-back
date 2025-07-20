<?php

namespace App\Controller\Api;

// src/Controller/MailController.php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\RelatedPart;
use Symfony\Component\Mime\Part\TextPart;
use Symfony\Component\Mime\Part\File;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Response;
use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Mail')]
class MailApiController extends AbstractController
{
    #[OA\Get(path: '/test-email', summary: 'Send test email')]
    #[OA\Response(response: 200, description: 'Success')]
    #[Route('/test-email', name: 'test_email')]
    public function send(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('lendo.app.esgi@gmail.com')
            ->to('louisguidez3@gmail.com')
            ->subject('Test SMTP Gmail via Docker')
            ->text('Ceci est un test')
            ->html('<p>Envoyé via Docker + Gmail SMTP 🎉</p>');

        $mailer->send($email);

        return new Response('✅ Mail envoyé !');
    }

    #[OA\Post(path: '/api/mail/send', summary: 'Send a custom email with image')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            type: 'object',
            required: ['receiver', 'subject', 'content'],
            properties: [
                new OA\Property(property: 'receiver', type: 'string'),
                new OA\Property(property: 'subject', type: 'string'),
                new OA\Property(property: 'content', type: 'string'),
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'Mail sent successfully')]
    #[Route('/api/mail/send', name: 'api_mail_send', methods: ['POST'])]
    public function sendMail(Request $request, MailerInterface $mailer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $receiver = $data['receiver'] ?? null;
        $subject = $data['subject'] ?? null;
        $content = $data['content'] ?? null;

        if (!$receiver || !$subject || !$content) {
            return $this->json(['error' => 'Missing required fields'], 400);
        }

        // Image à inclure
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/images/email-footer.png';

        if (!file_exists($imagePath)) {
            return $this->json(['error' => 'Image file not found'], 500);
        }

        // Création de l’email avec image inline
        $email = (new Email())
            ->from(new Address('lendo.app.esgi@gmail.com', 'Lendo Mailer'))
            ->to($receiver)
            ->subject($subject)
            ->html(
                $content . '<br><img src="cid:footer-image">',
                'text/html'
            )
            ->embedFromPath($imagePath, 'footer-image');

        $mailer->send($email);

        return $this->json(['success' => 'Email envoyé à ' . $receiver]);
    }
}
