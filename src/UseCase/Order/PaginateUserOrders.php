<?php

namespace App\UseCase\Order;

use App\Entity\Product;
use App\Repository\OrderRepository;
use App\Security\Role;
use Doctrine\ORM\Query;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Get paginated user orders.
 */
final class PaginateUserOrders
{
    public const LIMIT = 10;

    private PaginatorInterface $paginator;
    private OrderRepository $repository;
    private RequestStack $requestStack;
    private Security $security;

    public function __construct(
        PaginatorInterface $paginator,
        OrderRepository    $repository,
        RequestStack       $requestStack,
        Security           $security,
    )
    {
        $this->paginator = $paginator;
        $this->repository = $repository;
        $this->requestStack = $requestStack;
        $this->security = $security;
    }

    /**
     * Get paginated user orders.
     *
     * @return PaginationInterface<Product>
     */
    public function __invoke(): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->getTarget(),
            $this->getPage(),
            self::LIMIT,
        );
    }

    private function getTarget(): Query
    {
        return $this->repository->getQueryForProfilePagination($this->getUser());
    }

    private function getUser(): UserInterface
    {
        if (!$this->security->isGranted(Role::CUSTOMER)) {
            throw new AccessDeniedException();
        }

        return $this->security->getUser();
    }

    private function getPage(): int
    {
        return (int)$this->getRequest()->query->get('page', 1);
    }

    private function getRequest(): Request
    {
        return $this->requestStack->getMainRequest() ?? Request::createFromGlobals();
    }
}
