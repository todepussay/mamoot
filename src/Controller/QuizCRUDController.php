<?php

namespace App\Controller;

use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
