<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

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
            $confirmChange = $this->confirmChange("Êtes-vous sûr de vouloir changer votre pseudo?");
            if ($confirmChange) {
                $user->setUsername($newUsername);
                $this->entityManager->flush();
            }
        }

        // Handle changing password
        $oldPassword = $request->request->get('old_password');
        $newPassword = $request->request->get('new_password');
        if ($oldPassword !== null && $newPassword !== null) {
            $confirmChange = $this->confirmChange("Êtes-vous sûr de vouloir changer votre mot de passe?");
            if ($confirmChange) {
                $user->setPassword($this->passwordEncoder->hashPassword($user, $newPassword));
                $this->entityManager->flush();
            }
        }

        // Handle changing email
        $newEmail = $request->request->get('new_email');
        if ($newEmail !== null) {
            $confirmChange = $this->confirmChange("Êtes-vous sûr de vouloir changer votre adresse email?");
            if ($confirmChange) {
                $user->setEmail($newEmail);
                $this->entityManager->flush();
            }
        }

        // Handle profile deletion
        if ($request->request->has('delete_profile')) {
            $confirmDeletion = $this->confirmChange("Êtes-vous sûr de vouloir supprimer votre profil?");
            if ($confirmDeletion) {
                $this->entityManager->remove($user);
                $this->entityManager->flush();

                // Logout the user and redirect to the homepage
                $this->tokenStorage->setToken(null);
                $request->getSession()->invalidate();
                return $this->redirectToRoute('index');
            }
        }

        return $this->render('profils/index.html.twig', [
            'activeTab' => 'profils',
            'user' => $user,
        ]);
    }

    private function confirmChange(string $message): bool
    {
        // Replace this part with the actual code for asking user confirmation
        // If dialog.helper is not available, you may need to implement a confirmation mechanism
        // For simplicity, you can use the built-in `confirm` JavaScript function in your template

        // Example: return $this->container->get('dialog.helper')->askConfirmation($message, false);
        return true; // Modify this line accordingly
    }
}