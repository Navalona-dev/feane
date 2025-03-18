<?php

namespace App\Controller\Front;

use App\Entity\Produit;
use App\Service\HeaderDataProvider;
use App\Repository\ProduitRepository;
use App\Repository\HomePageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private EntityManagerInterface $em,
        private ProduitRepository $produitRepo,
    )
    {

    }

    #[Route('/feane/cart', name: 'app_cart')]
    #[IsGranted("ROLE_USER")]
    public function index(SessionInterface $session): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $cart = $session->get("cart", []);

        // on "fabrique" les données

        $dataCart = [];

        foreach($cart as $id => $quantity) {
            $produit = $this->produitRepo->find($id);

            $dataCart[] = [
                'produit' => $produit,
                'quantity' => $quantity
            ];

        }

        $data = array_merge($headerData, [
            'homePages' => $homePages,
             //compact('dataCart')
             'dataCart' => $dataCart

        ]);

        return $this->render('front/cart/index.html.twig', $data);
    }

    #[Route('/feane/cart/add/{id}', name: 'app_cart_add')]
    public function add(Produit $produit, SessionInterface $session)
    {
        //on récupère le panier actuel

        $cart = $session->get("cart", []);

        $id = $produit->getId();

        $limit = $produit->getNumber();

        if((!empty($cart[$id]))) {

            if($cart[$id] < $limit) {

                $cart[$id]++;

            }
           
        } else {
            $cart[$id] = 1;
        }

        // on sauvegarde dans la session

        $session->set("cart", $cart);

        return $this->redirectToRoute('app_cart');

    }

    #[Route('/feane/cart/remove/{id}', name: 'app_cart_remove')]
    public function remove(Produit $produit, SessionInterface $session)
    {
        //on récupère le panier actuel

        $cart = $session->get("cart", []);

        $id = $produit->getId();


        if(!empty($cart[$id])) {

            if($cart[$id] > 1) {

            $cart[$id]--;

            } else {
                unset($cart[$id]);
            }
        } 

        // on sauvegarde dans la session

        $session->set("cart", $cart);

        return $this->redirectToRoute('app_cart');

    }

    #[Route('/feane/cart/delete/{id}', name: 'app_cart_delete')]
    public function delete(Produit $produit, SessionInterface $session)
    {
        //on récupère le panier actuel

        $cart = $session->get("cart", []);

        $id = $produit->getId();

        if(!empty($cart[$id])) {

                unset($cart[$id]);
        } 

        // on sauvegarde dans la session

        $session->set("cart", $cart);

        return $this->redirectToRoute('app_cart');

    }

    #[Route('/feane/cart/supprimer', name: 'app_cart_supprimer')]
    public function deleteAll(SessionInterface $session)
    {

        $session->remove("cart");

        return $this->redirectToRoute('app_cart');

    }
}
