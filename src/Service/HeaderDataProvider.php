<?php 

namespace App\Service;

use App\Service\JWTService;
use App\Service\SiteConfig;
use App\Repository\SocialLinkRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class HeaderDataProvider implements ServiceSubscriberInterface
{

    public function __construct(
        private SiteConfig $config,
        private SocialLinkRepository $socialRepo,
        private Security $security,
        private JWTService $jwt,
        private FormFactoryInterface $formFactory
    )
    {
        
    }

    public function getHeaderData()
    {
        $user = $this->security->getUser();

        $logo = $this->config->getLogoUrl();
        $contact = $this->config->getTelephone();
        $email = $this->config->getEmail();
        $socialLink = $this->socialRepo->findBy(['isActive' => true]);
        $favicon = $this->config->getFaviconUrl();
        $menus = $this->config->getMenuLinks();
        $adresse = $this->config->getAdresse();

        $isTokenExpired = '';

        if ($user && !$user->getIsVerified()) {
            $token = $user->getResetToken();
            $isTokenExpired = $this->jwt->isExpired($token);
        } else {
            $isTokenExpired = false;
        }

        return [
            'logo' => $logo,
            'favicon' => $favicon,
            'socialLink' => $socialLink,
            'contact' => $contact,
            'email' => $email,
            'menus' => $menus,
            'isTokenExpired' => $isTokenExpired,
            'adresse' => $adresse

        ];
    }

    public static function getSubscribedServices(): array
{
    return [
        // Liste des services auxquels votre classe est abonnée
    ];
}

}
