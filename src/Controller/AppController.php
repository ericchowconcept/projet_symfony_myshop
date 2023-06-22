<?php

namespace App\Controller;

use App\Service\CartService;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AppController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(ProduitRepository $repo): Response
    {
        $produits = $repo->findAll();
        return $this->render('app/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    
    
    #[Route('/cart/add/{id}', name:'cart_add')]
    public function add($id, CartService $cs)
    {
        
        $cs->add($id);
        return $this->redirectToRoute('home');
    }

    #[Route('cart/remove/{id}', name:'cart_remove')]
    public function remove($id, CartService $cs)
    {
        $cs->remove($id);
        return $this->redirectToRoute('cart');

    }

    #[Route('/cart', name:'cart')]
    public function show(CartService $cs)
    {
        
        $cartWithData = $cs->cartWithData();
        $total = $cs->total();

        return $this->render('app/cart.html.twig', [
            'items' => $cartWithData,
            'total' => $total
        ]);
    
    }
}
