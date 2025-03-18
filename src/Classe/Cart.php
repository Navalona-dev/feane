<?php

namespace App\Classe;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart {

    private $session;
    private $entityManager;

    public function __construct(SessionInterface $session, EntityManagerInterface $entityManager)
    {
        $this->session = $session;
        $this->entityManager = $entityManager;
    }

    public function addCart($id) 
    {
        $cart = $this->session->get('cart', []);

        if(!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $this->session->set('cart', $cart);
    }

    public function getCart() {
        return $this->session->get('cart', []);
    }

    public function removeCart() {
        return $this->session->remove('cart');
    }

    public function delete($id) {
        $cart = $this->session->get('cart', []);

        unset($cart[$id]);

        return $cart;
    }

    public function getFull() {
        $cartComplete = [];

        if($this->getCart()) {
            foreach ($this->getCart() as $id => $quantity) {

                $produit_object = $this->entityManager->getRepository(Produit::class)->findOneById($id);

                if (!$produit_object) {
                    $this->delete($id);
                    continue;
                }

                $cartComplete[] = [
                    'produit' => $produit_object,
                    'quantity' => $quantity
                ];
            }
        }

        return $cartComplete;
    }

}