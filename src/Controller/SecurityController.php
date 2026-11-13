<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() instanceof User) {
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(Request $request, UserManager $userManager, UserRepository $userRepository): Response
    {
        if ($this->getUser() instanceof User) {
            return $this->redirectToRoute('app_home');
        }

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('register', $request->request->getString('_token'))) {
                $this->addFlash('error', 'Invalid security token. Please try again.');

                return $this->redirectToRoute('app_register');
            }

            $firstName = trim((string) $request->request->get('first_name', ''));
            $lastName = trim((string) $request->request->get('last_name', ''));
            $email = strtolower(trim((string) $request->request->get('email', '')));
            $password = (string) $request->request->get('password', '');

            if ('' === $firstName || '' === $lastName || '' === $email || '' === $password) {
                $this->addFlash('error', 'Please complete all fields to create your account.');
            } elseif (!filter_var($email, \FILTER_VALIDATE_EMAIL)) {
                $this->addFlash('error', 'Please enter a valid email address.');
            } elseif (strlen($password) < 6) {
                $this->addFlash('error', 'Your password must be at least 6 characters long.');
            } elseif ($userRepository->findOneBy(['email' => $email])) {
                $this->addFlash('error', 'An account with this email already exists.');
            } else {
                $user = (new User())
                    ->setFirstName($firstName)
                    ->setLastName($lastName)
                    ->setEmail($email)
                    ->setStatus(User::STATUS_ACTIVE);

                $userManager->createUser($user, $password, User::ROLE_USER);

                $this->addFlash('success', 'Your account has been created. Please sign in.');

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/register.html.twig');
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['GET', 'POST'])]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
