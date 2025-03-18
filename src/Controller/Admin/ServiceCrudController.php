<?php

namespace App\Controller\Admin;

use App\Entity\Service;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ServiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Service::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle('index', 'Liste de Services')
            ->setPageTitle('detail', 'Detail de Service')
            ->setPageTitle('new', 'Créer Service ')
            ->setPageTitle('edit', 'Modification Service')
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('Service')
            ->setEntityLabelInPlural('Services')
            ;
    } 

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id', 'Identification')
            ->hideOnForm();

        yield BooleanField::new('isActive', 'Est-il activé ?');

        yield TextField::new('title', 'Titre de service')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : Service traiteur'
            ]);  

        yield TextField::new('subTitle', 'Sous titre de service')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : Feane à Votre Porte : Service Traiteur'
            ]);  


        yield TextField::new('iconFile', 'Icone')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : fas fa-home'
            ]);  
        
        yield TextareaField::new('description', 'Petite Description')
        ->setFormTypeOption(
            'attr', 
            [
                'class' => 'ckeditor',
                'required' => true,

            ]);  

      
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->add(Crud::PAGE_INDEX, 'detail');

        return $actions;
    }
}
