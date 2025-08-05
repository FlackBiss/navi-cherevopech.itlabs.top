<?php

namespace App\Controller\Admin;

use App\Entity\Queries;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class QueriesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Queries::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInPlural('Запросы')
            ->setEntityLabelInSingular('запрос');
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->disable(Action::NEW, Action::DELETE, Action::EDIT);

        return parent::configureActions($actions);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield AssociationField::new('tenant', 'Объект карты');
        yield TextField::new('type', 'Тип');
        yield DateTimeField::new('createdAt', 'Дата создания');
    }
}
