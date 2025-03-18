<?php

namespace App\Controller\Front;

use App\Service\JWTService;
use App\Service\SendMailService;
use App\Repository\AdminRepository;
use App\Service\HeaderDataProvider;
use App\Repository\BookingRepository;
use App\Repository\HomePageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookingSuccessController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private BookingRepository $bookingRepo,
        private AdminRepository $adminRepo,
        private EntityManagerInterface $em,
        private SendMailService $mail,
        private JWTService $jwt,

    )
    {

    }
    
    #[Route('/feane/reservation/merci/{stripeSessionId}', name: 'app_booking_success')]
    public function index($stripeSessionId): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $admin = $this->adminRepo->findOneBy(['isSendMail' => true]);

        $booking = $this->bookingRepo->findOneByStripeSessionId($stripeSessionId);

        $user = $this->getUser();

        $token = $booking->getResetToken();

        if(!$booking || $booking->getBooker() != $user) {
            return $this->redirectToRoute('app_home');
        }

        if(!$booking->IsIsPaid()) {

            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'booking_id' => $booking->getId()
            ];

            $jwtSecretKeyPath = $_ENV['JWT_SECRET_KEY'];
            $token = $this->jwt->generate($header, $payload, $jwtSecretKeyPath);

            $booking->setResetToken($token);

            $booking->setIsPaid(1);
            $this->em->flush();

            if ($admin && $admin->getIsVerified()) {
                $url = $this->generateUrl('app_admin', [], UrlGeneratorInterface::ABSOLUTE_URL);
                
                $context = compact('url', 'admin', 'user', 'booking');
    
                $title = 'Nouvelle réservation d\'une table programmée';
    
                $this->mail->send(
                    'lnomenjanahary68@gmail.com',
                    $admin->getEmail(),
                    $title,
                    'send_email_new_booking_table',
                    $context
                );
            }
            if ($user && $user->getIsVerified()) {

                $url = $this->generateUrl('app_booking_list', [], UrlGeneratorInterface::ABSOLUTE_URL);
                
                $context = compact('url', 'user', 'booking', 'token');
   
                $title = 'Nouveau commande réussi';
   
                $this->mail->send(
                    'lnomenjanahary68@gmail.com',
                    $user->getEmail(),
                    $title,
                    'send_email_new_booking_user',
                    $context
                );
   
            }
        }

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'booking' => $booking
        ]);

        return $this->render('front/booking/success.html.twig', $data);
    }

    #[Route('/feane/confirmation-reservation/{token}', name: 'confirmation_reservation')]
    public function bookingConfirm($token, Request $request): Response
    {
        $messageSucces = 'Votre réservation a bien été confirmé' ;
        $messageError = 'Le jeton est invalide ou a expiré';

        $jwtSecretKeyPath = $_ENV['JWT_SECRET_KEY'];

        if($this->jwt->isValid($token) && !$this->jwt->isExpired($token) && $this->jwt->check($token, $jwtSecretKeyPath)){
            $payload = $this->jwt->getPayload($token);

            $booking = $this->bookingRepo->find($payload['booking_id']);

            if($booking && !$booking->IsIsConfirmed()){
                $booking->setIsConfirmed(true);
                $this->em->flush($booking);
                $this->addFlash(
                    'success', 
                    $messageSucces);
                return $this->redirectToRoute('app_booking_list');
            }
        }
        $this->addFlash(
            'danger',
            $messageError);
        return $this->redirectToRoute('app_booking_list');
    }
}
