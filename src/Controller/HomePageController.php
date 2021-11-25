<?php

namespace App\Controller;

use App\Repository\Common\MutatorInterface;
use App\Repository\ProductRepository;
use DateTime;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
    private ProductRepository $productRepository;

    public function __construct(
        ProductRepository $productRepository,
    )
    {
        $this->productRepository = $productRepository;
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        $novelties = $this->productRepository->getNovelties(new class implements MutatorInterface {
            public function mutateQueryBuilder(QueryBuilder $builder): void
            {
                $builder->setMaxResults(8);
            }

            public function mutateQuery(Query $query): void
            {
                $query->useQueryCache(true)->enableResultCache(3600);
            }
        });

        $popular = $this->productRepository->getPopularProducts(new class implements MutatorInterface {
            public function mutateQueryBuilder(QueryBuilder $builder): void
            {
                $builder->setMaxResults(8);
                $builder->andWhere('product.createdAt BETWEEN :start and :end');
                $builder->setParameter('start', new DateTime('-30 days'));
                $builder->setParameter('end', new DateTime('now'));
            }

            public function mutateQuery(Query $query): void
            {
                $query->useQueryCache(true)->enableResultCache(2 * 3600);
            }
        });

        return $this->render('home_page/index.html.twig', [
            'new_products' => $novelties,
            'popular_products' => $popular,
        ]);
    }
}
