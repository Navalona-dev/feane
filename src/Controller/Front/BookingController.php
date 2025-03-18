<?php

namespace App\Controller\Front;

use App\Entity\Table;
use App\Entity\Booking;
use App\Service\JWTService;
use App\Form\BookingFormType;
use App\Service\SendMailService;
use App\Repository\AdminRepository;
use App\Repository\TableRepository;
use App\Service\HeaderDataProvider;
use App\Repository\BookingRepository;
use App\Repository\HomePageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private SendMailService $mail,
        private EntityManagerInterface $em,
        private BookingRepository $bookingRepo,
        private TableRepository $tableRepo,
        private PaginatorInterface $paginator,
        private AdminRepository $adminRepo,
        private JWTService $jwt,

    )
    {

    }

    #[Route('/feane/booking', name: 'app_booking_index')]
    public function index(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $stripePublicKey = $_ENV['STRIPE_PUBLIC_KEY'];

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'stripe_public_key' => $stripePublicKey,

        ]);

        return $this->render('front/booking/index.html.twig', $data);
    }

    #[Route('/feane/reservation-liste', name: 'app_booking_list')]
    #[IsGranted("ROLE_USER")]
    public function list(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $stripePublicKey = $_ENV['STRIPE_PUBLIC_KEY'];

        $user = $this->getUser();

        $bookings = $this->bookingRepo->findByBooker($user, ['createdAt' => 'DESC']);

        $pagination = $this->paginator->paginate(
            $bookings,
            $request->query->getInt('page', 1),
            3
        );

        $pagination->setTemplate('front/partials/pagination.html.twig');

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'bookings' => $pagination,
            'stripe_public_key' => $stripePublicKey,

        ]);

        return $this->render('front/booking/liste.html.twig', $data);
    }

    #[Route('/feane/reservation/recapitulatif/{id}', name: 'app_booking_recap')]
    #[IsGranted("ROLE_USER")]
    public function recapitulatif(Request $request, $id): Response
    {
        $booking = $this->bookingRepo->findOneById($id);
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $stripePublicKey = $_ENV['STRIPE_PUBLIC_KEY'];

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'booking' => $booking,
            'stripe_public_key' => $stripePublicKey,

        ]);

        return $this->render('front/booking/recapitulatif.html.twig', $data);
    }

    #[Route('/feane/reservation/add/{id}', name: 'app_booking_add')]
    #[IsGranted("ROLE_USER")]
    public function add(Request $request, Table $table): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $reservations = $this->bookingRepo->findAll();

        $booking = new Booking();

        $form = $this->createForm(BookingFormType::class, $booking);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $date = new \DateTime();
            $dateBooking = $form->get('dateBooking')->getData();
            $bookingHour = $form->get('bookingHour')->getData();
            $message = $form->get('message')->getData();
            $reference = $date->format('dmY').'-'.uniqId();

            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'booking_id' => $booking->getId()
            ];

            $jwtSecretKeyPath = $_ENV['JWT_SECRET_KEY'];
            $token = $this->jwt->generate($header, $payload, $jwtSecretKeyPath);

            $booking->setBooker($user)
                    ->setCreatedAt($date)
                    ->setUpdatedAt($date)
                    ->setPrice($table->getCoutReservation())
                    ->setTableRestaurant($table)
                    ->setDateBooking(\DateTime::createFromFormat('d/m/Y H:i', $dateBooking->format('d/m/Y') . ' ' . $bookingHour->format('H:i')))
                    ->setMessage($message)
                    ->setIsPaid(0)
                    ->setResetToken($token)
                    ->setReference($reference)
                    ->setIsConfirmed(false);


            if(!$booking->inReservedDate($dateBooking, $bookingHour)) {
                $this->addFlash(
                    'danger',
                    'Les heures que vous avez choisi ne peuvent pas être réservées : elles sont déjà prises!!'
                );
            } else {

                $this->em->persist($booking);
                $this->em->flush();

                $this->addFlash(
                    'success',
                    $message
                );
    
                return $this->redirectToRoute('app_booking_recap', [
                    'id' => $booking->getId(),
                ]);
            }

           
        }

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'form' => $form->createView(),
            'table' => $table,
        ]);

        return $this->render('front/booking/add.html.twig', $data);
    }
    
}
