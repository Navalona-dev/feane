<?php

namespace App\Controller\Admin;

use App\Entity\SiteConfiguration;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SiteConfigurationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SiteConfiguration::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle('index', 'Liste de configuration du site')
            ->setPageTitle('detail', 'Detail de configuration du site')
            ->setPageTitle('new', 'Créer configuration du site')
            ->setPageTitle('edit', 'Modification configuration du site')
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('Configuration du site')
            ->setEntityLabelInPlural('Configurations du site')
            ;
    } 

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id', 'Identification')
            ->hideOnForm();

        yield TextField::new('label', 'Nom du site')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'exemple : feane'
            ]); 

        yield TextField::new('email', 'Adresse e-mail')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'esample@gmail.com'
            ]);        
    
        yield TextField::new('telephone', 'Numéro de téléphone')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => '+33 7 000 000'
            ]);

        yield TextField::new('adresse', 'Adresse exact')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => '3422 Harrison Street, North Olivia, AZ 85147'
            ]);

        yield ImageField::new('logo', 'Logo du site')
            ->setBasePath('/uploads/siteConfig')
            ->setUploadDir('public/uploads/siteConfig')
            ;

        yield ImageField::new('favicon', 'Favicone')
            ->setBasePath('/uploads/siteConfig')
            ->setUploadDir('public/uploads/siteConfig')
            ;

      
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->add(Crud::PAGE_INDEX, 'detail');

        return $actions;
    }
}
