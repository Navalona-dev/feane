<?php

namespace App\Controller\Admin;

use App\Entity\Testimonial;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TestimonialCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Testimonial::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle('detail', 'Detail de Témoignage')
            ->setPageTitle('index', 'Liste de Témoignages')
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('Témoignage')
            ->setEntityLabelInPlural('Témoignages')
            ;
    } 

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id', 'Identification')
            ->hideOnForm();
            
        yield BooleanField::new('isActive', 'Est-il activé');

        yield AssociationField::new('user', 'Client')->hideOnForm();
        
        yield DateTimeField::new('createdAt', 'Date de création')->hideOnForm();


      
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->disable(Action::NEW);

        $actions->add(Crud::PAGE_INDEX, 'detail');

        return $actions;
    }
}
