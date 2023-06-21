<?php

namespace App\Controller\Admin;

use Symfony\Component\HttpFoundation\Response;
use App\Controller\Admin\ProduitCrudController;
use App\Entity\Commande;
use App\Entity\Membre;
use App\Entity\Produit;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    public function __construct(private AdminUrlGenerator $adminUrlGenerator){

    }
    
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        // $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        // return $this->redirect($adminUrlGenerator->setController(OneOfYourCrudController::class)->generateUrl());

       
        $url = $this->adminUrlGenerator->setController(ProduitCrudController::class)->generateUrl();
        return $this->redirect($url);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('MYSHOP');
    }

    public function configureMenuItems(): iterable
    {
        // yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
        return[
        MenuItem::linkToDashboard("BACKOFFICE", 'fa fa-home'),

        MenuItem::section('Membre'),
        MenuItem::linkToCrud('Utilisateurs', 'fas fa-user', Membre::class),
        MenuItem::section('Produits'),
        MenuItem::linkToCrud('Produits', 'fas fa-shirt', Produit::class),
        MenuItem::section('Commandes'),
        MenuItem::linkToCrud('Commande', 'fas fa-shopping-cart', Commande::class),

             

        ];
    }
}
