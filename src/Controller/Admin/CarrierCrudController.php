<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CarrierCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Carrier::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle('index', 'Liste Transporteurs ')
            ->setPageTitle('detail', 'Detail de Transporteur')
            ->setPageTitle('new', 'Créer Transporteur')
            ->setPageTitle('edit', 'Modification Transporteur')
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('Transporteur')
            ->setEntityLabelInPlural('Transporteurs')
            ;
    } 

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id', 'Identification')
            ->hideOnForm();
            
        yield BooleanField::new('isActive', 'Active ?');
            
        yield TextField::new('name', 'Nom du transport')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : Service rapide'
            ]);        
    
        yield MoneyField::new('price', 'Prix de transport')
        ->setCurrency('EUR')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
            ]);

        yield TextareaField::new('description', 'Description')
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
