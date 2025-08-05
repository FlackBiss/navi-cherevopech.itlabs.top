<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\VichFileField;
use App\Controller\Admin\Field\VichImageField;
use App\Entity\StandBy;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class StandByCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return StandBy::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInPlural('Режим ожидания')
            ->setEntityLabelInSingular('файл')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавление файла')
            ->setPageTitle(Crud::PAGE_EDIT, 'Изменение файла');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();

        $image = VichFileField::new('mediaFile', 'Медиа')
            ->setHelp('
                <div class="mt-3">
                    <span class="badge badge-info">*.jpg</span>
                    <span class="badge badge-info">*.jpeg</span>
                    <span class="badge badge-info">*.png</span>
                    <span class="badge badge-info">*.jiff</span>
                    <span class="badge badge-info">*.webp</span>
                    <span class="badge badge-info">*.mp4</span>
                    <span class="badge badge-info">*.webm</span>
                </div>
            ')
            ->onlyOnForms()
            ->setFormTypeOption('allow_delete', false)
            ->setRequired(true);

        if (Crud::PAGE_EDIT == $pageName) {
            $image->setRequired(false);
        }

        yield $image;
        yield VichFileField::new('media', 'Медиа')
            ->onlyOnIndex();

        yield BooleanField::new('view', 'Виден ли файл');

        yield IntegerField::new('sequence', 'Порядок отображения');
    }
}
