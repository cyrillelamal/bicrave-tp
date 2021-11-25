<?php

namespace App\MessageHandler\Product;

use App\Entity\Product;
use App\Message\Product\PaginateCategoryProductsMessage;
use App\Repository\ProductRepository;
use Doctrine\ORM\Query;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class PaginateCategoryProductsMessageHandler implements MessageHandlerInterface
{
    public const LIMIT = 12;

    private ProductRepository $repository;
    private PaginatorInterface $paginator;
    private RequestStack $requestStack;

    public function __construct(
        ProductRepository  $repository,
        PaginatorInterface $paginator,
        RequestStack       $requestStack,
    )
    {
        $this->repository = $repository;
        $this->paginator = $paginator;
        $this->requestStack = $requestStack;
    }

    /**
     * @param PaginateCategoryProductsMessage $message
     * @return PaginationInterface<Product>
     */
    public function __invoke(PaginateCategoryProductsMessage $message): PaginationInterface
    {
        return $this->paginate($message); // TODO: cache
    }

    private function paginate(PaginateCategoryProductsMessage $message): PaginationInterface
    {
        $request = $this->getRequest();

        return $this->paginator->paginate(
            $this->getTarget($message->getCategoryId()),
            (int)$request->query->get('page', 1),
            self::LIMIT
        );
    }

    private function getTarget(int $categoryId): Query
    {
        return $this->repository->getQueryForCategoryPagination($categoryId);
    }

    private function getRequest(): Request
    {
        // It seems more convenient to use the global request because the KnpPaginator already doest it.
        return $this->requestStack->getMainRequest() ?? Request::createFromGlobals();
    }
}
