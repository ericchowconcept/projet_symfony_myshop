<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\MembreRepository;
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

    #[Route('cart/order', name:'order')]
    public function order(CartService $cs, EntityManagerInterface $manager)
    {
        if($this->getUser())
        {
            $membre = $this->getUser();
        }else{
            $this->addFlash('success', 'Connectez vous ou Inscrivez vous pour reserver!');
            return $this->redirectToRoute('home'); 
        }

        $commande = new Commande;

        

        $cartWithData = $cs->cartWithData();
        foreach($cartWithData as $item )
        {
            if($item['produit']->getStock() < $item['quantity'])
            {
                $commande   -> setQuantite($item['produit']->getStock())
                            -> setMontant($item['quantity'] * $item['produit']->getPrix());

                if($item['produit']->getStock() <= 0)
                {
                    $this->addFlash('danger', 'Ils nous restent plus de stock');
                    return $this->redirectToRoute('cart');
                }
                
            }else{
                $commande   -> setQuantite($item['quantity'])
                            -> setMontant($item['quantity'] * $item['produit']->getPrix());
            }

            $commande   -> setMembre($membre)
                        -> setProduit($item['produit'])
                        -> setEtat('En cours de traitement')
                        -> setDateEnregistrement(new \DateTime);
            
            
            $manager->persist($commande);
            $item['produit']->setStock($item['produit']->getStock() - $item['quantity']);
            $manager->persist($item['produit']);
            
        }
        $manager->flush();
        $cs->removeSession();
        $this->addFlash('success', 'Votre commande a été bien enregistré');
        return $this->redirectToRoute('home');
    }

    #[Route('/monCompte', name:'user_profile')]
    public function profile(CommandeRepository $reco)
    {
        $commandes = $reco->findBy(['membre' => $this->getUser('id')]);

            return $this->render('app/moncompte.html.twig',[
                'commandes' => $commandes,
                
            ]);
    }


}
