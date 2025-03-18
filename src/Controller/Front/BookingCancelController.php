<?php

namespace App\Controller\Front;

use App\Service\HeaderDataProvider;
use App\Repository\BookingRepository;
use App\Repository\HomePageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingCancelController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private EntityManagerInterface $em,
        private BookingRepository $bookingRepo,

    )
    {

    }

    #[Route('/feane/reservation/erreur/{stripeSessionId}', name: 'app_booking_cancel')]
    public function index($stripeSessionId): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $booking = $this->bookingRepo->findOneByStripeSessionId($stripeSessionId);
        $user = $this->getUser();

        if(!$booking || $booking->getUser() != $user) {
            return $this->redirectToRoute('app_home');
        }

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'booking' => $booking
        ]);

        return $this->render('front/booking/cancel.html.twig', $data);
    }
}
