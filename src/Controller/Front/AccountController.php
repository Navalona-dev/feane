<?php

namespace App\Controller\Front;

use App\Entity\User;
use App\Service\JWTService;
use App\Entity\PasswordUpdate;
use App\Form\UpdateUserFormType;
use App\Service\SendMailService;
use App\Form\UpdateEmailFormType;
use App\Form\RegisterUserFormType;
use App\Repository\UserRepository;
use App\Form\ResetPasswordFormType;
use App\Form\UpdateProfileFormType;
use App\Service\HeaderDataProvider;
use App\Form\UpdatePasswordFormType;
use Symfony\Component\Form\FormError;
use App\Repository\HomePageRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AccountController extends AbstractController
{
    public function __construct(
        private HomePageRepository $homePage, 
        private HeaderDataProvider $headerDataProvider,
        private JWTService $jwt,
        private SendMailService $mail,
        private UserRepository $userRepo,
        private UserPasswordHasherInterface $encoder,
        private EntityManagerInterface $em,


    )
    {

    }

    #[Route('/feane/mon-profile', name: 'app_account')]
    #[Security("is_authenticated() and is_granted('ROLE_USER')")]
    public function index(): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $user = $this->getUser();

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'user' => $user,
        ]);

        return $this->render('front/account/mon_profile.html.twig', $data);
    }

    #[Route('/feane/inscription', name: 'app_inscription')]
    public function register(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $user = new User();

        $form = $this->createForm(RegisterUserFormType::class, $user);
        $form->handleRequest($request);
        $message = 'Votre compte a bien été créé! Vous pouvez maintenant vous connecter!' ;

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->encoder->hashPassword($user, $user->getPassword());

            $roles = ["ROLE_USER"];
            $user->setRoles($roles);

            $user->setPassword($password);

            foreach($user->getAdresses() as $adresse) {
                $adresse->setUser($user);
                $this->em->persist($adresse);
            }

            $this->em->persist($user);
            $this->em->flush();

            // On génère le JWT de l'utilisateur
            // On crée le Header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // On crée le Payload
            $payload = [
                'user_id' => $user->getId()
            ];

            // On génère le token
            //$jwtSecretKeyPath = $this->getParameter('JWT_SECRET_KEY');
            $jwtSecretKeyPath = $_ENV['JWT_SECRET_KEY'];
            $token = $this->jwt->generate($header, $payload, $jwtSecretKeyPath);

            $user->setResetToken($token); // Ajoutez cette ligne pour stocker le token
            $this->em->persist($user);
            $this->em->flush();

            $title = 'Activation de votre compte sur le site feane';

            // On envoie un mail
            $this->mail->send(
                'lnomenjanahary68@gmail.com',
                $user->getEmail(),
                $title,
                'register_user',
                compact('user', 'token')
            );

            $this->addFlash(
                'success',
                $message
            );

            return $this->redirectToRoute('app_login_front', [], Response::HTTP_SEE_OTHER);
        } 

        $data = array_merge($headerData, [
            'homePages' => $homePages,
            'form' => $form->createView()
        ]);

        return $this->render('front/security/register.html.twig', $data);
    }

    #[Route('/feane/verify-user/{token}', name: 'verify_user')]
    public function verifyUser($token, Request $request): Response
    {
        $messageSucces = 'Votre compte est activé' ;
        $messageError = 'Le jeton est invalide ou a expiré';
        //On vérifie si le token est valide, n'a pas expiré et n'a pas été modifié
        //$jwtSecretKeyPath = $this->getParameter('JWT_SECRET_KEY');
        $jwtSecretKeyPath = $_ENV['JWT_SECRET_KEY'];

        if($this->jwt->isValid($token) && !$this->jwt->isExpired($token) && $this->jwt->check($token, $jwtSecretKeyPath)){
            // On récupère le payload
            $payload = $this->jwt->getPayload($token);

            // On récupère le user du token
            $user = $this->userRepo->find($payload['user_id']);

            //On vérifie que l'utilisateur existe et n'a pas encore activé son compte
            if($user && !$user->getIsVerified()){
                $user->setIsVerified(true);
                $this->em->flush($user);
                $this->addFlash(
                    'success', 
                    $messageSucces);
                return $this->redirectToRoute('app_home');
            }
        }
        // Ici un problème se pose dans le token
        $this->addFlash(
            'danger',
            $messageError);
        return $this->redirectToRoute('app_login_front');
    }

    #[Route('/feane/resend-activation', name: 'reset_activation_user')]
    public function resendActivationLink(Request $request): Response
    {
        $user = $this->getUser();

        if ($user && !$user->getIsVerified()) {
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            $payload = [
                'user_id' => $user->getId()
            ];

            $jwtSecretKeyPath = $_ENV['JWT_SECRET_KEY'];
            $token = $this->jwt->generate($header, $payload, $jwtSecretKeyPath);

            $user->setResetToken($token); 
            $this->em->persist($user);
            $this->em->flush();

            $title = 'Activation de votre compte sur le site Feane';
            
            $this->mail->send(
                'lnomenjanahary68@gmail.com',
                $user->getEmail(),
                $title,
                'register_user',
                compact('user', 'token')
            );

            $messageSucces = 'Le lien d\'activation a été renvoyé avec succès. Veuillez consulter votre boîte de reception mail ou votre spam, Meri!!!';

            $this->addFlash(
                'success',
                $messageSucces
            );
        }

        return $this->redirectToRoute('app_home'); 
    }

    #[Route('/feane/update-mail', name: 'app_update_mail_user')]
    #[Security("is_authenticated() and is_granted('ROLE_USER')")]
    public function editMail(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $user = $this->getUser();

        $form = $this->createForm(UpdateEmailFormType::class, $user);

        $form->handleRequest($request);

        $profile = $user->getProfile();

        $message = 'Votre mail a bien été modifié! Merci pour votre confiance!';

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash(
                'success',
                $message
            );

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        } 

        $data = array_merge($headerData, [
            'form' => $form->createView(),
            'homePages' => $homePages
        ]);

        return $this->render('front/account/edit_mail.html.twig', $data);
        
    }

    #[Route('/feane/edit-information', name: 'app_edit_info_user')]
    #[Security("is_authenticated() and is_granted('ROLE_USER')")]
    public function editInfo(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $user = $this->getUser();

        $form = $this->createForm(UpdateUserFormType::class, $user);

        $form->handleRequest($request);

        $message = 'Votre information personnelle a bien été modifié! Merci pour votre confiance';

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash(
                'success',
                $message
            );

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        } 

        $data = array_merge($headerData, [
            'form' => $form->createView(),
            'homePages' => $homePages,
        ]);

        return $this->render('front/account/edit_info.html.twig', $data);
        
    }

    #[Route('/feane/edit-profile-photo', name: 'app_edit_profile')]
    #[Security("is_authenticated() and is_granted('ROLE_USER')")]
    public function editprofile(Request $request): Response
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $user = $this->getUser();

        $originalPhoto = $user->getProfile();

        $form = $this->createForm(UpdateProfileFormType::class, $user);
        $form->handleRequest($request);
        
        $message = 'Votre photo de profile a bien été modifié! Merci pour votre confiance!';

        if ($form->isSubmitted() && $form->isValid()) {

            $form_profile = $form->get('imageFile')->getData();

            if($originalPhoto == null) {
                $user->setProfile($form_profile);
                $this->em->persist($user);
                $this->em->flush();

                $this->addFlash(
                    'success',
                    $message
                );

                return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
            } else {
                $user->setProfile($form_profile);
                $this->em->flush();

                $this->addFlash(
                    'success',
                    $message
                );

                return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
            }

        } 

        $data = array_merge($headerData, [
            'form' => $form->createview(),
            'homePages' => $homePages
        ]);

        return $this->render('front/account/edit_profile.html.twig', $data);
        
    }

    #[Route('/feane/update-password', name: 'app_update_password_user')]
    #[Security("is_authenticated() and is_granted('ROLE_USER')")]
    public function updatePassword(Request $request, TokenStorageInterface $tokenStorage) 
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $user = $this->getUser();

        $passwordUpdate = new PasswordUpdate();

        $form = $this->createForm(UpdatePasswordFormType::class, $passwordUpdate);

        $form->handleRequest($request);

        $message = 'Votre mot de passe a bien été modifié! Vous pouvez maintenant vous connecter!';
        $messageError = 'Le mot de passe que vous avez tapé n\'est pas votre mot de passe actuel!';

        if ($form->isSubmitted() && $form->isValid()) {

            $oldPassword = $form->get('oldPassword')->getData();

            $passwordValid = $this->encoder->isPasswordValid($user, $oldPassword);
        
            if ($passwordValid) {
                $newPassword = $passwordUpdate->getNewPassword();
                $password = $this->encoder->hashPassword($user, $newPassword);
        
                $user->setPassword($password);
        
                $this->em->persist($user);
                $this->em->flush();
        
                $this->addFlash(
                    'success',
                    $message
                );

                $tokenStorage->setToken(null);
        
                return $this->redirectToRoute('app_login_front', [], Response::HTTP_SEE_OTHER);
            } else {
                $form->get('oldPassword')->addError(new FormError($messageError));
            }
        }
        
        $data = array_merge($headerData, [
            'form' => $form->createview(),
            'homePages' => $homePages,
        ]);

        return $this->renderForm('front/account/update_password.html.twig', $data);
    }

    #[Route('/user/mot-de-passe-oublié', name:'forgotten_password_user')]
    public function forgottenPassword(TokenGeneratorInterface $tokenGenerator, Request $request)
    {
        $homePages = $this->homePage->findAll();
        $headerData = $this->headerDataProvider->getHeaderData();

        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user = $this->userRepo->findOneByEmail($form->get('email')->getData());

            if($user){
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $this->em->persist($user);
                $this->em->flush();
            
                $url = $this->generateUrl('reset_pass_user', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                
                $context = compact('url', 'user');

                $this->mail->send(
                    'lnomenjanahary68@gmail.com',
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    'password_reset_user',
                    $context
                );

                $this->addFlash('success', 'Email envoyé avec succès, veuillez consulter votre boite de reception mail');
                return $this->redirectToRoute('app_login_front');
            }
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login_front');
        }

        $data = array_merge($headerData, [
            'form' => $form->createview(),
            'homePages' => $homePages,
        ]);
                
        return $this->render('front/account/reset_password_request.html.twig', $data);
    }

    #[Route('/user/mot-de-passe-oublié/{token}', name:'reset_pass_user')]
    public function resetPass(string $token, Request $request,): Response
    {
        $homePages = $this->homePage->findAll();

        $headerData = $this->headerDataProvider->getHeaderData();

        $user = $this->userRepo->findOneByResetToken($token);
        
        if($user){
            $form = $this->createForm(ResetPasswordFormType::class);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $user->setResetToken('');
                $user->setPassword(
                    $this->encoder->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $this->em->persist($user);
                $this->em->flush();

                $this->addFlash('success', 'Mot de passe changé avec succès, vous pouvez vous connecter maintenant');
                return $this->redirectToRoute('app_login_front');
            }

            $data = array_merge($headerData, [
                'form' => $form->createview(),
                'homePages' => $homePages,
            ]);

            return $this->render('front/account/reset_password.html.twig', $data);
        }
        
        $this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('app_login_front');
    }
}
