<?php

namespace App\Controller\Front;

use App\Service\HeaderDataProvider;
use App\Repository\ProduitRepository;
use App\Repository\HomePageRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\MenuRestaurantRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private ProduitRepository $produitRepo,
        private MenuRestaurantRepository $menuRestoRepo,
        private PaginatorInterface $paginator,
    )
    {

    }
    
    #[Route('/feane/produit', name: 'app_produit')]
    public function index(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();
        $produits = $this->produitRepo->findBy(['isActive' => true, 'isOutOffStock' => false]);
        $menuResto = $this->menuRestoRepo->findBy(['isActive' => true]);

        $pagination = $this->paginator->paginate(
            $produits,
            $request->query->getInt('page', 1),
            6
        );

        $pagination->setTemplate('front/partials/pagination.html.twig');

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'produits' => $pagination,
            'menuResto' => $menuResto
        ]);

        return $this->render('front/produit/index.html.twig', $data);
    }
}
