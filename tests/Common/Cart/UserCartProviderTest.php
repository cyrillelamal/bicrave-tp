<?php

namespace App\Tests\Common\Cart;

use App\Common\Cart\UserCartProvider;
use App\Entity\Cart;
use App\Entity\User;
use App\Repository\CartRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Security;

class UserCartProviderTest extends KernelTestCase
{
    /**
     * @test
     */
    public function it_cannot_provide_cart_for_anonymous_user(): void
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturn(false);

        $provider = new UserCartProvider(
            $security,
            $this->createMock(CartRepository::class)
        );

        $this->assertFalse($provider->canProvideCart());
    }

    /**
     * @test
     */
    public function it_can_provide_cart_if_user_is_authenticated(): void
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturn(true);

        $provider = new UserCartProvider(
            $security,
            $this->createMock(CartRepository::class)
        );

        $this->assertTrue($provider->canProvideCart());
    }

    /**
     * @test
     */
    public function it_provides_the_user_cart(): void
    {
        $user = new User();
        $cart = new Cart();
        $user->setCart($cart);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturn(true);
        $security->method('getUser')->willReturn($user);
        $repository = $this->createMock(CartRepository::class);
        $repository->method('findByUserJoinDemands')->willReturn($user->getCart());

        $provider = new UserCartProvider(
            $security,
            $repository,
        );

        $this->assertSame($cart, $provider->getCart());
    }
}
