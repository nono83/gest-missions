<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Pays;
use App\Entity\TypeMission;
use App\Entity\Statut;
use App\Entity\Specialite;
use App\Entity\Planque;
use App\Entity\Cible;
use App\Entity\Contact;
use App\Entity\Agent;
use App\Entity\Mission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'help' => "Le titre de la mission",
                'label' => 'Titre*',
                'attr' => array('class' => 'field-width'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'help' => "La description de la mission",
                'label' => 'Description*',
                'attr' => array('class' => 'field-width'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('nom_code', TextType::class, [
                'help' => "Le nom de code de la mission",
                'label' => 'Nom de code*',
                'attr' => array('class' => 'field-width'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('date_debut', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début*',
                'attr' => array('class' => 'field-width'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('date_fin', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin*',
                'attr' => array('class' => 'field-width'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                    new GreaterThan([
                        //'value' => $builder->get('date_debut'),
                        'propertyPath' => 'parent.all[date_debut].data',
                        'message' => 'La date de fin doit etre supérieure à la date de début'
                    ]),
                ]
            ])
            ->add('pays', EntityType::class, [
                'class' => Pays::class,
                'label' => 'Pays*',
                'attr' => array('class' => 'field-width'),
                'choice_label' => 'nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('type_mission', EntityType::class, [
                'class' => TypeMission::class,
                'label' => 'Type de mission*',
                'attr' => array('class' => 'field-width'),
                'choice_label' => 'nom', 
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('statut', EntityType::class, [
                'class' => Statut::class,
                'label' => 'Statut*',
                'attr' => array('class' => 'field-width'),
                'choice_label' => 'nom', 
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('specialite', EntityType::class, [
                'class' => Specialite::class,
                'label' => 'Spécialité*',
                'attr' => array('class' => 'field-width'),
                'choice_label' => 'nom', 
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
           /*  ->add('cibles', EntityType::class, [
                'label' => 'Cibles*',
                'placeholder' => 'Choisissez au moins une cible',
                'class' => Cible::class,
                'multiple' => true,
                'expanded' => false,
                'attr' => array('class' => 'field-width'),
                 'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
                //champ pour le choice_label automatiquement retournées par la méthode __tostring() de l'entity Cible
                //'choice_label' => 'nom', 
            ])
             ->add('agents', EntityType::class, [
                'label' => 'Agents*',
                'placeholder' => 'Choisissez au moins un agent',
                'class' => Agent::class,
                'multiple' => true,
                'expanded' => true,
                'attr' => array('class' => 'field-width'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ],
                //champ pour le choice_label automatiquement retournées par la méthode __tostring() de l'entity Agent
                'choice_label' => 'nom' 
            ])
            ->add('contacts', EntityType::class, [
                'label' => 'Contacts*',
                'placeholder' => 'Choisissez au moins un contact',
                'class' => Contact::class,
                'multiple' => true,
                'expanded' => false,
                'attr' => array('class' => 'field-width'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
                //champ pour le choice_label automatiquement retournées par la méthode __tostring() de l'entity Agent
                //'choice_label' => 'nom', 
            ]) */ 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }
}
