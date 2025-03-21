<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UpdatePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'attr' => [
                    'class' => 'fom-control form-control-md',
                ],
                'mapped' => false,
            ])
            ->add('newPassword', PasswordType::class, [
                'attr' => [
                    'class' => 'fom-control form-control-md',
                ]
            ])
            ->add('confirmPassword', PasswordType::class, [
                'attr' => [
                    'class' => 'fom-control form-control-md',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
