<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\ContactMessageRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted(User::ROLE_ADMIN)]
class DashboardController extends AbstractController
{
    #[Route('', name: 'app_admin_dashboard', methods: ['GET'])]
    public function index(
        UserManager $userManager,
        UserRepository $userRepository,
        ProductRepository $productRepository,
        OrderRepository $orderRepository,
        ContactMessageRepository $messageRepository,
    ): Response {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        $summary = $userManager->getAdminDashboardSummary();
        $revenueStats = $orderRepository->getRevenueStats();

        return $this->render('admin/dashboard.html.twig', [
            'summary' => $summary,
            'revenueStats' => $revenueStats,
            'unreadMessages' => $messageRepository->countUnread(),
            'recentUsers' => $userRepository->findRecent(5),
            'recentProducts' => $productRepository->findRecent(6),
            'recentOrders' => $orderRepository->findRecent(5),
        ]);
    }
}
