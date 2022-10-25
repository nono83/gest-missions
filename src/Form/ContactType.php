<?php

namespace App\Form;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
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
                'attr' => array('class' => 'field-width'),
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ])
                ]
            ])
            ->add('prenom', TextType::class, [
                'help' => "Le prénom du contact",
                'attr' => array('class' => 'field-width'),
                'label' => 'Prénom'
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
                'help' => "Le nom de code du contact",
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
                'choice_label' => 'nom',
                'label' => 'Nationalité*',
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
                'choice_label' => 'titre', 
                'attr' => array('class' => 'field-width')
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
            'constraints' => [
               new Callback([$this, 'validate']),
           ], 
        ]);
    }

     /**
     * Le contact doit avoir la même nationalité du pays de la mission
     */
    public function validate( $data, ExecutionContextInterface $context): void
    {
        $nationalite=$data->getNationalite()->getNom();
        $mission=$data->getMission();

        if ($nationalite!=$mission->getPays()->getNom()) {
            $context->buildViolation('Le contact doit avoir la même nationalité du pays de la mission' )
                ->atPath('nationalite')
                ->addViolation();
        }

    }
}
