<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: "users")]
class UserController extends AbstractController
{
    #[Route("/register", name: "register")]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $encoder){
        $user = new User();
        $user->setDateCreated(new DateTime());

        $registerForm = $this->createForm(RegisterType::class, $user);
        $registerForm->handleRequest($request);

        //mettre le role user par défaut
        $user->setRoles("ROLE_USER");

        if($registerForm->isSubmitted() && $registerForm->isValid()){
            $hashed = $encoder->hashPassword($user, $user->getPassword());
            $user->setPassword($hashed);




            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute("login");
        }

        return $this->render("user/register.html.twig", [
            "registerForm" => $registerForm->createView()
        ]);
    }

    #[Route("/login", name: "login")]
    public function login(){
        return $this->render("user/login.html.twig");
    }

    #[Route("logout", name: "logout")]
    public function logout(){

    }



}
