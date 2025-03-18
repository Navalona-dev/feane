<?php

namespace App\Form;

use App\Entity\Adresse;
use App\Entity\Carrier;
use App\Repository\CarrierRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'];

        $builder
            ->add('adresses', EntityType::class, [
                'class' => Adresse::class,
                'choices' => $user->getAdresses(),
                'required' => true,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('carriers', EntityType::class, [
                'class' => Carrier::class,
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'query_builder' => function (CarrierRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.isActive = :isActive')
                        ->setParameter('isActive', true);
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'user' => array(),
        ]);
    }
}
