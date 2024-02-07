<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\QuizType;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/bibliotheque")]
class BibliothequeController extends AbstractController
{
    #[Route('/', name: 'bibliotheque_index')]
    public function index(EntityManagerInterface $em, Request $request, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $repo = $em->getRepository(Quiz::class);
        $resultat = $repo->findByUser($this->getUser()->getId());

        $liste = [];

        foreach($resultat as $item) {
            $liste[] = [
                "id" => $item->getId(),
                "title" => $item->getTitle(),
                "number" => count($item->getQuestions()),
                "created" => $item->getCreatedDate(),
                "image" => $item->getImage() ? $item->getImage() : "logo.png",
            ];
        }

        return $this->render('bibliotheque/index.html.twig', [
            "activeTab" => "bibliotheque",
            "pagination" => $paginator->paginate($liste, $request->query->getInt('page', 1), 15)
        ]);
    }

    #[Route('/quiz/{id}', name: 'delete_quiz')]
    public function deleteQuiz(EntityManagerInterface $em, QuizRepository $repo, $id): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $quiz = $repo->find($id);

        if($quiz->getUser() === $this->getUser()) {
            $em->remove($quiz);
            $em->flush();
        }

        return $this->redirectToRoute("bibliotheque_index");
    }
}