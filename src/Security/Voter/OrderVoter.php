<?php

namespace App\Security\Voter;

use App\Entity\Order;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class OrderVoter extends Voter
{
    public const READ = 'ORDER_READ';

    public const ATTRIBUTES = [
        self::READ,
    ];

    /**
     * {@inheritDoc}
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, self::ATTRIBUTES) && $subject instanceof Order;
    }

    /**
     * {@inheritDoc}
     * @param Order $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::READ => $subject->isCreatedBy($user),
            default => false,
        };
    }
}
