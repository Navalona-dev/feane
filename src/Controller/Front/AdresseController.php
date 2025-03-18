<?php

namespace App\Controller\Front;

use App\Classe\Cart;
use App\Entity\Adresse;
use App\Form\AdresseFormType;
use App\Service\HeaderDataProvider;
use App\Repository\AdresseRepository;
use App\Repository\HomePageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/feane/adresse')]
class AdresseController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private EntityManagerInterface $em,
        private AdresseRepository $adresseRepo,
    )
    {

    }

    /*#[Route('/feane/adresse', name: 'app_adresse')]
    #[Security("is_authenticated() and is_granted('ROLE_USER')")]
    public function index(): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $user = $this->getUser();

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'user' => $user
        ]);

        return $this->render('front/adresse/index.html.twig', $data);
    }*/

    #[Route('/new', name: 'app_adresse_new', methods: ['GET', 'POST'])]
    #[Security("is_authenticated() and is_granted('ROLE_USER')")]
    public function new(SessionInterface $session, Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $adresse = new Adresse();
        $form = $this->createForm(AdresseFormType::class, $adresse);
        $form->handleRequest($request);

        $user = $this->getUser();

        $message = 'Votre adresse a bien été enregistrée, Merci pour votre confiance' ;

        if ($form->isSubmitted() && $form->isValid()) {
            $adresse->setUser($user);
            $this->em->persist($adresse);
            $this->em->flush();

            $this->addFlash(
                'success',
                $message
            );

            $cart = $session->get("cart", []);

            if($cart) {
                return $this->redirectToRoute('app_order', [], Response::HTTP_SEE_OTHER);
            } else {
                return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
            }

        }

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'form' => $form,
        ]);

        return $this->render('front/adresse/new.html.twig', $data);
    }

    #[Route('/{id}/edit', name: 'app_adresse_edit', methods: ['GET', 'POST'])]
    #[Security("is_authenticated() and is_granted('ROLE_USER')")]
    public function edit(Request $request, $id): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $adresse = $this->adresseRepo->findOneById($id);

        if(!$adresse || $adresse->getUser() != $this->getUser()) {
            return $this->redirectToRoute('app_account');
        }
        
        $form = $this->createForm(AdresseFormType::class, $adresse);
        $form->handleRequest($request);

        $message = 'Votre adresse a bien été modifiée, Merci pour votre confiance' ;

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->flush();

            $this->addFlash(
                'success',
                $message
            );

            return $this->redirectToRoute('app_account');
        }

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'form' => $form,
            'id' => $id
        ]);

        return $this->render('front/adresse/edit.html.twig', $data);
    }

    #[Route('/{id}/delete', name: 'app_adresse_delete', methods: ['POST'])]
    public function delete(Request $request, Adresse $adresse): Response
    {
        if($adresse && $adresse->getUser() == $this->getUser()) {
            $this->em->remove($adresse);
            $this->em->flush();
        }

        return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
    }
}
