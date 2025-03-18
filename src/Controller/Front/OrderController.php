<?php

namespace App\Controller\Front;

use App\Entity\Order;
use App\Form\OrderFormType;
use App\Service\JWTService;
use App\Entity\OrderDetails;
use App\Repository\OrderRepository;
use App\Service\HeaderDataProvider;
use App\Repository\ProduitRepository;
use App\Repository\HomePageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private EntityManagerInterface $em,
        private ProduitRepository $produitRepo,
        private PaginatorInterface $paginator,
        private OrderRepository $orderRepo,
        private JWTService $jwt,


    )
    {

    }

    #[Route('/feane/liste-commande', name: 'app_order_list')]
    #[IsGranted("ROLE_USER")]
    public function myOrderList(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $user = $this->getUser();

        $orders = $this->orderRepo->findByUser($user, ['createdAt' => 'DESC']);

        $pagination = $this->paginator->paginate(
            $orders,
            $request->query->getInt('page', 1),
            3
        );

        $pagination->setTemplate('front/partials/pagination.html.twig');

        $stripePublicKey = $_ENV['STRIPE_PUBLIC_KEY'];

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'orders' => $pagination,
            'stripe_public_key' => $stripePublicKey,

        ]);

        return $this->render('front/order/liste.html.twig', $data);
    }

    #[Route('/feane/commande', name: 'app_order')]
    #[IsGranted("ROLE_USER")]
    public function order(SessionInterface $session, Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        if(!$this->getUser()->getAdresses()->getValues()) {
            return $this->redirectToRoute('app_adresse_new');
        }

        $cart = $session->get("cart", []);

        if(!$cart) {
            return $this->redirectToRoute('app_cart');
        }

        $dataCart = [];

        foreach($cart as $id => $quantity) {
            $produit = $this->produitRepo->find($id);

            $dataCart[] = [
                'produit' => $produit,
                'quantity' => $quantity
            ];

        }

        $form = $this->createForm(OrderFormType::class, null, [
            'user' => $this->getUser(),
        ]);

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'form' => $form->createView(),
            'dataCart' => $dataCart,
        ]);

        return $this->render('front/order/order.html.twig', $data);
    }

    #[Route('/feane/recapitulatif-commande', name: 'app_order_recap', methods: ['POST'])]
    #[IsGranted("ROLE_USER")]
    public function add(SessionInterface $session, Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $cart = $session->get("cart", []);

        if(!$cart) {
            return $this->redirectToRoute('app_cart');
        }

        $dataCart = [];

        foreach($cart as $id => $quantity) {
            $produit = $this->produitRepo->find($id);

            $dataCart[] = [
                'produit' => $produit,
                'quantity' => $quantity
            ];

        }

        $stripePublicKey = $_ENV['STRIPE_PUBLIC_KEY'];

        $form = $this->createForm(OrderFormType::class, null, [
            'user' => $this->getUser(),
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

           $date = new \DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('adresses')->getData();

            $delivery_content = $delivery->getAdress(). '<br/>' .$delivery->getCity(). '<br/>' .$delivery->getPostal(). '<br/>' .$delivery->getPhone();
            if($delivery->getCompany()) {
                $delivery_content .= '<br/>' .$delivery->getCompany();
            }

            $order = new Order();

            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'order_id' => $order->getId()
            ];

            $jwtSecretKeyPath = $_ENV['JWT_SECRET_KEY'];
            $token = $this->jwt->generate($header, $payload, $jwtSecretKeyPath);

            $reference = $date->format('dmY').'-'.uniqId();
            $order->setReference($reference)
                  ->setCreatedAt($date)
                  ->setCarrierName($carriers->getName())
                  ->setCarrierPrice($carriers->getPrice())
                  ->setDelivery($delivery_content)
                  ->setIsPaid(0)
                  ->setUser($user)
                  ->setIsConfirmed(false)
                  ->setResetToken($token)
                  ->setIsDelivery(false)
                  ;

            $this->em->persist($order);

            foreach($dataCart as $product) {
                $orderDetail = new OrderDetails();
                $orderDetail->setMyOrder($order)
                            ->setProduct($product['produit']->getTitle())
                            ->setQuantity($product['quantity'])
                            ->setPrice($product['produit']->getPrice())
                            ->setTotal($product['produit']->getPrice() * $product['quantity'])
                            ;

                $nombreProduit_restant = $product['produit']->getNumber() - $product['quantity'];

                $produit->setNumber($nombreProduit_restant);

                if($produit->getNumber() == 0) {
                    $produit->setIsOutOffStock(1);
                }

                $this->em->persist($orderDetail);
                $this->em->persist($produit);
                
            }

            $this->em->flush();

            $data = array_merge($headerData, [
                'homePages' => $homePages,
                'dataCart' => $dataCart,
                'carrier' => $carriers,
                'adresse' => $delivery_content,
                'reference' => $order->getReference(),
                'stripe_public_key' => $stripePublicKey,

            ]);
    
            return $this->render('front/order/add.html.twig', $data);
        }

        return $this->redirectToRoute('app_cart');
       
    }

    
    
}
