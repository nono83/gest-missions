<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Agent;
use App\Entity\Pays;
use App\Entity\Specialite;
use App\Entity\Mission;
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
                'attr' => array('class' => 'field-width'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'help' => "Le prénom de l'agent",
                'label' => 'Prénom*',
                'attr' => array('class' => 'field-width'),
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
                'attr' => array('class' => 'field-width'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('code_identification', TextType::class, [
                'help' => "Le code d'identification de l'agent",
                'label' => 'Code d\'identification*',
                'attr' => array('class' => 'field-width'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('nationalite', EntityType::class, [
                'label' => 'Nationalité*',
                'attr' => array('class' => 'field-width'),
                'placeholder' => 'Choisissez une option',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ],
                'class' => Pays::class,
                'choice_label' => 'nom', 
            ])
            ->add('specialites', EntityType::class, [
                'label' => 'Spécialités*',
                'attr' => array('class' => 'field-width'),
                'placeholder' => 'Choisissez une option',
                'class' => Specialite::class,
                'multiple' => true,
                'expanded' => true,
                'choice_label' => 'nom', 
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('missions', EntityType::class, [
                'class' => Mission::class,
                'placeholder' => "Sélectionnez une mission",
                'label' => 'Missions*',
                'multiple' => true,
                'expanded' => false,
                'choice_label' => 'titre', 
                'attr' => array('class' => 'field-width')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Agent::class,
            'constraints' => [
               new Callback([$this, 'validate']),
           ], 
        ]);
    }

     /**
     * L'agent ne peut pas avoir la même nationalité que l'uns des cibles de la mission
     */
    public function validate( $data, ExecutionContextInterface $context): void
    {
        $nationalite=$data->getNationalite()->getNom();
        $missions=$data->getMissions();
        foreach($missions as $mission){
            $mission_name=$mission->getTitre();
            $cibles=$mission->getCibles()->toArray();
            foreach($cibles as $cible){
                if ($nationalite==$cible->getNationalite()->getNom()) {
                    $context->buildViolation(sprintf('L\'agent ne peut pas avoir la même nationalité que la cible %s de la mission %s',$cible,$mission_name))
                        ->atPath('nationalite')
                        ->addViolation();
                }
            }
        }
    }
}
