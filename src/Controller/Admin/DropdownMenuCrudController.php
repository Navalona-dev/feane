<?php

namespace App\Controller\Admin;

use App\Entity\DropdownMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DropdownMenuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DropdownMenu::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle('index', 'Liste de Menus Déroulant')
            ->setPageTitle('detail', 'Detail de Menu déroulant')
            ->setPageTitle('new', 'Créer Menu déroulant')
            ->setPageTitle('edit', 'Modification Menu déroulant')
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('Menu déroulant')
            ->setEntityLabelInPlural('Menus déroulant')
            ;
    } 

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id', 'Identification')
            ->hideOnForm();
            
        yield AssociationField::new('menuHeader', 'Menu d\'en-tête')->formatValue(function ($value, $entity) {
            if ($entity && $entity->getMenuHeader()) {
                return $entity->getMenuHeader()->getName();
            }
        
            return '';
        });

        yield TextField::new('name', 'Menus déroulant')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : Gallery'
            ]);        
    
        yield TextField::new('link', 'Liens')
            ->setFormTypeOption(
                'attr', 
                [
                    'autocomplete' => 'off',
                    'placeholder' => 'Exemple : app_login'
                ]);

        yield BooleanField::new('isActive', 'Active ?');

        yield BooleanField::new('isParameter', 'Avoir un parametre ?');
        yield BooleanField::new('isUserLogout', 'Afficher pour un utlisateur connecter ?');

      
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->add(Crud::PAGE_INDEX, 'detail');

        return $actions;
    }
}
