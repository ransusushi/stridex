<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StaffController extends AbstractController
{
    #[Route('/dashboard', name: 'app_staff_dashboard', methods: ['GET'])]
    #[IsGranted(User::ROLE_STAFF)]
    public function index(ProductRepository $productRepository): Response
    {
        if ($this->isGranted(User::ROLE_ADMIN)) {
            return $this->redirectToRoute('app_admin_dashboard');
        }

        /** @var User $user */
        $user = $this->getUser();

        $myProducts = $productRepository->findByFilters(owner: $user);

        return $this->render('dashboard/staff.html.twig', [
            'myProductCount' => $productRepository->countByOwner($user),
            'totalSharedProducts' => $productRepository->countAllRecords(),
            'lowStockCount' => $productRepository->countLowStock(),
            'myRecentProducts' => \array_slice($myProducts, 0, 5),
            'recentSharedProducts' => $productRepository->findRecent(5),
        ]);
    }
}
