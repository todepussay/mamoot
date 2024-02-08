<?php

namespace App\Controller;

use App\Entity\PlayerHistorique;
use App\Entity\Quiz;
use App\Entity\QuizHistorique;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/jouer")]
class PlayController extends AbstractController
{

    #[Route('/', name: 'play')]
    public function index(Request $request): Response
    {
        if (!$this->isGranted('ROLE_USER') && !$this->isGranted('ROLE_ADMIN')) {
            $this->denyAccessUnlessGranted('ROLE_USER');
        }

        $id_user = $request->query->get('id_user');

        if(!$request->query->get('id_quiz')){
            return $this->redirectToRoute("bibliotheque_index");
        }

        return $this->render('play/index.html.twig', [
            "id_quiz" => $request->query->get("id_quiz"),
            "id_user" => $id_user
        ]);
    }

    #[Route("/get_data", name: "get_data")]
    public function getData(EntityManagerInterface $em, Request $request): JsonResponse
    {

        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute("bibliotheque_index");
        }

        $request->getSession()->clear();

        $data = json_decode($request->getContent());

        $repo = $em->getRepository(Quiz::class);
        $resultat = $repo->findById($data->id_quiz)[0];

        $quiz = [
            "id" => $resultat->getId(),
            "title" => $resultat->getTitle(),
            "created_date" => $resultat->getCreatedDate(),
            "questions" => []
        ];

       for($i = 0; $i < count($resultat->getQuestions()); $i++){
           $question_data = $resultat->getQuestions()[$i];

           $quiz["questions"][$i] = [
               "id" => $question_data->getId(),
               "label" => $question_data->getLabel(),
               "vraifaux" => $question_data->getVraifaux(),
               "valeurvraifaux" => $question_data->getValeurvraifaux(),
               "curseur" => $question_data->getCurseur(),
               "temps" => $question_data->getTemps(),
               "minimum_curseur" => $question_data->getMinimumCurseur(),
               "maximum_curseur" => $question_data->getMaximumCurseur(),
               "interval_curseur" => $question_data->getIntervalCurseur(),
               "minimum_valide" => $question_data->getMinimumValide(),
               "maximum_valide" => $question_data->getMaximumValide()
           ];

           if(count($question_data->getReponses()) !== 0){
               $quiz["questions"][$i]["reponses"] = [];

               for($j = 0; $j < count($question_data->getReponses()); $j++){
                   $reponse_data = $question_data->getReponses()[$j];

                    $quiz["questions"][$i]["reponses"][$j] = [
                        "id" => $reponse_data->getId(),
                        "reponse" => $reponse_data->getReponse(),
                        "good" => $reponse_data->getGood()
                    ];
               }
           }
       }

        return new JsonResponse([
            "quiz" => $quiz
        ]);

    }

    #[Route("/virer", name: "kicked_player")]
    public function kicked(Request $request){
        $request->getSession()->clear();
        return $this->render("play/kicked.html.twig");
    }

    #[Route("/fini", name: "finish_quiz")]
    public function finish(EntityManagerInterface $em, Request $request){
        $quiz = $request->getSession()->get("quiz");

        if(!$quiz){
            return $this->redirectToRoute("index");
        }

        if(!$quiz["is_saved"]){
            $repo = $em->getRepository(Quiz::class);
            $resultat = $repo->find($quiz["quiz"]["id"]);

            $party = new QuizHistorique();

            $party->setCreatedDate(new DateTime());
            $party->setQuiz($resultat);
            $party->setUser($this->getUser());

            for($i = 0; $i < count($quiz["players"]); $i++){
                $player = new PlayerHistorique();
                $player->setQuiz($party);
                $player->setScore($quiz["players"][$i]["score"]);
                $player->setUsername($quiz["players"][$i]["username"]);
                $party->addPlayer($player);
            }

            $em->persist($party);
            $em->flush();

            $quiz['is_saved'] = true;
            $request->getSession()->set("quiz", $quiz);
        }


        return $this->render("play/finish.html.twig", [
            "quiz" => $quiz
        ]);
    }

    #[Route("/set_session_quiz", name: "set_session_quiz")]
    public function set_session_quiz(Request $request){

        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute("join");
        }

        $data = json_decode($request->getContent(), true);
        $quiz = $data['resultat'];

        $request->getSession()->set("quiz", $quiz);

        return new JsonResponse(["success" => true]);
    }

    #[Route("/fin", name: "end_game")]
    public function end(){
        return $this->render("play/end.html.twig");
    }

    #[Route("/{code}", name: "play_code")]
    public function play_code($code, Request $request){

        if(!$request->getSession()->get("username")){
            return $this->redirectToRoute("join");
        }

        if(!preg_match("/^[1-9]{6}$/", $code)){
            $this->redirectToRoute("join");
        }

        $username = $request->getSession()->get("username");
        $request->getSession()->set("code", $code);

        return $this->render("play/play.html.twig", [
            "username" => $username,
            "code" => $code
        ]);
    }
}
