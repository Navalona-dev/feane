<?php

namespace App\Controller\Admin;

use App\Entity\Produit;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ProduitCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Produit::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle('index', 'Liste de Produits')
            ->setPageTitle('detail', 'Detail de Produit')
            ->setPageTitle('new', 'Créer Produit ')
            ->setPageTitle('edit', 'Modification Produit')
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits')
            ;
    } 

    public function configureFields(string $pageName): iterable
    {
        yield BooleanField::new('isActive', 'Est-il activé ?');

        yield BooleanField::new('isReduction', 'Est-il en réduction ?');

        yield BooleanField::new('isOutOffStock', 'Est-il hors stock ?');

        yield AssociationField::new('menuRestaurant', 'Nom du menu');

        yield TextField::new('title', 'Nom du produit')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : Pizza Délicieux'
            ]);  

        yield IntegerField::new('number', 'Nombre de produit');

        yield MoneyField::new('price', 'Prix')
        ->setCurrency('EUR')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
            ]);

        yield TextField::new('reduction', 'Reduction')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : 20%'
            ])
        ->hideOnIndex();

        yield TextField::new('textSpecial', 'Texte special')
        ->setFormTypeOption(
            'attr', 
            [
                'autocomplete' => 'off',
                'placeholder' => 'Exemple : Jeudi Gourmands'
            ])
        ->hideOnIndex();

        yield ImageField::new('image', 'Image')
            ->setBasePath('/uploads/produits')
            ->setUploadDir('public/uploads/produits')
            ;
        
            yield TextareaField::new('description', 'Petite Description')
            ->setFormTypeOption(
                'attr', 
                [
                    'class' => 'ckeditor',
                    'required' => true,
    
                ])
            ->hideOnIndex();  

      
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->add(Crud::PAGE_INDEX, 'detail');

        return $actions;
    }
}
