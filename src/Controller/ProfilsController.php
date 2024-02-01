<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfilsController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordEncoder;
    private TokenStorageInterface $tokenStorage;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordEncoder,
        TokenStorageInterface $tokenStorage
    ) {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->tokenStorage = $tokenStorage;
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
            $this->entityManager->flush();
        }

        // Handle changing password
        $oldPassword = $request->request->get('old_password');
        $newPassword = $request->request->get('new_password');
        if ($oldPassword !== null && $newPassword !== null) {
            $user->setPassword($this->passwordEncoder->hashPassword($user, $newPassword));
            $this->entityManager->flush();
        }

        return $this->render('profils/index.html.twig', [
            "activeTab" => "profils",
        ]);
    }

    #[Route('/delete-profile', name: 'delete_profile')]
    public function deleteProfile(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $user = $this->getUser();
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        // Logout the user and redirect to the homepage
        $this->tokenStorage->setToken(null);
        $request->getSession()->invalidate();
        return $this->redirectToRoute('index');
    }
}
