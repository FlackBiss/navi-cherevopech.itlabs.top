<?php

namespace App\Controller\Admin;

use App\Entity\Settings;
use App\Entity\StandBy;
use App\Entity\User;
use App\Repository\SettingsRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route(path: '/admin', name: 'admin')]
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('
                <span>Админ-панель</span>
            ')
            ->setFaviconPath('favicon.ico')
            ->renderContentMaximized()
            ->generateRelativeUrls();
    }

    public function __construct(private readonly SettingsRepository $settingsRepository,)
    {
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToCrud('Режим ожидания', 'fa fa-terminal', StandBy::class);
        yield MenuItem::section('Настройки');
        if ($this->settingsRepository->count([]) === 0) {
            yield MenuItem::linkToCrud('Настройки', 'fa fa-gear', Settings::class)
                ->setAction(Action::NEW);
        } else {
            yield MenuItem::linkToCrud('Настройки', 'fa fa-gear', Settings::class)
                ->setAction(Action::EDIT)->setEntityId($this->settingsRepository->findAll()[0]->getId());
        }
        yield MenuItem::linkToCrud('Пользователи', 'fas fa-user-gear', User::class)
            ->setPermission('ROLE_ADMIN');
        yield MenuItem::linkToUrl('API', 'fa fa-link', '/api')->setLinkTarget('_blank')
            ->setPermission('ROLE_ADMIN');
    }
}
