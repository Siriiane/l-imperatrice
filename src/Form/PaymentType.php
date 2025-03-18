<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;



class PaymentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero_de_carte', TextType::class, [
                'label' => 'Numéro de carte',
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de carte est obligatoire.']),
                    new Length(['min' => 16, 'max' => 16, 'exactMessage' => 'Le numéro de carte doit contenir 16 chiffres.']),
                    new Regex(['pattern' => '/^\d{16}$/', 'message' => 'Le numéro de carte doit contenir uniquement des chiffres.'])
                ]
            ])
            ->add('date_expiration', TextType::class, [
                'label' => 'Date d\'expiration (MM/YY)',
                'constraints' => [
                    new NotBlank(['message' => 'La date d\'expiration est obligatoire.']),
                    new Regex([
                        'pattern' => '/^(0[1-9]|1[0-2])\/\d{2}$/',
                        'message' => 'Le format doit être MM/YY.'
                    ])
                ]
            ])
            ->add('cvv', TextType::class, [
                'label' => 'CVV',
                'constraints' => [
                    new NotBlank(['message' => 'Le CVV est obligatoire.']),
                    new Length(['min' => 3, 'max' => 4, 'exactMessage' => 'Le CVV doit contenir 3 ou 4 chiffres.']),
                    new Regex(['pattern' => '/^\d{3,4}$/', 'message' => 'Le CVV doit contenir uniquement des chiffres.'])
                ]
            ])
            ->add('titulaire', TextType::class, [
                'label' => 'Nom du titulaire',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom du titulaire est obligatoire.']),
                    new Regex(['pattern' => '/^[a-zA-Z\s\-]+$/', 'message' => 'Le nom ne doit contenir que des lettres et des espaces.'])
                ]
            ])
            ->add('payer', SubmitType::class, [
                'label' => 'Payer'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
