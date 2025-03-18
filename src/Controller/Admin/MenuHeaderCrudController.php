<?php

namespace App\Controller\Admin;

use App\Entity\MenuHeader;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class MenuHeaderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MenuHeader::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle('index', 'Liste de Menus')
            ->setPageTitle('detail', 'Detail de Menu')
            ->setPageTitle('new', 'Créer Menu')
            ->setPageTitle('edit', 'Modification Menu')
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('Menu d\'en-tête')
            ->setEntityLabelInPlural('Menus d\en-tête')
            ;
    } 

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id', 'Identification')
            ->hideOnForm();

        yield TextField::new('name', 'Menus')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : Accueil'
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

      
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->add(Crud::PAGE_INDEX, 'detail');

        return $actions;
    }
}
