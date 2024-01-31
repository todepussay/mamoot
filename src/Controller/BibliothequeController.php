<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/bibliotheque")]
class BibliothequeController extends AbstractController
{

    //
    #[Route('/', name: 'bibliotheque_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $repo = $em->getRepository(Quiz::class);
        $resultat = $repo->findByUser($this->getUser()->getId());

        $liste = [];

        foreach($resultat as $item){
            $liste[] = [
                "id" => $item->getId(),
                "title" => $item->getTitle(),
                "number" => count($item->getQuestions()),
                "created" => $item->getCreatedDate()
            ];
        }

        return $this->render('bibliotheque/index.html.twig', [
            "activeTab" => "bibliotheque",
            "liste" => $liste
        ]);
    }

    #[Route('/quiz/{id}', name: 'delete_quiz')]
    public function deleteQuiz(EntityManagerInterface $em, QuizRepository $repo, $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $quiz = $repo->find($id);

        if($quiz->getUser() === $this->getUser()){
            $em->remove($quiz);
            $em->flush();
        }

        return $this->redirectToRoute("bibliotheque_index");
    }
}