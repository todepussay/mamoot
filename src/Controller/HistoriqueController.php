<?php

namespace App\Controller;

use App\Entity\QuizHistorique;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/historique")]
class HistoriqueController extends AbstractController
{
    #[Route('/', name: 'historique_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $repo = $em->getRepository(QuizHistorique::class);
        $resultat = $repo->findByUser($this->getUser()->getId());

        dump($resultat);

        return $this->render('historique/index.html.twig', [
            "activeTab" => "historique"
        ]);
    }
}
