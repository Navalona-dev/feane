<?php

namespace App\Controller\Front;

use App\Repository\TableRepository;
use App\Service\HeaderDataProvider;
use App\Repository\HomePageRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TableController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private PaginatorInterface $paginator,
        private TableRepository $tableRepo
    )
    {

    }
    
    #[Route('/feane/table', name: 'app_table')]
    public function index(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();
        $tables = $this->tableRepo->findBy(['isActive' => true]);

        $pagination = $this->paginator->paginate(
            $tables,
            $request->query->getInt('page', 1),
            6
        );

        $pagination->setTemplate('front/partials/pagination.html.twig');

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'tables' => $pagination,
        ]);


        return $this->render('front/table/index.html.twig', $data);
    }
}
