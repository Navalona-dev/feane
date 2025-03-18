<?php

namespace App\Controller;

use App\Form\LoginFormType;
use App\Service\HeaderDataProvider;
use App\Repository\HomePageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserSecurityContollerController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,

    )
    {

    }

    #[Route(path: '/feane/connexion', name: 'app_login_front')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $form = $this->createForm(LoginFormType::class);
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $data = array_merge($headerData, [
            'last_username' => $lastUsername, 
            'error' => $error,
            'homePages' => $homePages,
            'form' => $form->createView()
        ]);

        return $this->render('front/security/login.html.twig', $data);
    }

    #[Route(path: '/feane/deconnexion', name: 'app_logout_front')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
