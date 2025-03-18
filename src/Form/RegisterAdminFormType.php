<?php

namespace App\Form;

use App\Entity\Admin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use MeteoConcept\HCaptchaBundle\Form\HCaptchaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use MeteoConcept\HCaptchaBundle\Validator\Constraints\IsValidCaptcha;

class RegisterAdminFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control form-control-md',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('password', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control form-control-md'
                ]
            ])
            ->add('passwordConfirm', PasswordType::class, [
                'attr' => [
                    'class' => 'form-control form-control-md'
                ],
                'required' => true
            ])
            ->add('firstName', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-md',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-md',
                    'autocomplete' => 'off'
                ]
            ])
            ->add('imageFile', VichImageType::class, [
                'attr' => [
                    'class' => 'form-control form-control-md'
                ]
            ])
            ->add('captcha', HCaptchaType::class, [
                'required' => true,
                'label' => false,
                'constraints' => [
                    new NotBlank(["message" => "Le captcha ne doit pas être null"]),
                    new IsValidCaptcha(),
                ],
                // optionally: use a different site key than the default one:
                'hcaptcha_site_key' => 'f8a3c2c2-4a6c-40d4-944f-16bcc5bae7d2',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Admin::class,
        ]);
    }
}
