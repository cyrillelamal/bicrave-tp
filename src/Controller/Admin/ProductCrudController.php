<?php

namespace App\Controller\Admin;

use App\Common\Currency\Currency;
use App\Common\Currency\CurrencyProviderInterface;
use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    private CurrencyProviderInterface $currencyProvider;

    public function __construct(
        CurrencyProviderInterface $currencyProvider,
    )
    {
        $this->currencyProvider = $currencyProvider;
    }

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('product.acc.s')
            ->setEntityLabelInPlural('product.nom.pl')
            ->setPageTitle(Crud::PAGE_EDIT, 'product.edit')
            ->setPageTitle(Crud::PAGE_NEW, 'product.new')
            ->setSearchFields(['name', 'category.name'])
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'product.fields.id')
            ->hideOnForm();
        yield TextField::new('name', 'product.fields.name');
        yield MoneyField::new('cost', 'product.fields.cost')
            ->setCurrency($this->getApplicationCurrency()->getCode());
        yield IntegerField::new('rest', 'product.fields.rest');
        yield DateTimeField::new('createdAt', 'product.fields.created_at')
            ->hideOnForm();
        yield AssociationField::new('category', 'product.fields.category');
        yield TextareaField::new('description', 'product.fields.description');
    }

    protected function getApplicationCurrency(): Currency
    {
        return $this->getCurrencyProvider()->getCurrency();
    }

    protected function getCurrencyProvider(): CurrencyProviderInterface
    {
        return $this->currencyProvider;
    }
}
