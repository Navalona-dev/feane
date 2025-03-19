<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Service\JWTService;
use App\Service\SendMailService;
use App\Form\RegisterAdminFormType;
use App\Form\ResetPasswordFormType;
use App\Repository\AdminRepository;
use App\Service\HeaderDataProvider;
use App\Repository\HomePageRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class AccountController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private JWTService $jwt,
        private SendMailService $mail,
        private AdminRepository $adminRepo,
        private UserPasswordHasherInterface $encoder,
        private EntityManagerInterface $em,
        private HeaderDataProvider $headerDataProvider,


    )
    {

    }
    
    #[Route('/register', name: 'app_register')]
    #[IsGranted("ROLE_ADMIN")]
    public function register(Request $request): Response
    {
        $homePages = $this->homePage->findAll();

        $headerData = $this->headerDataProvider->getHeaderData();

        $admin = new Admin();

        $form = $this->createForm(RegisterAdminFormType::class, $admin);
        $form->handleRequest($request);
        $message = 'Votre compte a bien été créé! Vous pouvez maintenant vous connecter!' ;

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->encoder->hashPassword($admin, $admin->getPassword());

            $roles = ["ROLE_ADMIN"];
            $admin->setRoles($roles);

            $admin->setPassword($password);

            $this->em->persist($admin);
            $this->em->flush();

            // On génère le JWT de l'utilisateur
            // On crée le Header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // On crée le Payload
            $payload = [
                'admin_id' => $admin->getId()
            ];

            // On génère le token
            //$jwtSecretKeyPath = $this->getParameter('JWT_SECRET_KEY');
            $jwtSecretKeyPath = $_ENV['JWT_SECRET_KEY'];
            $token = $this->jwt->generate($header, $payload, $jwtSecretKeyPath);

            $admin->setResetToken($token); // Ajoutez cette ligne pour stocker le token
            $this->em->persist($admin);
            $this->em->flush();

            $title = 'Activation de votre compte sur le site feane';

            // On envoie un mail
            $this->mail->send(
                'lnomenjanahary68@gmail.com',
                $admin->getEmail(),
                $title,
                'register_admin',
                compact('admin', 'token')
            );

            $this->addFlash(
                'success',
                $message
            );

            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        } 

        $data = array_merge($headerData, [
            'form' => $form->createview(),
            'homePages' => $homePages,
        ]);

        return $this->render('admin/account/register.html.twig', $data);
        
    }

    #[Route('/verify/{token}', name: 'verify_admin')]
    public function verifyUser($token, Request $request): Response
    {
        $messageSucces = 'Votre compte est activé' ;
        $messageError = 'Le jeton est invalide ou a expiré';
        $jwtSecretKeyPath = $_ENV['JWT_SECRET_KEY'];

        if($this->jwt->isValid($token) && !$this->jwt->isExpired($token) && $this->jwt->check($token, $jwtSecretKeyPath)){
            $payload = $this->jwt->getPayload($token);

            $admin = $this->adminRepo->find($payload['admin_id']);

            if($admin && !$admin->getIsVerified()){
                $admin->setIsVerified(true);
                $this->em->flush($admin);
                $this->addFlash(
                    'success', 
                    $messageSucces);
                return $this->redirectToRoute('app_admin');
            }
        }
        $this->addFlash(
            'danger',
            $messageError);
        return $this->redirectToRoute('app_login');
    }


    #[Route('/mot-de-passe-oublié', name:'forgotten_password_admin')]
    public function forgottenPassword(TokenGeneratorInterface $tokenGenerator, Request $request)
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $admin = $this->adminRepo->findOneByEmail($form->get('email')->getData());

            if($admin){
                $token = $tokenGenerator->generateToken();
                $admin->setResetToken($token);
                $this->em->persist($admin);
                $this->em->flush();
            
                $url = $this->generateUrl('reset_pass_admin', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                
                $context = compact('url', 'admin');

                $this->mail->send(
                    'lnomenjanahary68@gmail.com',
                    $admin->getEmail(),
                    'Réinitialisation de mot de passe',
                    'password_reset_admin',
                    $context
                );

                $this->addFlash('success', 'Email envoyé avec succès, veuillez consulter votre boite de reception mail');
                return $this->redirectToRoute('app_login');
            }
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
        }

        $data = array_merge($headerData, [
            'form' => $form->createview(),
            'homePages' => $homePages,
        ]);
                
        return $this->render('admin/account/reset_password_request.html.twig', $data);
    }

    #[Route('/mot-de-passe-oublié/{token}', name:'reset_pass_admin')]
    public function resetPass(string $token, Request $request,): Response
    {
        $homePages = $this->homePage->findAll();

        $headerData = $this->headerDataProvider->getHeaderData();

        $admin = $this->adminRepo->findOneByResetToken($token);
        
        if($admin){
            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $admin->setResetToken('');
                $admin->setPassword(
                    $this->encoder->hashPassword(
                        $admin,
                        $form->get('password')->getData()
                    )
                );
                $this->em->persist($admin);
                $this->em->flush();

                $this->addFlash('success', 'Mot de passe changé avec succès, vous pouvez vous connecter maintenant');
                return $this->redirectToRoute('app_login');
            }

            $data = array_merge($headerData, [
                'form' => $form->createview(),
                'homePages' => $homePages,
            ]);

            return $this->render('admin/account/reset_password.html.twig', $data);
        }
        
        $this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('app_login');
    }
}
