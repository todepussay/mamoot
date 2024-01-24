<?php

namespace App\Controller;

use App\Form\PlayerType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/rejoindre")]
class JoinController extends AbstractController
{
    #[Route('/', name: 'join')]
    public function index(Request $request): Response
    {
        //création d'un formulaire pour rejoindre la partie
        $joinForm = $this->createForm(PlayerType::class);
        //traitement du formulaire
        $joinForm->handleRequest($request);
        if ($joinForm->isSubmitted() && $joinForm->isValid()) {

        }




        return $this->render('join/join.html.twig', [
            'form' => $joinForm->createView(),
        ]);
    }
}
