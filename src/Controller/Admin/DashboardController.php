<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Floor;
use App\Entity\Functions;
use App\Entity\Infrastructure;
use App\Entity\NodeType;
use App\Entity\Queries;
use App\Entity\Sessions;
use App\Entity\Settings;
use App\Entity\StandBy;
use App\Entity\Tenant;
use App\Entity\Terminal;
use App\Entity\User;
use App\Repository\FloorRepository;
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
        return $this->redirect($adminUrlGenerator->setController(CategoryCrudController::class)->generateUrl());
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

    public function __construct(
        private readonly SettingsRepository $settingsRepository,
        private readonly FloorRepository $floorRepository,
    )
    {
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::section('Карта');
        if ($this->floorRepository->count([]) === 0) {
            yield MenuItem::linkToCrud('Карта', 'fas fa-sort', Floor::class)
                ->setAction(Action::NEW);
        } else {
            yield MenuItem::linkToCrud('Карта', 'fas fa-sort', Floor::class)
                ->setAction(Action::EDIT)->setEntityId($this->floorRepository->findAll()[0]->getId());
        }
        yield MenuItem::linkToCrud('Объекты карты', 'fas fa-door-closed', Tenant::class);
        yield MenuItem::linkToCrud('Терминалы', 'fas fa-display', Terminal::class);

        yield MenuItem::section('Аналитика');
        yield MenuItem::linkToCrud('Запросы', 'fas fa-list', Queries::class);
        yield MenuItem::linkToCrud('Функции', 'fa fa-list', Functions::class);
        yield MenuItem::linkToCrud('Сессии', 'fa fa-clock', Sessions::class);
        yield MenuItem::section('Информация');
        yield MenuItem::linkToCrud('Категории', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Режим ожидания', 'fa fa-terminal', StandBy::class);
        yield MenuItem::section('Настройки');
        if ($this->settingsRepository->count([]) === 0) {
            yield MenuItem::linkToCrud('Настройки', 'fa fa-gear', Settings::class)
                ->setAction(Action::NEW);
        } else {
            yield MenuItem::linkToCrud('Настройки', 'fa fa-gear', Settings::class)
                ->setAction(Action::EDIT)->setEntityId($this->settingsRepository->findAll()[0]->getId());
        }
        yield MenuItem::linkToUrl('API', 'fa fa-link', '/api')->setLinkTarget('_blank')
            ->setPermission('ROLE_ADMIN');
    }
}
