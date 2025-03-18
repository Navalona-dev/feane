<?php

namespace App\Controller\Front;

use BaconQrCode\Writer;
use App\Service\JWTService;
use App\Service\SendMailService;
use App\Repository\AdminRepository;
use App\Repository\OrderRepository;
use App\Service\HeaderDataProvider;
use App\Repository\HomePageRepository;
use BaconQrCode\Renderer\ImageRenderer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use Symfony\Component\Routing\Annotation\Route;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderSuccessController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private OrderRepository $orderRepo,
        private AdminRepository $adminRepo,
        private EntityManagerInterface $em,
        private SendMailService $mail,
        private JWTService $jwt,

    )
    {

    }
    
    #[Route('/feane/commande/merci/{stripeSessionId}', name: 'app_order_validate')]
    public function index($stripeSessionId, SessionInterface $session): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $admin = $this->adminRepo->findOneBy(['isSendMail' => true]);

        $order = $this->orderRepo->findOneByStripeSessionId($stripeSessionId);

        $user = $this->getUser();

        $token = $order->getResetToken();

        $dateOrder = $order->getCreatedAt();

        if(!$order || $order->getUser() != $user) {
            return $this->redirectToRoute('app_home');
        }

        if(!$order->IsIsPaid()) {

            $session->remove("cart");

            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'order_id' => $order->getId()
            ];

            $jwtSecretKeyPath = $_ENV['JWT_SECRET_KEY'];
            $token = $this->jwt->generate($header, $payload, $jwtSecretKeyPath);

            $order->setResetToken($token);
            $order->setIsPaid(1);

            //QRCode

            $qrCodeText = 'Numéro de commande : ' . $order->getReference();
            $renderer = new ImageRenderer(
                new RendererStyle(400), 
                new SvgImageBackEnd(400, 400) 
            );
            
            
            $writer = new Writer($renderer);
            $qrCodeImage = $writer->writeString($qrCodeText);
            
            $order->setQrCode($qrCodeImage); 

            $this->em->flush();

            if ($admin && $admin->getIsVerified()) {
                $url = $this->generateUrl('app_admin', [], UrlGeneratorInterface::ABSOLUTE_URL);
                
                $context = compact('url', 'admin', 'dateOrder', 'user', 'order');
    
                $title = 'Nouvelle commande réussi';
    
                $this->mail->send(
                    'lnomenjanahary68@gmail.com',
                    $admin->getEmail(),
                    $title,
                    'send_email_new_order',
                    $context
                );
            }
            if ($user && $user->getIsVerified()) {

                $url = $this->generateUrl('app_order_list', [], UrlGeneratorInterface::ABSOLUTE_URL);
                
                $context = compact('url', 'user', 'dateOrder', 'order', 'token');
   
                $title = 'Nouvelle commande réussi';
   
                $this->mail->send(
                    'lnomenjanahary68@gmail.com',
                    $user->getEmail(),
                    $title,
                    'send_email_new_order_user',
                    $context
                );
   
            }
        }

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'order' => $order
        ]);

        return $this->render('front/order/success.html.twig', $data);
    }

    #[Route('/feane/confirmation-commande/{token}', name: 'confirmation_commande')]
    public function orderConfirm($token, Request $request): Response
    {
        $messageSucces = 'Votre commande a bien été confirmé' ;
        $messageError = 'Le jeton est invalide ou a expiré';

        $jwtSecretKeyPath = $_ENV['JWT_SECRET_KEY'];

        if($this->jwt->isValid($token) && !$this->jwt->isExpired($token) && $this->jwt->check($token, $jwtSecretKeyPath)){
            $payload = $this->jwt->getPayload($token);

            $order = $this->orderRepo->find($payload['order_id']);

            if($order && !$order->IsIsConfirmed()){
                $order->setIsConfirmed(true);
                $this->em->flush($order);
                $this->addFlash(
                    'success', 
                    $messageSucces);
                return $this->redirectToRoute('app_order_list');
            }
        }
        $this->addFlash(
            'danger',
            $messageError);
        return $this->redirectToRoute('app_order_list');
    }
}
