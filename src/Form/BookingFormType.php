<?php

namespace App\Form;

use App\Entity\Booking;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingFormType extends AbstractType
{
    private $frenchTransformer ;

    public function __construct(FrenchToDateTimeTransformer $frenchTransformer) {
        $this->frenchTransformer = $frenchTransformer;
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateBooking', TextType::class, [
                'attr' => [
                    'class' => 'form-control form-control-md',
                    'autocomplete' => 'off'
                ] 
            ])
            ->add('bookingHour', TimeType::class, [
                'widget' => 'choice',
                'hours' => [6, 7, 8, 9, 10, 11, 12, 14, 15, 16, 17, 18, 19, 20, 21, 22],
                'minutes' => [0, 15, 30, 45],
            ])
            ->add('message', HiddenType::class, [
                'attr' => [
                    'class' => 'ckeditor-hidden',
                ],
                'required' => false,
            ])
        ;

        $builder->get('dateBooking')->addModelTransformer($this->frenchTransformer);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
