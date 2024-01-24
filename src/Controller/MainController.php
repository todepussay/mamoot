<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class MainController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig', [
            "activeTab" => "index"
        ]);
    }

    #[Route("/change_language/{locale}", name: "change_locale")]
    public function changeLanguage($locale, Request $request){
        $request->getSession()->set("_locale", $locale);

        return $this->redirect($request->headers->get("referer"));
    }
}
