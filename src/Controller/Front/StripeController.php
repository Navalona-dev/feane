<?php

namespace App\Controller\Front;

use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Service\SendMailService;
use App\Repository\AdminRepository;
use App\Repository\OrderRepository;
use App\Repository\TableRepository;
use App\Repository\BookingRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    public function __construct(
        private ProduitRepository $produitRepo,
        private OrderRepository $orderRepo,
        private EntityManagerInterface $em,
        private BookingRepository $bookingRepo,
        private TableRepository $tableRepo,
    )
    {

    }

    #[Route('/feane/commande/create-session/{reference}', name: 'app_stripe_create_session_order')]
    #[IsGranted("ROLE_USER")]
    public function order($reference): Response
    {
        $product_for_stripe = [];

        $local_hosts = array('localhost', '127.0.0.1');

        $protocol = (preg_match('/' . implode('|', $local_hosts) . '/', $_SERVER['HTTP_HOST']) === 1)
        ? 'http://' 
        : 'https://';

        $YOUR_DOMAIN = $protocol . $_SERVER['HTTP_HOST'];

        //$stripePublicKey = $_ENV['STRIPE_PUBLIC_KEY'];
        $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'];

        $order = $this->orderRepo->findOneByReference($reference);

        $orderDetails = $order->getOrderDetails()->getValues();

        $user = $this->getUser();

        if(!$order) {
            new JsonResponse(['error' => 'order']);
        }

        foreach($orderDetails as $product) {
            $product_object = $this->produitRepo->findOneByTitle($product->getProduct());
            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN."/uploads/produits/".$product_object->getImage()],
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];
        }

        $product_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$YOUR_DOMAIN],
                ],
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey($stripeSecretKey);
            
        $checkout_session = Session::create([
            'customer_email' => $user->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[
                $product_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/feane/commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/feane/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);
        $this->em->flush();

        $response = new JsonResponse(['id' => $checkout_session->id]);

        return $response;

        //4242 4242 4242 4242

    }

    #[Route('/feane/reservation/create-session/{reference}', name: 'app_stripe_create_session_booking')]
    #[IsGranted("ROLE_USER")]
    public function booking($reference): Response
    {
        $product_for_stripe = [];

        
        $local_hosts = array('localhost', '127.0.0.1');

        $protocol = (preg_match('/' . implode('|', $local_hosts) . '/', $_SERVER['HTTP_HOST']) === 1)
        ? 'http://' 
        : 'https://';

        $YOUR_DOMAIN = $protocol . $_SERVER['HTTP_HOST'];

        //$stripePublicKey = $_ENV['STRIPE_PUBLIC_KEY'];
        $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'];

        $booking = $this->bookingRepo->findOneByReference($reference);

        $table = $booking->getTableRestaurant();

        $user = $this->getUser();

        if(!$booking) {
            new JsonResponse(['error' => 'booking']);
        }

        $product_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $table->getCoutReservation(),
                'product_data' => [
                    'name' => $table->getType(),
                    'images' => [$YOUR_DOMAIN."/uploads/tables/".$table->getImage()],
                ],
            ],
            'quantity' => 1,
        ];

        Stripe::setApiKey($stripeSecretKey);
            
        $checkout_session = Session::create([
            'customer_email' => $user->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [[
                $product_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/feane/reservation/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/feane/reservation/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $booking->setStripeSessionId($checkout_session->id);
        $this->em->flush();

        $response = new JsonResponse(['id' => $checkout_session->id]);

        return $response;

        //4242 4242 4242 4242

    }
}

