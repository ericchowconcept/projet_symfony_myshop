<?php 

namespace App\Service;

use App\Repository\ProduitRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService 
{
    private $repo;

    private $rs;

    public function __construct(ProduitRepository $repo, RequestStack $rs)
    {
        $this->rs = $rs;
        $this->repo = $repo;
    }

    public function add($id)
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart',[]);

        $qt = $session->get('qt', 0);


        if(!empty($cart[$id]))
        {
            $cart[$id]++;
            $qt++;
        }else{
            $cart[$id]=1;
            $qt++;

        }

        $session->set('qt', $qt); 
        $session->set('cart', $cart);
    }

    public function minus($id)
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart',[]);

        $qt = $session->get('qt', 0);


        if(!empty($cart[$id] && $cart[$id] > 1))
        {
            $cart[$id]--;
            $qt--;
        }else{

            $qt-= $cart[$id];
            unset($cart[$id]);

        }

        $session->set('qt', $qt); 
        $session->set('cart', $cart);
    }

    public function remove($id)
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart',[]);

        $qt = $session->get('qt', 0);

        if(!empty($cart[$id]))
        {
            $qt -= $cart[$id];
            unset($cart[$id]);
        }

        if($qt<0)
        {
            $qt =0;
        }

        $session->set('qt', $qt);
        $session->set('cart', $cart);
    }

    public function cartWithData()
    {
        $session = $this->rs->getSession();
        $cart = $session->get('cart',[]);

        $cartWithData = [];

        foreach($cart as $id => $quantity)
        {
            $produits = $this->repo->find($id);
            $cartWithData[] = [
                'produit' => $produits,
                'quantity'=> $quantity
            ];
        }

        return $cartWithData;
    }

    public function total()
    {
        $cartWithData = $this->cartWithData();
        $total = 0;

        foreach($cartWithData as $item)
        {
            $total += $item['produit']->getPrix() * $item['quantity'];
        }

        return $total;
    }

    public function removeSession()
    {
        $session = $this->rs->getSession();

       $session->remove('qt');
       $session->remove('cart');

        
    }
}