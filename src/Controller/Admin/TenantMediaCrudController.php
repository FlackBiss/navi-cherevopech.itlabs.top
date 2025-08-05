<?php

namespace App\Controller\Admin;

use App\Controller\Admin\Field\VichFileField;
use App\Controller\Admin\Field\VichImageField;
use App\Entity\TenantMedia;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TenantMediaCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TenantMedia::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield VichFileField::new('mediaFile', 'Медиа')
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
            ->setFormTypeOption('allow_delete', false);
    }
}