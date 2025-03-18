<?php

namespace App\Service;

use DateTime;
use App\Entity\Table;
use App\Entity\Carrier;
use App\Entity\Produit;
use App\Entity\Service;
use App\Entity\HomePage;
use App\Entity\MenuHeader;
use App\Entity\SocialLink;
use App\Entity\DropdownMenu;
use App\Entity\HomePageBlock;
use App\Entity\MenuRestaurant;
use Symfony\Component\Yaml\Yaml;
use App\Entity\SiteConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use function Symfony\Component\String\u;
use App\Repository\MenuRestaurantRepository;
use Symfony\Component\Filesystem\Filesystem;


class DefaultsLoader
{

    public function __construct(
        private EntityManagerInterface $em,
        private MenuRestaurantRepository $menuResRepo,
    )
    {
    }

    private function maybeCreate($class, $criteria, ?string $repositoryMethodName = 'findOneBy'): array
    {
        $entity = $this->em->getRepository($class)->{$repositoryMethodName}($criteria);
        $isNew = is_null($entity);
        if ($isNew) {
            $entity = new $class;
        }
        return [$isNew, $entity];
    }

    public function loadDb()
    {
        $this->homePageSections();
        $this->services();
        $this->menusRestaurant();
        $this->tables();
        $this->siteConfigs();
        $this->menusHeader();
        $this->carriers();
        
    }


    public function homePageSections() {
        $homePageSections = Yaml::parseFile('defaults/data/homePage.yaml');

        foreach ($homePageSections as $label => $content) {
            list($isNewHomePageSection, $homePageSection) = $this->maybeCreate(HomePage::class, ['label' => $label]);
            if($isNewHomePageSection){
                $homePageSection->setLabel($label);
                if($content['backgroundColor']) $homePageSection->setBackgroundColor($content['backgroundColor']);
                if($content['backgroundImage']) $homePageSection->setBackgroundImage($content['backgroundImage']);
                if($content['buttonColor']) $homePageSection->setButtonColor($content['buttonColor']);
                if($content['buttonColorHover']) $homePageSection->setButtonColorHover($content['buttonColorHover']);
                if($content['title']) $homePageSection->setTitle($content['title']);
                if($content['description']) $homePageSection->setDescription($content['description']);
                if($content['homePageButton']) $homePageSection->setHomePageButton($content['homePageButton']);
                if($content['image']) $homePageSection->setImage($content['image']);
                if($content['video']) $homePageSection->setVideo($content['video']);
                $this->em->persist($homePageSection);
                $this->em->flush();
            }

            $homePageBlocks = $content['homePageBlocks'] ?? [];
            foreach ($homePageBlocks as $label => $homePageBlock) {
                list($isNewHomePageBlock, $newHomePageBlock) = $this->maybeCreate(HomePageBlock::class, ['homePage' => $homePageSection, 'label' => $label]);
                if($isNewHomePageBlock){
                    $newHomePageBlock->setHomePage($homePageSection);
                    $newHomePageBlock->setLabel($label);
                    $newHomePageBlock->setTitle($homePageBlock['title']);
                    if($homePageBlock['description']) $newHomePageBlock->setDescription($homePageBlock['description']);
                    $newHomePageBlock->setHomePageBlockButton($homePageBlock['homePageBlockButton']);
                    $newHomePageBlock->setImage($homePageBlock['image']);
                    $newHomePageBlock->setBackgroundColor($homePageBlock['backgroundColor']);
                    $this->em->persist($newHomePageBlock);
                    $this->em->flush();
                }
                
            }
        
        }
    }

    public function services() {
        $services = Yaml::parseFile('defaults/data/service.yaml');

        foreach ($services as $label => $content) {
            list($isNewService, $service) = $this->maybeCreate(Service::class, ['label' => $label]);
            if($isNewService){
                $service->setLabel($label);
                $service->setIsActive(true);
                $service->setTitle($content['title']);
                $service->setSubTitle($content['subTitle']);
                $service->setDescription($content['description']);
                $service->setIconFile($content['iconFile']);
                $this->em->persist($service);
                $this->em->flush();
            }
            
        }
    }

    public function menusRestaurant() {
        $menusRestaurant = Yaml::parseFile('defaults/data/menuRestaurant.yaml');

        foreach ($menusRestaurant as $label => $content) {
            list($isNewMenuRes, $menu) = $this->maybeCreate(MenuRestaurant::class, ['label' => $label]);
            if($isNewMenuRes){
                $menu->setLabel($label);
                $menu->setIsActive(true);
                $menu->setName($content['name']);
                $this->em->persist($menu);
                $this->em->flush();
            }

            $produits = $content['produits'] ?? [];

            foreach ($produits as $label => $content) {
            
                list($isNewProduit, $produit) = $this->maybeCreate(Produit::class, ['menuRestaurant' => $menu, 'label' => $label]);
                if($isNewProduit){
                    $produit->setLabel($label);
                    $produit->setMenuRestaurant($menu);
                    $produit->setIsActive(true);
                    $produit->setIsReduction(false);
                    $produit->setIsOutOffStock(false);
                    $produit->setTitle($content['title']);
                    $produit->setDescription($content['description']);
                    $produit->setPrice($content['price']);
                    $produit->setNumber($content['number']);
                    $produit->setImage($content['image']);
                    if($content['reduction']) $produit->setReduction($content['reduction']);
                    if($content['textSpecial']) $produit->setTextSpecial($content['textSpecial']);
    
                    $this->em->persist($produit);
                    $this->em->flush();
    
    
                }
    
            }
            
        }
    }

    public function tables() {
        $tables = Yaml::parseFile('defaults/data/table.yaml');

        foreach ($tables as $label => $content) {
            list($isNewTable, $table) = $this->maybeCreate(Table::class, ['label' => $label]);
            if($isNewTable){
                $table->setLabel($label);
                $table->setIsActive(true);
                $table->setnumber($content['number']);
                $table->setCapacity($content['capacity']);
                $table->setEmplacement($content['emplacement']);
                $table->setType($content['type']);
                $table->setDescription($content['description']);
                $table->setNoteSpecial($content['noteSpecial']);
                $table->setCoutReservation($content['coutReservation']);
                $table->setImage($content['image']);
                $this->em->persist($table);
                $this->em->flush();
            }
            
        }
    }

    public function siteConfigs() {
        $siteConfigs = Yaml::parseFile('defaults/data/siteConfiguration.yaml');

        foreach ($siteConfigs as $label => $content) {
            list($isNewSIteConfig, $siteConfig) = $this->maybeCreate(SiteConfiguration::class, ['label' => $label]);
            if($isNewSIteConfig){
                $siteConfig->setLabel($label);
                $siteConfig->setIsActive(true);
                if($content['favicon']) $siteConfig->setFavicon($content['favicon']);
                if($content['logo']) $siteConfig->setLogo($content['logo']);
                if($content['email']) $siteConfig->setEmail($content['email']);
                if($content['telephone']) $siteConfig->setTelephone($content['telephone']);
                if($content['adresse']) $siteConfig->setAdresse($content['adresse']);

                $this->em->persist($siteConfig);
                $this->em->flush();
            }

            $socialLinks = $content['socialLinks'] ?? [];
            foreach ($socialLinks as $label => $content) {
                list($isNewSocialLink, $newSocialLink) = $this->maybeCreate(SocialLink::class, ['siteConfiguration' => $siteConfig, 'label' => $label]);
                if($isNewSocialLink){
                    $newSocialLink->setSiteConfiguration($siteConfig);
                    $newSocialLink->setLabel($label);
                    $newSocialLink->setIsActive(true);
                    $newSocialLink->setIcon($content['icon']);
                    $newSocialLink->setName($content['name']);
                    $newSocialLink->setLink($content['link']);
                    $this->em->persist($newSocialLink);
                    $this->em->flush();
                }
                
            }
            
        }
    }

    public function menusHeader() {
        $menusHeader = Yaml::parseFile('defaults/data/menuHeader.yaml');

        foreach ($menusHeader as $label => $content) {
            list($isNewMenuHeader, $menuHeader) = $this->maybeCreate(MenuHeader::class, ['label' => $label]);
            if($isNewMenuHeader){
                $menuHeader->setLabel($label);
                $menuHeader->setIsActive(true);
                $menuHeader->setName($content['name']);
                $menuHeader->setLink($content['link']);
                $menuHeader->setIsParameter($content['isParameter']);

                $this->em->persist($menuHeader);
                $this->em->flush();
            }

            $dropdownMenus = $content['dropdownMenus'] ?? [];
            foreach ($dropdownMenus as $label => $content) {
                list($isNewDropdownMenu, $dropdown) = $this->maybeCreate(DropdownMenu::class, ['menuHeader' => $menuHeader, 'label' => $label]);
                if($isNewDropdownMenu){
                    $dropdown->setMenuHeader($menuHeader);
                    $dropdown->setLabel($label);
                    $dropdown->setIsActive(true);
                    $dropdown->setName($content['name']);
                    $dropdown->setLink($content['link']);
                    $dropdown->setIsParameter($content['isParameter']);
                    $dropdown->setIsUserLogout($content['isUserLogout']);

                    $this->em->persist($dropdown);
                    $this->em->flush();
                }
                
            }
            
        }
    }

    public function carriers() {
        $carriers = Yaml::parseFile('defaults/data/carrier.yaml');

        foreach ($carriers as $label => $content) {
            list($isNewCarrier, $carrier) = $this->maybeCreate(Carrier::class, ['label' => $label]);
            if($isNewCarrier){
                $carrier->setLabel($label);
                $carrier->setIsActive(true);
                $carrier->setName($content['name']);
                $carrier->setDescription($content['description']);
                $carrier->setPrice($content['price']);
                $this->em->persist($carrier);
                $this->em->flush();
            }
            
        }
    }

    public function copyFiles()
    {
        $fs = new Filesystem();
        $fileDefs = Yaml::parseFile('defaults/files.yaml') ?? [];
        foreach ($fileDefs as $destDir => $fileMappings) {
            foreach ($fileMappings as $dest => $source) {
                $destFile = u('/')->join([u($destDir), $dest]);
                $fs->copy($source, $destFile);
            };
        };
    }
}
