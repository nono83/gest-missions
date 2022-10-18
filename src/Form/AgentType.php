<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Agent;
use App\Entity\Pays;
use App\Entity\Specialite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AgentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'help' => "Le nom de l'agent",
                'label' => 'Nom*',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'help' => "Le prénom de l'agent",
                'label' => 'Prénom*',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('date_naissance', DateType::class, [
                'help' => "La date de naissance de l'agent",
                'widget' => 'single_text',
                'label' => 'Date de naissance*',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('code_identification', TextType::class, [
                'help' => "Le code d'identification de l'agent",
                'label' => 'Code d\'identification*',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('nationalite', EntityType::class, [
                'label' => 'Nationalité*',
                'placeholder' => 'Choisissez une option',
                'class' => Pays::class,
                'choice_label' => 'nom', 
            ])
            ->add('specialites', EntityType::class, [
                'label' => 'Spécialités*',
                'placeholder' => 'Choisissez une option',
                'class' => Specialite::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'nom', 
            ])
            //Association agent et mission s'effectue à la saisie d'une mission
           // ->add('missions')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Agent::class,
        ]);
    }
}
