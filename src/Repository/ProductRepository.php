<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * @return list<Product>
     */
    public function findByFilters(
        ?string $search = null,
        ?string $category = null,
        ?User $owner = null,
        ?string $stockStatus = null,
    ): array {
        $qb = $this->createQueryBuilder('p')
            ->innerJoin('p.createdBy', 'u')
            ->addSelect('u')
            ->orderBy('p.createdAt', 'DESC')
            ->addOrderBy('p.id', 'DESC');

        if (null !== $search && '' !== trim($search)) {
            $term = '%'.strtolower(trim($search)).'%';
            $qb->andWhere('(LOWER(p.name) LIKE :term OR LOWER(p.sku) LIKE :term OR LOWER(p.description) LIKE :term OR LOWER(p.color) LIKE :term)')
                ->setParameter('term', $term);
        }

        if (null !== $category && '' !== trim($category)) {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', trim($category));
        }

        if (null !== $owner) {
            $qb->andWhere('p.createdBy = :owner')
                ->setParameter('owner', $owner);
        }

        if ('low' === $stockStatus) {
            $qb->andWhere('p.stockQuantity > 0 AND p.stockQuantity <= 10');
        } elseif ('out' === $stockStatus) {
            $qb->andWhere('p.stockQuantity = 0');
        } elseif ('in_stock' === $stockStatus) {
            $qb->andWhere('p.stockQuantity > 0');
        }

        /** @var list<Product> $result */
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    public function countAllRecords(): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countByOwner(User $owner): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.createdBy = :owner')
            ->setParameter('owner', $owner)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function countLowStock(int $threshold = 10): int
    {
        return (int) $this->createQueryBuilder('p')
            ->select('COUNT(p.id)')
            ->andWhere('p.stockQuantity <= :threshold')
            ->setParameter('threshold', $threshold)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getInventoryValue(): float
    {
        return (float) $this->createQueryBuilder('p')
            ->select('COALESCE(SUM(p.price * p.stockQuantity), 0)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return list<Product>
     */
    public function findRecent(int $limit = 6): array
    {
        /** @var list<Product> $result */
        $result = $this->createQueryBuilder('p')
            ->innerJoin('p.createdBy', 'u')
            ->addSelect('u')
            ->orderBy('p.createdAt', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $result;
    }
}
