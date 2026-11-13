<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $userRepository,
        private readonly ProductRepository $productRepository,
    ) {
    }

    public function createUser(User $user, string $plainPassword, string $role): User
    {
        $user->setPrimaryRole($role);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function updateUser(User $user, string $role, ?string $plainPassword = null): User
    {
        $user->setPrimaryRole($role);

        if (null !== $plainPassword && '' !== trim($plainPassword)) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
            $user->setPassword($hashedPassword);
        }

        $this->entityManager->flush();

        return $user;
    }

    public function changePassword(User $user, string $newPlainPassword): void
    {
        $hashedPassword = $this->passwordHasher->hashPassword($user, $newPlainPassword);
        $user->setPassword($hashedPassword);
        $this->entityManager->flush();
    }

    public function isPasswordValid(User $user, string $plainPassword): bool
    {
        return $this->passwordHasher->isPasswordValid($user, $plainPassword);
    }

    public function updateStatus(User $user, string $status): void
    {
        $user->setStatus($status);
        $this->entityManager->flush();
    }

    public function deleteUser(User $user): void
    {
        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    /**
     * @return array{
     *     totalUsers: int,
     *     totalStaff: int,
     *     totalAdmins: int,
     *     activeUsers: int,
     *     inactiveUsers: int,
     *     totalRecords: int,
     *     lowStockRecords: int
     * }
     */
       public function getAdminDashboardSummary(): array
    {
        $users = $this->userRepository->findAll();

        $totalUsers = count($users);
        $totalAdmins = 0;
        $totalStaff = 0;
        $activeUsers = 0;
        $inactiveUsers = 0;

        foreach ($users as $user) {
            if ($user->isActive()) { $activeUsers++; } else { $inactiveUsers++; }
            if ($user->isAdmin()) { $totalAdmins++; }
            elseif ($user->isStaff()) { $totalStaff++; }
        }

        return [
            'totalUsers' => $totalUsers,
            'totalAdmins' => $totalAdmins,
            'totalStaff' => $totalStaff,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'totalRecords' => $this->productRepository->countAllRecords(),
            'lowStockRecords' => $this->productRepository->countLowStock(),
        ];
    }
}
