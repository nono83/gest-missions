<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Planque;
use App\Entity\TypePlanque;
use App\Entity\Mission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanqueType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code', TextType::class, [
                'help' => "Le nom de code de la planque",
                'label' => 'Nom du code*',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('adresse', TextType::class, [
                'help' => "Adresse de la planque",
                'label' => 'Adresse*',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('type_planque', EntityType::class, [
                'class' => TypePlanque::class,
                'choice_label' => 'nom', 
            ])
            ->add('mission', EntityType::class, [
                'class' => Mission::class,
                'choice_label' => 'titre', 
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Planque::class,
        ]);
    }
}
