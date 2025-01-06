<?php

namespace App\Form;

use App\Entity\Contact;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'attr' => ['placeholder' => 'Votre Prénom'],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => ['placeholder' => 'Votre Nom'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Email',
                'required' => true,
                'attr' => ['placeholder' => 'Votre Email'],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre Message',
                'required' => true,
                'attr' => ['placeholder' => 'Ecrire votre message ici', 'rows' => 10, 'cols' => 70],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Pas d'entité, donc on désactive la liaison automatique
            'data_class' => Contact::class,
        ]);
    }
}
