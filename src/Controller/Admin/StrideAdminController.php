<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\ContactMessage;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\ContactMessageRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted(User::ROLE_ADMIN)]
class StrideAdminController extends AbstractController
{
    #[Route('/orders', name: 'app_admin_orders', methods: ['GET'])]
    public function orders(OrderRepository $orderRepository): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        return $this->render('admin/orders/index.html.twig', [
            'orders' => $orderRepository->findAllOrderedByRecent(),
            'revenueStats' => $orderRepository->getRevenueStats(),
        ]);
    }

    #[Route('/orders/{id}', name: 'app_admin_order_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showOrder(Order $order): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        return $this->render('admin/orders/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/orders/{id}/status', name: 'app_admin_order_status', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function updateOrderStatus(
        Request $request,
        Order $order,
        EntityManagerInterface $entityManager,
    ): Response {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        if (!$this->isCsrfTokenValid('order_status_'.$order->getId(), $request->request->getString('_token'))) {
            $this->addFlash('error', 'Invalid CSRF security token.');

            return $this->redirectToRoute('app_admin_orders');
        }

        $status = $request->request->getString('status');
        if (\in_array($status, [Order::STATUS_PENDING, Order::STATUS_PAID, Order::STATUS_SHIPPED, Order::STATUS_DELIVERED], true)) {
            $order->setStatus($status);
            $entityManager->flush();
            $this->addFlash('success', \sprintf('Order #%d status updated to %s.', $order->getId(), strtoupper($status)));
        }

        return $this->redirectToRoute('app_admin_orders');
    }

    #[Route('/inventory', name: 'app_admin_inventory', methods: ['GET'])]
    public function inventory(ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        return $this->render('admin/inventory.html.twig', [
            'products' => $productRepository->findByFilters(),
            'lowStockCount' => $productRepository->countLowStock(),
        ]);
    }

    #[Route('/inventory/{id}/stock', name: 'app_admin_inventory_stock', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function updateStock(
        Request $request,
        Product $product,
        EntityManagerInterface $entityManager,
    ): Response {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        if (!$this->isCsrfTokenValid('update_stock_'.$product->getId(), $request->request->getString('_token'))) {
            $this->addFlash('error', 'Invalid CSRF security token.');

            return $this->redirectToRoute('app_admin_inventory');
        }

        $newQty = max(0, $request->request->getInt('quantity', 0));
        $product->setStockQuantity($newQty);
        $product->touch();
        $entityManager->flush();

        $this->addFlash('success', \sprintf('Updated stock for "%s" to %d units.', $product->getName(), $newQty));

        return $this->redirectToRoute('app_admin_inventory');
    }

    #[Route('/messages', name: 'app_admin_messages', methods: ['GET'])]
    public function messages(ContactMessageRepository $messageRepository): Response
    {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        return $this->render('admin/messages.html.twig', [
            'messages' => $messageRepository->findAllOrderedByRecent(),
            'unreadCount' => $messageRepository->countUnread(),
        ]);
    }

    #[Route('/messages/{id}/read', name: 'app_admin_message_read', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function markMessageRead(
        Request $request,
        ContactMessage $contactMessage,
        EntityManagerInterface $entityManager,
    ): Response {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        if ($this->isCsrfTokenValid('read_msg_'.$contactMessage->getId(), $request->request->getString('_token'))) {
            $contactMessage->setIsRead(true);
            $entityManager->flush();
            $this->addFlash('success', 'Message marked as read.');
        }

        return $this->redirectToRoute('app_admin_messages');
    }

    #[Route('/messages/{id}/delete', name: 'app_admin_message_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function deleteMessage(
        Request $request,
        ContactMessage $contactMessage,
        EntityManagerInterface $entityManager,
    ): Response {
        $this->denyAccessUnlessGranted(User::ROLE_ADMIN);

        if ($this->isCsrfTokenValid('delete_msg_'.$contactMessage->getId(), $request->request->getString('_token'))) {
            $entityManager->remove($contactMessage);
            $entityManager->flush();
            $this->addFlash('success', 'Message deleted.');
        }

        return $this->redirectToRoute('app_admin_messages');
    }
}
