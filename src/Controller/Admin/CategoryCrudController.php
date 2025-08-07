<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\VichImageField;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInPlural('Категории')
            ->setEntityLabelInSingular('категорию')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавление категории')
            ->setPageTitle(Crud::PAGE_EDIT, 'Изменение категории');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();

        yield VichImageField::new('imageFile', 'Изображение')
            ->setHelp('
                <div class="mt-3">
                    <span class="badge badge-info">*.svg</span>
                </div>
            ')
            ->setFormTypeOption('allow_delete', false)
            ->onlyOnForms();

        yield VichImageField::new('image', 'Изображение')
            ->hideOnForm();

        yield ChoiceField::new('title', 'Название категории')
            ->setChoices(
                [
                    'Парки' => 'Парки',
                    'Культура' => 'Культура',
                    'Развлечения' => 'Развлечения',
                    'Архитектура' => 'Архитектура',
                    'Музеи' => 'Музеи',
                    'Спорт' => 'Спорт',
                ]
            );
    }
}
