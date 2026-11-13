<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AccountStatusUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        if (User::STATUS_DISABLED === $user->getStatus()) {
            throw new CustomUserMessageAccountStatusException('Your account has been disabled. Please contact an administrator.');
        }

        if (User::STATUS_ARCHIVED === $user->getStatus()) {
            throw new CustomUserMessageAccountStatusException('Your account has been archived and can no longer sign in.');
        }
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
    }
}
