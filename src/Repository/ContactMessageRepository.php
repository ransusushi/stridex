<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ContactMessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ContactMessage>
 */
class ContactMessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ContactMessage::class);
    }

    /**
     * @return list<ContactMessage>
     */
    public function findAllOrderedByRecent(): array
    {
        /** @var list<ContactMessage> $result */
        $result = $this->createQueryBuilder('m')
            ->orderBy('m.createdAt', 'DESC')
            ->addOrderBy('m.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function countUnread(): int
    {
        return (int) $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.isRead = :unread')
            ->setParameter('unread', false)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
