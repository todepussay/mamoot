<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/historique")]
class HistoriqueController extends AbstractController
{
    #[Route('/', name: 'historique_index')]
    public function index(): Response
    {
        return $this->render('historique/index.html.twig', [
            "activeTab" => "historique"
        ]);
    }
}
