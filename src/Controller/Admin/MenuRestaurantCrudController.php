<?php

namespace App\Controller\Admin;

use App\Entity\MenuRestaurant;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MenuRestaurantCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MenuRestaurant::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle('index', 'Liste de Menus de réstaurant')
            ->setPageTitle('detail', 'Detail de Menu de réstaurant')
            ->setPageTitle('new', 'Créer Menu Réstaurant')
            ->setPageTitle('edit', 'Modification Menu Réstaurant')
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('Menu Réstaurant')
            ->setEntityLabelInPlural('Menus Réstaurant')
            ;
    } 

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id', 'Identification')
            ->hideOnForm();

        yield BooleanField::new('isActive', 'Est-il activé ?');

        yield TextField::new('name', 'Nom du menu')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : Pizza'
            ]);        

      
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->add(Crud::PAGE_INDEX, 'detail');

        return $actions;
    }
}
