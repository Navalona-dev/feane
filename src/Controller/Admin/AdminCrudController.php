<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class AdminCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Admin::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle('detail', 'Detail de l\'administrateur')
            ->setPageTitle('index', 'Liste des administrateurs')
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('Administrateur')
            ->setEntityLabelInPlural('Administrateurs')
            ;
    } 

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id', 'Identification')
        ->hideOnForm();
            
        yield BooleanField::new('isSendMail', 'Est-il le destinataire de l\'nvoie de mail');
        
        yield TextField::new('firstName', 'Nom de famille')
        ->hideOnForm();

        yield TextField::new('lastName', 'Prénom(s)')
        ->hideOnForm();

        yield TextField::new('email', 'Adresse e-amail')
        ->hideOnForm();


      
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->disable(Action::NEW);

        $actions->disable(Action::DELETE);

        $actions->add(Crud::PAGE_INDEX, 'detail');

        return $actions;
    }
}
