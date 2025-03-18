<?php

namespace App\Controller\Front;

use App\Service\HeaderDataProvider;
use App\Repository\ServiceRepository;
use App\Repository\HomePageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AboutController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private PaginatorInterface $paginator,
        private ServiceRepository $serviceRepo,
    )
    {

    }
    
    #[Route('/feane/about', name: 'app_about')]
    public function index(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $services = $this->serviceRepo->findBy(['isActive' => true]);

        $pagination = $this->paginator->paginate(
            $services,
            $request->query->getInt('page', 1),
            6
        );

        $pagination->setTemplate('front/partials/pagination.html.twig');

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'services' => $pagination
        ]);

        return $this->render('front/about/index.html.twig', $data);
    }
}
