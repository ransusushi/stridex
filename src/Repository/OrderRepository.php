<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @return list<Order>
     */
    public function findAllOrderedByRecent(?User $user = null): array
    {
        $qb = $this->createQueryBuilder('o')
            ->leftJoin('o.items', 'i')
            ->addSelect('i')
            ->orderBy('o.createdAt', 'DESC')
            ->addOrderBy('o.id', 'DESC');

        if (null !== $user) {
            $qb->andWhere('o.user = :user')
                ->setParameter('user', $user);
        }

        /** @var list<Order> $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * @return list<Order>
     */
    public function findRecent(int $limit = 5): array
    {
        /** @var list<Order> $result */
        $result = $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'DESC')
            ->addOrderBy('o.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $result;
    }

    /**
     * @return array{totalRevenue: float, completedOrders: int, totalOrders: int}
     */
    public function getRevenueStats(): array
    {
        /** @var list<Order> $orders */
        $orders = $this->findAll();
        $totalRevenue = 0.0;
        $completedOrders = 0;

        foreach ($orders as $order) {
            if (\in_array($order->getStatus(), [Order::STATUS_PAID, Order::STATUS_SHIPPED, Order::STATUS_DELIVERED], true)) {
                $totalRevenue += (float) $order->getTotal();
                ++$completedOrders;
            }
        }

        return [
            'totalRevenue' => $totalRevenue,
            'completedOrders' => $completedOrders,
            'totalOrders' => \count($orders),
        ];
    }
}
