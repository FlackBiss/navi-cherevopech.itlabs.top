<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\VichImageField;
use App\Entity\Floor;
use App\Repository\FloorRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class FloorCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Floor::class;
    }

    public function __construct(
        protected readonly FloorRepository $floorRepository,
    )
    {
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInPlural('Этажи')
            ->setEntityLabelInSingular('этаж')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавление этажа')
            ->setPageTitle(Crud::PAGE_EDIT, 'Изменение этажа');
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->remove(Crud::PAGE_NEW, Action::SAVE_AND_ADD_ANOTHER);
        $actions->remove(Crud::PAGE_NEW, Action::SAVE_AND_RETURN);
        $actions->remove(Crud::PAGE_EDIT, Action::SAVE_AND_RETURN);
        $actions->add(Crud::PAGE_NEW, Action::SAVE_AND_CONTINUE);

        return parent::configureActions($actions);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield NumberField::new('zoomStart', 'Стартовое значение масштаба')
            ->setColumns(3)
            ->setHelp('Базовый масштаб = 1.0. Допустимо: от 1.0 (удаление) до 10.0 (приближение)');

        $image = VichImageField::new('mapImageFile', 'Карта')
            ->setHelp('
                <div class="mt-3">
                    <span class="badge badge-info">*.jpg</span>
                    <span class="badge badge-info">*.jpeg</span>
                    <span class="badge badge-info">*.png</span>
                    <span class="badge badge-info">*.jiff</span>
                    <span class="badge badge-info">*.webp</span>
                </div>
            ')
            ->onlyOnForms()
            ->setFormTypeOption('allow_delete', false)
            ->setRequired(true);

        if (Crud::PAGE_EDIT == $pageName) {
            $image->setRequired(false);
        }

        yield $image;
        yield VichImageField::new('mapImage', 'Карта')
            ->hideOnForm();
    }
}