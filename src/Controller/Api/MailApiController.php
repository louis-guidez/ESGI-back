<?php

namespace App\Controller\Api;

// src/Controller/MailController.php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class MailApiController extends AbstractController
{
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
}
