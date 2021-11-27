<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ImageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Image::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('image.acc.s')
            ->setEntityLabelInPlural('image.nom.pl')
            ->setPageTitle(Crud::PAGE_NEW, 'image.new')
            ->setSearchFields(['product.name'])
            ->setDefaultSort(['uploadedAt' => 'DESC'])
            ->showEntityActionsInlined();
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'image.fields.id')
            ->hideOnForm();
        yield TextField::new('file', '')
            ->setFormType(VichImageType::class)
            ->onlyOnForms();
        yield ImageField::new('name', '')
            ->setBasePath($this->getParameter('app.path.product_images'))
            ->onlyOnIndex();
        yield AssociationField::new('product', 'image.fields.product');
        yield DateTimeField::new('uploadedAt', 'image.fields.uploaded_at')
            ->hideOnForm();
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->remove(Crud::PAGE_INDEX, Action::EDIT);
    }
}
