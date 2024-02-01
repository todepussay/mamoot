<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Reponse;
use App\Entity\User;
use App\Form\QuestionType;
use App\Form\QuizType;
use App\Form\RegisterType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Json;
use function mysql_xdevapi\getSession;

#[Route("/creation")]
class CreateController extends AbstractController
{

    public $quiz;
    #[Route('/', name: 'create')]
    public function index(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        if($request->getSession()->get("quiz") !== null){
            $this->quiz = $request->getSession()->get("quiz");
            return $this->dashboard($request);
        } else {
            $this->quiz = new Quiz();
            return $this->change_title($request);
        }
    }

    #[Route("/quitter", name: "quitter_quiz_modal")]
    public function quitter(Request $request){
        if(!$request->isXmlHttpRequest()){
            return $this->redirectToRoute("index");
        }
        return $this->render("create/modal_quitter.html.twig");
    }

    #[Route("/quitter_oui", name: "quitter_quiz")]
    public function quitterOui(Request $request){
        $request->getSession()->remove("quiz");
        $request->getSession()->remove("questions");
        return $this->redirectToRoute("index");
    }

    #[Route("/quitter_non", name: "pas_quitter_quiz")]
    public function quitterNon(Request $request){
        return $this->dashboard($request);
    }

    public function change_title(Request $request){

        $quizForm = $this->createForm(QuizType::class, $this->quiz);
        $quizForm->handleRequest($request);

        if($quizForm->isSubmitted() && $quizForm->isValid()){
            $request->getSession()->set('quiz', $this->quiz);
            return $this->dashboard($request);
        }

        return $this->render("create/title.html.twig", [
            "quizForm" => $quizForm->createView()
        ]);
    }

    public function dashboard(Request $request){
        $questions = $request->getSession()->get('questions', []);
        dump($request->getSession()->get("quiz"));
        dump($request->getSession()->get("questions"));
        return $this->render("create/dashboard.html.twig", [
            "quiz" => $this->quiz,
            "questions" => $questions
        ]);
    }

    #[Route("/add_question_modal", name: "add_question_modal")]
    public function add_question_modal(Request $request){
        if($request->isXmlHttpRequest()) {
            return $this->render("create/add_question.html.twig", [
            ]);
        } else {
            return $this->redirectToRoute("create");
        }
    }

    #[Route("/add_question", name: "add_question", methods: "POST")]
    public function add_question(Request $request, TranslatorInterface $translator): JsonResponse
    {
        $questions = $request->getSession()->get("questions", []);
        $data = json_decode($request->getContent());
        $question = new Question();

        switch ($data->categorie) {
            case "quiz":
                $question->setVraifaux(0);
                $question->setCurseur(0);
                $question->setValeurvraifaux(0);
                for ($i = 0; $i < 2; $i++) {
                    $reponse = new Reponse();
                    $reponse->setQuestion($question);
                    $reponse->setReponse($translator->trans("Réponse ") . ($i + 1));
                    $question->addReponse($reponse);
                }
                break;
            case "vraifaux":
                $question->setVraifaux(1);
                $question->setValeurvraifaux(1);
                $question->setCurseur(0);
                break;
            case "curseur":
                $question->setCurseur(1);
                $question->setVraifaux(0);
                $question->setValeurvraifaux(0);
                $question->setMinimumCurseur($data->min);
                $question->setMaximumCurseur($data->max);
                $question->setIntervalCurseur($data->interval);
                $question->setMinimumValide($data->min);
                $question->setMaximumValide($data->max);
                break;
        }

        $question->setQuiz($request->getSession()->get("quiz"));
        $question->setTemps($data->temps);

        $questions[] = $question;

        $request->getSession()->set('questions', $questions);

        return new JsonResponse(count($questions) - 1);
    }

    #[Route("/detail_question", name: "detail_question")]
    public function detail_question_modal(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute("create");
        }

        $data = json_decode($request->getContent());
        $id = $data->id;
        $questions = $request->getSession()->get("questions");
        $question = $questions[$id];

        if ($question->getVraifaux() == false) {
            $question->setVraifaux(0);
        } else {
            $question->setVraifaux(1);
        }

        if ($question->getCurseur() == false) {
            $question->setCurseur(0);
        } else {
            $question->setCurseur(1);
        }

        if ($question->getValeurvraifaux() == false) {
            $question->setValeurvraifaux(0);
        } else {
            $question->setValeurvraifaux(1);
        }

        return $this->render("create/detail_question.html.twig", [
            "question" => $question,
            "id" => $id,
        ]);
    }

    #[Route("/get_liste", name: "get_liste")]
    public function get_liste(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute("create");
        }

        $quiz = $request->getSession()->get("quiz");
        $questions = $request->getSession()->get("questions", []);
        return $this->render("create/liste.html.twig", [
            "quiz" => $quiz,
            "questions" => $questions
        ]);
    }

    #[Route("/copy_question", name: "copy_question")]
    public function copy_question(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute("create");
        }

        $data = json_decode($request->getContent());
        $id = $data->id - 1;
        $questions = $request->getSession()->get("questions");
        if (isset($questions[$id])) {
            $questions[] = clone $questions[$id];
            $request->getSession()->set("questions", $questions);
            return new JsonResponse(["success" => true]);
        } else {
            return new JsonResponse(["success" => false, "message" => "ID not found"]);
        }
    }

    #[Route("/up_question", name: "up_question")]
    public function up_question(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute("create");
        }

        $data = json_decode($request->getContent());
        $id = $data->id - 1;
        $questions = $request->getSession()->get("questions");
        if(isset($questions[$id]) && $id > 0){
            $question = clone $questions[$id];
            unset($questions[$id]);
            array_splice($questions, $id - 1, 0, [$question]);
            $request->getSession()->set("questions", $questions);
            return new JsonResponse(["success" => true]);
        } else {
            return new JsonResponse(["success" => false, "message" => "ID not found"]);
        }
    }

    #[Route("/delete_question", name: "delete_question")]
    public function delete_question(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute("create");
        }

        $data = json_decode($request->getContent());
        $id = $data->id - 1;
        $questions = $request->getSession()->get("questions");
        if (isset($questions[$id])) {
            unset($questions[$id]);

            $request->getSession()->set("questions", $questions);

            return new JsonResponse(['success' => true]);
        } else {
            return new JsonResponse(['success' => false, 'message' => 'ID not found'], 400);
        }
    }

    #[Route("/change_question", name: "change_question")]
    public function change_question(Request $request)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute("create");
        }

        $data = json_decode($request->getContent());
        $id = $data->id;
        $label = $data->label;
        $category = $data->category;
        $questions = $request->getSession()->get("questions");
        $question = $questions[$id];

        $question->setLabel($label);

        switch($category){
            case "quiz":
                $reponses = $data->reponses;
                $count = $question->getReponses()->count();

                if($count > count($reponses)){
                    for($i = 0; $i < $count - count($reponses); $i++){
                        $question->removeReponse();
                    }
                }

                for($i = 0; $i < count($reponses); $i++){

                    if($i < $count){
                        $reponse = $question->getReponses()[$i];
                    } else {
                        $reponse = new Reponse();
                    }

                    $reponse->setReponse($reponses[$i]->label);
                    $reponse->setGood($reponses[$i]->good);
                    $reponse->setQuestion($question);

                    if($i >= $count){
                        $question->addReponse($reponse);
                    }
                }
                break;
            case "vraifaux":

                $vrai = $data->vrai;

                $question->setValeurvraifaux($vrai);

                break;
            case "curseur":

                $mini = $data->mini;
                $maxi = $data->maxi;

                $question->setMinimumValide($mini);
                $question->setMaximumValide($maxi);


                break;
        }

        $questions[$id] = $question;

        $request->getSession()->set("questions", $questions);

        return new JsonResponse();
    }

    #[Route("/create_quiz", name: "create_quiz")]
    public function create_quiz(Request $request, EntityManagerInterface $em, TranslatorInterface $translation)
    {
        if (!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute("create");
        }

        $quiz = $request->getSession()->get("quiz");
        $questions = $request->getSession()->get("questions");

        if ($quiz->getId()) {

            $existingQuiz = $em->getRepository(Quiz::class)->find($quiz->getId());

            if (!$existingQuiz || $existingQuiz->getUser() !== $this->getUser()) {
                throw $this->createNotFoundException('Quiz not found or unauthorized access');
            }

            $existingQuiz->setTitle($quiz->getTitle());

            foreach ($existingQuiz->getQuestions() as $question) {
                $em->remove($question);
            }

            foreach ($questions as $question) {

                $question->setQuiz($existingQuiz);
                $em->persist($question);
            }

            $em->flush();

            $request->getSession()->remove("quiz");
            $request->getSession()->remove("questions");

            return new JsonResponse(["success" => true]);
        } else {
            $quiz->setCreatedDate(new DateTime());
            $user = $em->getRepository(User::class)->find($this->getUser()->getId());
            $quiz->setUser($user);

            if ($quiz->getTitle() == "") {
                return new JsonResponse(["success" => false, "message" => $translation->trans("Le titre du quiz ne peut pas être vide")]);
            } else {
                foreach ($questions as $question) {
                    if ($question->getLabel() == "") {
                        return new JsonResponse(["success" => false, "message" => $translation->trans("La question n'est pas valide")]);
                    }

                    if ($question->getCurseur() &&
                        $question->getMinimumCurseur() == "" &&
                        $question->getMaximumCurseur() == "" &&
                        $question->getMinimumValide() == "" &&
                        $question->getMaximumValide() == "") {
                        return new JsonResponse(["success" => false, "message" => $translation->trans("La question n'est pas valide")]);
                    }

                    if ($question->getReponses()) {
                        foreach ($question->getReponses() as $reponse) {
                            $em->persist($reponse);
                        }
                    }

                    $em->persist($question);
                }
            }

            $em->persist($quiz);

            $em->flush();

            $request->getSession()->remove("quiz");
            $request->getSession()->remove("questions");

            return new JsonResponse(["success" => true]);
        }
    }

    #[Route("/edit/{id}", name: "edit_quiz")]
    public function edit(Request $request, int $id, EntityManagerInterface $em)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $quiz = $em->getRepository(Quiz::class)->findById($id);

        if (!$quiz || $quiz[0]->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('Quiz not found or unauthorized access');
        }


        $request->getSession()->set('quiz', $quiz[0]);
        $request->getSession()->set('questions', $quiz[0]->getQuestions());

        return $this->dashboard($request);
    }

}