<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPagesController extends AbstractController
{
    #[Route('/admin/orders', name: 'admin_orders')]
    public function orders(): Response
    {
        return $this->render('admin/pages/orders.html.twig');
    }

    #[Route('/admin/products', name: 'admin_products')]
    public function products(): Response
    {
        return $this->render('admin/pages/products.html.twig');
    }

    #[Route('/admin/inventory', name: 'admin_inventory')]
    public function inventory(): Response
    {
        return $this->render('admin/pages/inventory.html.twig');
    }

    #[Route('/admin/customers', name: 'admin_customers')]
    public function customers(): Response
    {
        return $this->render('admin/pages/customers.html.twig');
    }

    #[Route('/admin/messages', name: 'admin_messages')]
    public function messages(): Response
    {
        return $this->render('admin/pages/messages.html.twig');
    }

    #[Route('/admin/reviews', name: 'admin_reviews')]
    public function reviews(): Response
    {
        return $this->render('admin/pages/reviews.html.twig');
    }
}