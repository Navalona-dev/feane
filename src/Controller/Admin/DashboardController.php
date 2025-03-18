<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Order;
use App\Entity\Table;
use App\Entity\Carrier;
use App\Entity\Produit;
use App\Entity\Service;
use App\Entity\MenuHeader;
use App\Entity\SocialLink;
use App\Entity\Testimonial;
use App\Entity\DropdownMenu;
use App\Entity\MenuRestaurant;
use App\Entity\PasswordUpdate;
use App\Entity\SiteConfiguration;
use App\Form\UpdateAdminFormType;
use App\Form\UpdateEmailFormType;
use App\Form\UpdateProfileFormType;
use App\Service\HeaderDataProvider;
use App\Form\UpdatePasswordFormType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private HeaderDataProvider $headerDataProvider,
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $encoder,

    )
    {

    }

    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/dashboard.html.twig', [

        ]);
    }

    #[Route(path: '/admin/mon-profile', name: 'app_admin_mon_profile')]
    #[IsGranted("ROLE_ADMIN")]
    public function profile(Request $request): Response
    {
        $admin = $this->getUser();

        return $this->render('admin/account/mon_profile.html.twig', [
            'admin' => $admin,
        ]);
    }

    #[Route('/admin/edit-information', name: 'app_info_admin_edit')]
    #[Security("is_authenticated() and is_granted('ROLE_ADMIN')")]
    public function edit(Request $request): Response
    {
        $headerData = $this->headerDataProvider->getHeaderData();

        $admin = $this->getUser();

        $form = $this->createForm(UpdateAdminFormType::class, $admin);
        $form->handleRequest($request);
        $message = 'Votre compte a bien été modifié! Merci pour votre confiance';

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->flush();

            $this->addFlash(
                'success',
                $message
            );

            return $this->redirectToRoute('app_admin_mon_profile', [], Response::HTTP_SEE_OTHER);
        } 

        $data = array_merge($headerData, [
            'form' => $form->createview(),
        ]);

        return $this->render('admin/account/edit_information.html.twig', $data);
        
    }

    #[Route('/admin/update-password', name: 'admin_password_edit')]
    #[Security("is_authenticated() and is_granted('ROLE_ADMIN')")]
    public function updatePassword(Request $request, TokenStorageInterface $tokenStorage) 
    {
        $headerData = $this->headerDataProvider->getHeaderData();

        $passwordUpdate = new PasswordUpdate();

        $form = $this->createForm(UpdatePasswordFormType::class, $passwordUpdate);

        $form->handleRequest($request);

        $message = 'Votre mot de passe a bien été modifié! Vous pouvez maintenant vous connecter!';

        $messageError = 'Le mot de passe que vous avez tapé n\'est pas votre mot de passe actuel!';

        if ($form->isSubmitted() && $form->isValid()) {
            $admin = $this->getUser();

            $oldPassword = $form->get('oldPassword')->getData();

            $passwordValid = $this->encoder->isPasswordValid($admin, $oldPassword);
        
            if ($passwordValid) {
                $newPassword = $passwordUpdate->getNewPassword();
                $password = $this->encoder->hashPassword($admin, $newPassword);
        
                $admin->setPassword($password);
        
                $this->em->persist($admin);
                $this->em->flush();
        
                $this->addFlash(
                    'success',
                    $message
                );

                $tokenStorage->setToken(null);
        
                return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
            } else {
                $form->get('oldPassword')->addError(new FormError($messageError));
            }
        }
        
        $data = array_merge($headerData, [
            'form' => $form->createview(),
        ]);

        return $this->renderForm('admin/account/update_password.html.twig', $data);
    }

    #[Route('/admin/edit-email', name: 'app_admin_edit_email')]
    #[Security("is_authenticated() and is_granted('ROLE_ADMIN')")]
    public function editemail(Request $request): Response
    {
        $headerData = $this->headerDataProvider->getHeaderData();

        $admin = $this->getUser();

        $form = $this->createForm(UpdateEmailFormType::class, $admin);
        $form->handleRequest($request);
        $message = 'Votre email a bien été modifié! Merci pour votre confiance!';

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->flush();

            $this->addFlash(
                'success',
                $message
            );

            return $this->redirectToRoute('app_admin_mon_profile', [], Response::HTTP_SEE_OTHER);
        } 

        $data = array_merge($headerData, [
            'form' => $form->createview(),
        ]);

        return $this->render('admin/account/edit_mail.html.twig', $data);
        
    }

    #[Route('/admin/edit-profile-photo', name: 'app_admin_edit_profile')]
    #[Security("is_authenticated() and is_granted('ROLE_ADMIN')")]
    public function editprofile(Request $request): Response
    {
        $headerData = $this->headerDataProvider->getHeaderData();

        $admin = $this->getUser();

        $originalPhoto = $admin->getProfile();

        $form = $this->createForm(UpdateProfileFormType::class, $admin);
        $form->handleRequest($request);
        
        $message = 'Votre photo de profile a bien été modifié! Merci pour votre confiance!';       if ($form->isSubmitted() && $form->isValid()) {

            $form_profile = $form->get('imageFile')->getData();

            if($originalPhoto == 0) {
                $admin->setProfile($form_profile);
                $this->em->persist($admin);
                $this->em->flush();

                $this->addFlash(
                    'success',
                    $message
                );

                return $this->redirectToRoute('app_admin_mon_profile', [], Response::HTTP_SEE_OTHER);
            } else {
                $admin->setProfile($form_profile);
                $this->em->flush();

                $this->addFlash(
                    'success',
                    $message
                );

                return $this->redirectToRoute('app_admin_mon_profile', [], Response::HTTP_SEE_OTHER);
            }

        } 

        $data = array_merge($headerData, [
            'form' => $form->createview(),
        ]);

        return $this->render('admin/account/edit_profile.html.twig', $data);
        
    }

    public function configureDashboard(): Dashboard
    {
        $headerData = $this->headerDataProvider->getHeaderData();
        
        $logo = $headerData['logo'];

        $favicon = $headerData['favicon'];
    
        return Dashboard::new()
            ->setTitle('<img src="../../uploads/siteConfig/' .$logo .'" />')
            ->setFaviconPath('../../uploads/siteConfig/' .$favicon);
            
    }

    /**
     * @return Assets
     */
    public function configureAssets(): Assets
    {
        return Assets::new()
            ->addCssFile('assets/css/admin.css')
            ->addJsFile('assets/js/jquery.min.js')
            ->addJsFile('assets/plugins/ckeditor/ckeditor.js')
            ->addJsFile('assets/js/admin.js');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkToRoute('Mon Profile', 'fa fa-user', 'app_admin_mon_profile');
        yield MenuItem::linkToCrud('Administrateurs', 'fas fa-users', Admin::class);
        yield MenuItem::linkToCrud('Commandes', 'fas fa-shopping-cart', Order::class);
        yield MenuItem::linkToCrud('Menus d\'en-tête', 'fas fa-bars', MenuHeader::class);
        yield MenuItem::linkToCrud('Menus Réstaurant', 'fas fa-utensils', MenuRestaurant::class);
        yield MenuItem::linkToCrud('Menus Déroulant', 'fas fa-chevron-down', DropdownMenu::class);
        yield MenuItem::linkToCrud('Produits', 'fas fa-shopping-basket', Produit::class);
        yield MenuItem::linkToCrud('Réseaux sociaux', 'fab fa-instagram', SocialLink::class);
        yield MenuItem::linkToCrud('Services', 'fas fa-cogs', Service::class);
        yield MenuItem::linkToCrud('Tables', 'fas fa-chair', Table::class);
        yield MenuItem::linkToCrud('Transporteur', 'fas fa-truck', Carrier::class);
        yield MenuItem::linkToCrud('Témoignages', 'fas fa-comments', Testimonial::class);
        yield MenuItem::linkToCrud('Configuration du site', 'fas fa-cog', SiteConfiguration::class);
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-home', 'app_admin');
        
    }
}
