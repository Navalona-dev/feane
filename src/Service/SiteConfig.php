<?php

namespace App\Service;

use App\Entity\MenuHeader;
use App\Entity\SiteConfiguration;
use App\Repository\MenuHeaderRepository;
use App\Repository\SiteConfigurationRepository;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;



class SiteConfig 
{
    private SiteConfiguration $config;
    private MenuHeader $configMenu;

    public function __construct(
        private UploaderHelper        $vich,
        SiteConfigurationRepository   $siteConfigRepository,
        MenuHeaderRepository          $menuRepo,
        
    )
    {
        $this->config = $siteConfigRepository->findOneBy([]);
        $this->configMenu = $menuRepo->findOneBy([]);
        $this->menuRepo = $menuRepo->findBy(['isActive' => true]);
    }

    public function getTelephone(): ?string
    {
        return $this->config ? $this->config->getTelephone() : null;
    }

    public function getEmail(): ?string
    {
        return $this->config ? $this->config->getEmail() : null;
    }

    public function getAdresse(): ?string
    {
        return $this->config ? $this->config->getAdresse() : null;
    }

    public function getLogoUrl(): ?string
    {
        return $this->config ? $this->config->getLogo() : null;
    }

    public function getFaviconUrl(): ?string
    {
        return $this->config ? $this->config->getFavicon() : null;
    }


public function getMenuLinks(): array
{
    $menuLinks = [];
    
    $menus = $this->menuRepo;

    foreach ($menus as $menu) {
        $dropDownMenusActiveAndIsUserLogin = [];
        $dropDownMenusActiveAndIsUserLogout = [];
        $dropDownMenus = [];

        if ($menu->getDropdownMenus()->count() > 0) {
            foreach ($menu->getDropdownMenus() as $dropdown) {
                    if($dropdown->isIsActive() == true && $dropdown->isIsUserLogout() == false ){
                    $dropDownMenusActiveAndIsUserLogin[] = [
                        'name' => $dropdown->getName(),
                        'link' => $dropdown->getLink(),
                        'isParameter' => $dropdown->isIsParameter(), 
                        'isUserLogout' => $dropdown->isIsUserLogout(), 
                    ];
                    }
            }
        } else {
            
        }

        if ($menu->getDropdownMenus()->count() > 0) {
            foreach ($menu->getDropdownMenus() as $dropdown) {
                    if($dropdown->isIsActive() == true && $dropdown->isIsUserLogout() == true ){
                    $dropDownMenusActiveAndIsUserLogout[] = [
                        'name' => $dropdown->getName(),
                        'link' => $dropdown->getLink(),
                        'isParameter' => $dropdown->isIsParameter(), 
                        'isUserLogout' => $dropdown->isIsUserLogout(), 
                    ];
                    }
            }
        } else {
            
        }

        if ($menu->getDropdownMenus()->count() > 0) {
            foreach ($menu->getDropdownMenus() as $dropdown) {
                    $dropDownMenus[] = [
                        'name' => $dropdown->getName(),
                        'link' => $dropdown->getLink(),
                        'isParameter' => $dropdown->isIsParameter(), 
                        'isUserLogout' => $dropdown->isIsUserLogout(), 
                    ];
            }
        } else {
            
        }

        $menuLinks[] = [
            'name' => $menu->getName(),
            'link' => $menu->getLink(),
            'isParameter' => $menu->isIsParameter(),
            'dropDownMenusActiveAndIsUserLogin' => $dropDownMenusActiveAndIsUserLogin,
            'dropDownMenusActiveAndIsUserLogout' => $dropDownMenusActiveAndIsUserLogout,
            'dropDownMenus' => $dropDownMenus,
        ];
    }
    
    return $menuLinks;
}






    
}