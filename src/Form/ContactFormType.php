<?php

namespace App\Form;

use App\DTO\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'contact.lastname',
                'constraints' => [new NotBlank()],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'contact.firstname',
                'constraints' => [new NotBlank()],
            ])
            ->add('mail', EmailType::class, [
                'label' => 'contact.mail',
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ],
            ])
            ->add('tel', TelType::class, [
                'label' => 'contact.tel',
                'constraints' => [
                    new NotBlank(),
                    new Regex([
                        'pattern' => '/^\+?[0-9\s]{8,15}$/',
                        'message' => 'tel.invalid'
                    ])
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'contact.message',
                'constraints' => [new NotBlank()],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
