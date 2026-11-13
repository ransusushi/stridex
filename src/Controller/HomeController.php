<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        if ($this->isGranted(User::ROLE_ADMIN)) {
            return $this->redirectToRoute('app_admin_dashboard');
        }

        if ($this->isGranted(User::ROLE_STAFF)) {
            return $this->redirectToRoute('app_staff_dashboard');
        }

        return $this->redirectToRoute('app_storefront');
    }
}
