<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Form\Quiz1Type;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('admin/quiz/c/r/u/d')]
class QuizCRUDController extends AbstractController
{

    #[Route('/', name: 'app_quiz_c_r_u_d_index', methods: ['GET'])]
    public function index(QuizRepository $quizRepository): Response
    {
        return $this->render('quiz_crud/index.html.twig', [
            'quizs' => $quizRepository->findAllQuiz(),
        ]);
    }

    #[Route('/new', name: 'app_quiz_c_r_u_d_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(Quiz1Type::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($quiz);
            $entityManager->flush();

            return $this->redirectToRoute('app_quiz_c_r_u_d_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('quiz_crud/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_c_r_u_d_show', methods: ['GET'])]
    public function show(Quiz $quiz): Response
    {
        return $this->render('quiz_crud/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    #[Route('/{id}', name: 'app_quiz_c_r_u_d_delete', methods: ['POST'])]
    public function delete(Request $request, Quiz $quiz, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->request->get('_token'))) {
            $entityManager->remove($quiz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_quiz_c_r_u_d_index', [], Response::HTTP_SEE_OTHER);
    }
}
