<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\DBAL\Connection;

#[Route('/admin', name: 'admin_')]
class AdminDashboardController extends AbstractController
{
    #[Route('', name: 'dashboard')]
    public function index(Connection $db): Response
    {
        $stats = [
            'orders'   => (int) $db->fetchOne('SELECT COUNT(*) FROM orders'),
            'products' => (int) $db->fetchOne('SELECT COUNT(*) FROM products'),
            'users'    => (int) $db->fetchOne('SELECT COUNT(*) FROM users'),
            'messages' => (int) $db->fetchOne('SELECT COUNT(*) FROM messages'),
        ];

        return $this->render('admin_dashboard/index.html.twig', [
            'stats' => $stats,
        ]);
    }
}