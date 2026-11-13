<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(\sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * @return list<User>
     */
    public function findByFilters(?string $search = null, ?string $role = null, ?string $status = null): array
    {
        $qb = $this->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC')
            ->addOrderBy('u.id', 'DESC');

        if (null !== $search && '' !== trim($search)) {
            $term = '%'.strtolower(trim($search)).'%';
            $qb->andWhere('(LOWER(u.name) LIKE :term OR LOWER(u.email) LIKE :term)')
                ->setParameter('term', $term);
        }

        if (null !== $status && '' !== trim($status)) {
            $qb->andWhere('u.status = :status')
                ->setParameter('status', trim($status));
        }

        /** @var list<User> $users */
        $users = $qb->getQuery()->getResult();

        if (null !== $role && '' !== trim($role)) {
            $roleFilter = trim($role);
            $users = array_values(array_filter(
                $users,
                static fn (User $u): bool => $u->getPrimaryRole() === $roleFilter
            ));
        }

        return $users;
    }

    /**
     * @return array{
     *     totalUsers: int,
     *     totalStaff: int,
     *     totalAdmins: int,
     *     activeUsers: int,
     *     inactiveUsers: int
     * }
     */
    public function getUserCounts(): array
    {
        /** @var list<User> $users */
        $users = $this->findAll();

        $totalUsers = \count($users);
        $totalStaff = 0;
        $totalAdmins = 0;
        $activeUsers = 0;
        $inactiveUsers = 0;

        foreach ($users as $user) {
            if ($user->isAdmin()) {
                ++$totalAdmins;
            } elseif ($user->isStaff()) {
                ++$totalStaff;
            }

            if ($user->isActive()) {
                ++$activeUsers;
            } else {
                ++$inactiveUsers;
            }
        }

        return [
            'totalUsers' => $totalUsers,
            'totalStaff' => $totalStaff,
            'totalAdmins' => $totalAdmins,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
        ];
    }

    /**
     * @return list<User>
     */
    public function findRecent(int $limit = 5): array
    {
        /** @var list<User> $result */
        $result = $this->createQueryBuilder('u')
            ->orderBy('u.createdAt', 'DESC')
            ->addOrderBy('u.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $result;
    }
}
