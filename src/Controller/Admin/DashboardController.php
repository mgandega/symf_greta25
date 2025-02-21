<?php

namespace App\Controller\Admin;

use App\Entity\Categorie;
use App\Entity\Conference;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Controller\Admin\ConferenceCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ConferenceCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Symf Greta25');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        
        yield MenuItem::section('Conférence');
        yield MenuItem::subMenu('Conférence', 'fa fa-article')->setSubItems([
            MenuItem::linkToCrud('Ajouter une conférence', 'fa fa-tags', Conference::class)->setAction(Crud::PAGE_NEW),
            MenuItem::linkToCrud('Afficher les conférences', 'fa fa-file-text', Conference::class)
        ]);

        //  MenuItem::subMenu('Blog', 'fa fa-article')->setSubItems([
        //     MenuItem::linkToCrud('Categories', 'fa fa-tags', Category::class),
        //     MenuItem::linkToCrud('Posts', 'fa fa-file-text', BlogPost::class),
        //     MenuItem::linkToCrud('Comments', 'fa fa-comment', Comment::class),
        // ]),
    }
}
