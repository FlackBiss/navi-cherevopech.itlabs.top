<?php

namespace App\Controller\Admin;

use App\Entity\Tenant;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Component\HttpFoundation\File\File;

class TenantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tenant::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInPlural('Объекты карты')
            ->setEntityLabelInSingular('объект карты')
            ->setPageTitle(Crud::PAGE_NEW, 'Добавление объекта карты')
            ->setPageTitle(Crud::PAGE_EDIT, 'Изменение объекта карты');
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->createQr($entityManager, $entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->createQr($entityManager, $entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    public function createQr(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $writer = new PngWriter();
        $qrCode = new QrCode(
            data: $entityInstance->getLink(),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        $result = $writer->write($qrCode);
        $outputQrCodePath = $this->getUniqueFilePath();
        $result->saveToFile($outputQrCodePath);

        $entityInstance->setImage(basename($outputQrCodePath))->setImageFile(new File($outputQrCodePath));
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('Главная');
        yield IdField::new('id')->onlyOnIndex();
        yield TextField::new('title', 'Название')
            ->setColumns(8);
        yield TextEditorField::new('description', 'Описание')
            ->setColumns(8);
        yield TextField::new('link', 'Ссылка')
            ->setColumns(8);
        yield AssociationField::new('category', 'Категория')
            ->setColumns(8);
        yield FormField::addTab('Медиа');
        yield CollectionField::new('medias', 'Медиа')
            ->onlyOnForms()
            ->setRequired(false)
            ->useEntryCrudForm(TenantMediaCrudController::class);

        yield FormField::addTab('Слова для поиска');
        yield ArrayField::new('searchAliases', 'Слова')
            ->setColumns(8)
            ->hideOnIndex();
    }

    private function getUniqueFilePath(): string
    {
        $uniqueName = uniqid('tenant', true);
        $dirPath = $this->getParameter('kernel.project_dir') . "/public/images/tenant";

        if (!is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        return "$dirPath/$uniqueName.png";
    }
}