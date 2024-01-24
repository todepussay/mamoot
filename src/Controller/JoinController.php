<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/rejoindre")]
class JoinController extends AbstractController
{
    #[Route('/', name: 'join')]
    public function index(): Response
    {
        return $this->render('join/join.html.twig');
    }
}
