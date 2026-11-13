<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @extends Voter<string, Product>
 */
class ProductVoter extends Voter
{
    public const VIEW = 'PRODUCT_VIEW';
    public const EDIT = 'PRODUCT_EDIT';
    public const DELETE = 'PRODUCT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::VIEW, self::EDIT, self::DELETE], true)
            && $subject instanceof Product;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User || !$user->isActive()) {
            return false;
        }

        // Admins have full system-wide access to view, edit, and delete ANY record
        if ($user->isAdmin()) {
            return true;
        }

        // Staff members can view records, and can ONLY edit or delete their OWN records
        return match ($attribute) {
            self::VIEW => true,
            self::EDIT, self::DELETE => null !== $subject->getCreatedBy()
                && null !== $subject->getCreatedBy()->getId()
                && $subject->getCreatedBy()->getId() === $user->getId(),
            default => false,
        };
    }
}
