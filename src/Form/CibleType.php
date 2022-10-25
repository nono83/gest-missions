<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Pays;
use App\Entity\Mission;
use App\Entity\Cible;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CibleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'help' => "Le nom du cible",
                'label' => 'Nom*',
                'attr' => array('class' => 'field-widh'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'help' => "Le prénom du cible",
                'label' => 'Prénom',
                'attr' => array('class' => 'field-width')
            ])
            ->add('date_naissance', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de naissance*',
                'attr' => array('class' => 'field-width'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('nom_code', TextType::class, [
                'help' => "Le nom de code du cible",
                'label' => 'Nom de code*',
                'attr' => array('class' => 'field-width'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('nationalite', EntityType::class, [
                'class' => Pays::class,
                'placeholder' => "Sélectionnez un pays",
                'label' => 'Nationalité*',
                'choice_label' => 'nom', 
                'attr' => array('class' => 'field-width'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('mission', EntityType::class, [
                'class' => Mission::class,
                'placeholder' => "Sélectionnez une mission",
                'label' => 'Mission',
                'choice_label' => 'titre', 
                'attr' => array('class' => 'field-width')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cible::class,
            'constraints' => [
               new Callback([$this, 'validate']),
           ], 
        ]);
    }

     /**
     * La cible ne peut pas avoir la même nationalité que l'un des agents de la mission
     */
    public function validate( $data, ExecutionContextInterface $context): void
    {
        $nationalite=$data->getNationalite()->getNom();
        $mission=$data->getMission();
        $mission_name=$mission->getTitre();
        $agents=$mission->getAgents()->toArray();
        foreach($agents as $agent){
            if ($nationalite==$agent->getNationalite()->getNom()) {
                $context->buildViolation(sprintf('La cible ne peut pas avoir la même nationalité que l\'agent %s de la mission %s',$agent,$mission_name))
                    ->atPath('nationalite')
                    ->addViolation();
            }
        }
    }

   
}
