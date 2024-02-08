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
        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {
            $this->denyAccessUnlessGranted('ROLE_USER');
        }

        $repo = $em->getRepository(QuizHistorique::class);
        $resultat = $repo->findByUser($this->getUser()->getId());

        $liste = [];

        for($i = 0; $i < count($resultat); $i++) {
            $liste[$i] = [
                "id" => $resultat[$i]->getId(),
                "date" => $resultat[$i]->getCreatedDate(),
                "title" => $resultat[$i]->getQuiz()->getTitle(),
                "players" => []
            ];

            foreach($resultat[$i]->getPlayers() as $player){
                $liste[$i]["players"][]= [
                    "id" => $player->getId(),
                    "username" => $player->getUsername(),
                    "score" => $player->getScore()
                ];
            }
        }

        return $this->render('historique/index.html.twig', [
            "activeTab" => "historique",
            "liste" => $liste
        ]);
    }

    #[Route("/delete/{id}", name: "delete-historique")]
    public function delete(EntityManagerInterface $em, $id){
        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {
            $this->denyAccessUnlessGranted('ROLE_USER');
        }

        $repo = $em->getRepository(QuizHistorique::class);
        $quiz = $repo->find($id);

        if($quiz->getUser() === $this->getUser()) {
            $em->remove($quiz);
            $em->flush();
        }

        return $this->redirectToRoute("historique_index");
    }
}