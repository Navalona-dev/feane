<?php

namespace App\Controller\Admin;

use App\Entity\SocialLink;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class SocialLinkCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SocialLink::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle('index', 'Liste de réseaux sociaux')
            ->setPageTitle('detail', 'Detail de réseau social')
            ->setPageTitle('new', 'Créer réseau social')
            ->setPageTitle('edit', 'Modification réseau social')
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('Réseau social')
            ->setEntityLabelInPlural('Réseaux sociaux')
            ;
    } 

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id', 'Identification')
            ->hideOnForm();

        yield BooleanField::new('isActive', 'Active ?')->onlyOnForms();
            
        yield AssociationField::new('siteConfiguration', 'Nom du site')->formatValue(function ($value, $entity) {
            if ($entity && $entity->getSiteConfiguration()) {
                return $entity->getSiteConfiguration()->getLabel();
            }
        
            return '';
        });

        yield TextField::new('name', 'Nom de réseau social')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : Facebook'
            ]);        
    
        yield TextField::new('link', 'Liens')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : https://facebook.feane.com'
            ]);

        yield TextField::new('icon', 'Icone en fontaoesome')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : fas fa-home'
            ]);

        yield BooleanField::new('isActive', 'Active ?')->hideOnForm();

      
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->add(Crud::PAGE_INDEX, 'detail');

        return $actions;
    }
}
