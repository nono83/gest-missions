<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Pays;
use App\Entity\Mission;
use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'help' => "Le nom du contact",
                'label' => 'Nom*',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'help' => "Le prénom du contact",
                'label' => 'Prénom'
            ])
            ->add('date_naissance', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de naissance*',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('nom_code', TextType::class, [
                'help' => "Le nom de code du contact",
                'label' => 'Nom de code*',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('nationalite', EntityType::class, [
                'class' => Pays::class,
                'placeholder' => "Sélectionnez un pays",
                'choice_label' => 'nom', 
            ])
            ->add('mission', EntityType::class, [
                'class' => Mission::class,
                'placeholder' => "Sélectionnez une mission",
                'choice_label' => 'titre', 
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
