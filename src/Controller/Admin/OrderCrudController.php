<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud 
            ->setPageTitle('detail', 'Detail de commande')
            ->setPageTitle('index', 'Liste de commandes')
            ->setPaginatorPageSize(10)
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
            ->setDefaultSort(['id' => 'DESC'])
            ;
    } 

    public function configureFields(string $pageName): iterable
    {
        yield IntegerField::new('id', 'Identification')
            ->hideOnForm();
            
        yield BooleanField::new('isPaid', 'Est-il payé');

        yield AssociationField::new('orderDetails', 'Produits achetés')
            ->hideOnForm()
            ->formatValue(function ($value, $entity) {
                $count = count($entity->getOrderDetails());
                if ($count === 0) {
                    $produitList = '<li><a href="#" class="produit-list" data-list="produit-list-0" data-parent=".produit-link" style="color: #000;">Aucun Produit</a></li>';
                } else {
                    $produitList = implode(' ', array_map(function($produit, $index) {
                        $produitAndQuantity = '- ' .$produit->getProduct(). ' x ' .$produit->getQuantity();
                        $index++;
                        return '<li class="mt-3"><a href="#" class="produit-list fs-6" data-list="produit-list-' . $index . '" data-parent=".produit-link" style="color: #000;">'.$produitAndQuantity.'</a></li>';
                    }, $entity->getOrderDetails()->toArray(), array_keys($entity->getOrderDetails()->toArray())));
                }
                
                return '<span class="produit-link-wrapper"><a href="#" class="produit-link text-black" data-count="'.$count.'" data-list="'.htmlspecialchars($produitList).'" style="color: #000;">'.$count.'</a></span>';
            });

        yield AssociationField::new('user', 'Client');

        yield TextField::new('delivery', 'Adresse de livraison')
        ->hideOnIndex();

        yield TextField::new('carrierName', 'Transporteur');

        yield AssociationField::new('orderDetails', 'Total produit')
        ->hideOnForm()
        ->formatValue(function ($value, $entity) {
            $total = 0;

            foreach ($entity->getOrderDetails() as $orderDetail) {
                $carrierPrice = $orderDetail->getMyOrder()->getCarrierPrice();
                $total += $orderDetail->getTotal();
            }

            return number_format($total / 100, 2, ',', '') . ' €';

        });

        yield MoneyField::new('carrierPrice', 'Prix de livraison')->setCurrency('EUR');

        yield DateTimeField::new('createdAt', 'Date de création');


      
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->disable(Action::NEW);

        $actions->disable(Action::EDIT);

        $actions->disable(Action::DELETE);

        $actions->add(Crud::PAGE_INDEX, 'detail');

        return $actions;
    }
}
