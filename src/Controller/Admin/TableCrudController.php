<?php

namespace App\Controller\Admin;

use App\Entity\Table;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TableCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Table::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle('index', 'Liste de Tables')
            ->setPageTitle('detail', 'Detail de Table')
            ->setPageTitle('new', 'Créer Table')
            ->setPageTitle('edit', 'Modification Table')
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('Table')
            ->setEntityLabelInPlural('Tables')
            ;
    } 

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id', 'Identification')
            ->hideOnForm();

        yield BooleanField::new('isActive', 'Est-il activé ?');

        yield TextField::new('number', 'Numéro de table')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : Table 1'
            ]);   
            
        yield TextField::new('capacity', 'Capacité')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : 4 Personnes'
            ]);  
            
        yield TextField::new('type', 'Type de table')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : table ronde, etc'
            ]);   

        yield TextField::new('emplacement', 'Emplacement')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple :  Près de la fenêtre'
            ]);   

        yield TextField::new('noteSpecial', 'Notes Spéciales')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : Préférence pou une bougie, ...'
            ])
            ->hideOnIndex();   

        yield MoneyField::new('coutReservation', 'Coût de réservation')
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

            ])
        ->hideOnIndex();

        yield ImageField::new('image', 'Image')
            ->setBasePath('/uploads/tables')
            ->setUploadDir('public/uploads/tables')
            ;

      
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->add(Crud::PAGE_INDEX, 'detail');

        return $actions;
    }
}
