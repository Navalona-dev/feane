<?php

namespace App\Form;

use App\Form\LoginFormType;
use Symfony\Component\Form\AbstractType;
use Gregwar\CaptchaBundle\Type\CaptchaType;
use Symfony\Component\Form\FormBuilderInterface;
use MeteoConcept\HCaptchaBundle\Form\HCaptchaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use MeteoConcept\HCaptchaBundle\Validator\Constraints\IsValidCaptcha;

class LoginFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'Votre adresse e-mail',
                    'class' => 'mb-2 form-control form-control-md',
                    'autocomplete' => 'off',
                    'required' => true,
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'placeholder' => 'Saisissez votre mot de passe',
                    'class' => 'mb-2 form-control form-control-md password-input',
                    'required' => true,
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
