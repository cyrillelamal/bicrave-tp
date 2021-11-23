<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle($this->getParameter('app.name'))
            ->setTranslationDomain('admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('category.nom.pl', 'fa fa-book', Category::class);
        yield MenuItem::linkToCrud('product.nom.pl', 'fa fa-barcode', Product::class);
        yield MenuItem::linkToCrud('image.nom.pl', 'fa fa-image', Image::class);
    }
}
