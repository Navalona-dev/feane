<?php

namespace App\Controller\Front;

use App\Service\HeaderDataProvider;
use App\Repository\ProduitRepository;
use App\Repository\HomePageRepository;
use App\Repository\TestimonialRepository;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\MenuRestaurantRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private ProduitRepository $produitRepo,
        private MenuRestaurantRepository $menuRestoRepo,
        private TestimonialRepository $testimonialRepo,
        private PaginatorInterface $paginator,

    )
    {

    }
    
    #[Route('/', name: 'app_home')]
    public function index(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();
        $produits = $this->produitRepo->findBy(['isActive' => true, 'isOutOffStock' => false]);
        $menuResto = $this->menuRestoRepo->findBy(['isActive' => true]);
        $testimonials = $this->testimonialRepo->findBy(['isActive' => true]);

        $produitsReduction = $this->produitRepo->findBy(['isActive' => true, 'isReduction' => true, 'isOutOffStock' => false]);

        $paginationTestimonial = $this->paginator->paginate(
            $testimonials,
            $request->query->getInt('page', 1),
            3
        );

        $paginationProduct = $this->paginator->paginate(
            $produits,
            $request->query->getInt('page', 1),
            9
        );

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'produits' => $paginationProduct,
            'menuResto' => $menuResto,
            'products' => $produitsReduction,
            'testimonials' => $paginationTestimonial
        ]);

        return $this->render('front/home/index.html.twig', $data);
    }
}
