<?php

namespace App\Event;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

class UserLoggedInEvent extends Event
{
    public const NAME = 'security.logged_in';

    private UserInterface $user;

    #[Pure] public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    #[Pure] public function getUser(): UserInterface
    {
        return $this->user;
    }
}
