<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface; // Import UserPasswordHasherInterface

class ProfilsController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordEncoder; // Injected UserPasswordHasherInterface

    // Constructor to inject the EntityManagerInterface and UserPasswordHasherInterface
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder; // Set the injected UserPasswordHasherInterface
    }

    #[Route('/profils', name: 'user_profile')]
    public function index(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();

        // Handle changing username
        $newUsername = $request->request->get('new_username');
        if ($newUsername !== null) {
            $user->setUsername($newUsername);
            $this->entityManager->flush(); // Use the injected EntityManagerInterface
        }

        // Handle changing password
        $oldPassword = $request->request->get('old_password');
        $newPassword = $request->request->get('new_password');
        if ($oldPassword !== null && $newPassword !== null) {
            $user->setPassword($this->passwordEncoder->hashPassword($user, $newPassword));
            $this->entityManager->flush(); // Use the injected EntityManagerInterface
        }

        return $this->render('profils/index.html.twig', [
            "activeTab" => "profils",
        ]);
    }
}

