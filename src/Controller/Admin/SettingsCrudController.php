<?php

namespace App\Controller\Admin;

use App\Entity\Settings;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class SettingsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Settings::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInPlural('Настройки')
            ->setEntityLabelInSingular('настройку')
            ->setPageTitle(Crud::PAGE_NEW, 'Создать настройку')
            ->setPageTitle(Crud::PAGE_EDIT, 'Редактировать настройку');
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
        yield IdField::new('id')->hideOnForm();

        yield IntegerField::new('timeImages', 'Время показа изображений (сек)');
        yield IntegerField::new('timeDelay', 'Переход в режим бездействия терминала (сек)');
        yield IntegerField::new('timePopup', 'Время до открытия окна предупреждения о переходе в режим бездействия (сек)');
        yield TextField::new('password', 'Пароль');
    }
}
