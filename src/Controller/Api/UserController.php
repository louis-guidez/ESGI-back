<?php
// src/Controller/Api/UserController.php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
#[Route('/api/users', name: 'api_users', methods: ['GET'])]
public function index(): JsonResponse
{
return $this->json([
['id' => 1, 'name' => 'Alice'],
['id' => 2, 'name' => 'Bob'],
]);
}
}
