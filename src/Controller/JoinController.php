<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route("/session", name: "set_session_username_code")]
    public function session(Request $request): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $username = $data['username'];
        $code = $data["code"];

        $request->getSession()->set("username", $username);
        $request->getSession()->set("code", $code);

        return new JsonResponse(["success" => true]);
    }
}
