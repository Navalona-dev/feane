<?php

namespace App\Controller\Front;

use App\Repository\OrderRepository;
use App\Service\HeaderDataProvider;
use App\Repository\HomePageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderCancelController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private EntityManagerInterface $em,
        private OrderRepository $orderRepo,

    )
    {

    }

    #[Route('/feane/commande/erreur/{stripeSessionId}', name: 'app_order_failed')]
    public function index($stripeSessionId): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $order = $this->orderRepo->findOneByStripeSessionId($stripeSessionId);
        $user = $this->getUser();

        if(!$order || $order->getUser() != $user) {
            return $this->redirectToRoute('app_home');
        }

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'order' => $order
        ]);

        return $this->render('front/order/cancel.html.twig', $data);
    }
}
