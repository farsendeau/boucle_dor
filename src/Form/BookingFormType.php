<?php

namespace App\Form;

use App\DTO\Booking;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class BookingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'gite.form_lastname',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2])
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'gite.form_firstname',
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 2])
                ]
            ])
            ->add('mail', EmailType::class, [
                'label' => 'gite.form_mail',
                'constraints' => [
                    new NotBlank(),
                    new Email()
                ]
            ])
            ->add('nbAdult', IntegerType::class, [
                'label' => 'gite.form_nb_adult',
                'attr' => ['min' => 1],
                'constraints' => [
                    new NotBlank(),
                    new Range(['min' => 1])
                ]
            ])
            ->add('nbChild', IntegerType::class, [
                'label' => 'gite.form_nb_child',
                'required' => false,
            ])
            ->add('dateArrival', DateType::class, [
                'label' => 'gite.form_date_arrival',
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new DateTime())->format('Y-m-d')
                ],
                'constraints' => [
                    new NotBlank(),
                    new GreaterThanOrEqual(['value' => 'today'])
                ]
            ])
            ->add('dateDeparture', DateType::class, [
                'label' => 'gite.form_date_departure',
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new DateTime('+1 day'))->format('Y-m-d')
                ],
                'constraints' => [
                    new NotBlank(),
                    new GreaterThan(['propertyPath' => 'parent.all[dateArrival].data'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
