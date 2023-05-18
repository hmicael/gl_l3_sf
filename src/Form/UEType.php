<?php

namespace App\Form;

use App\Entity\UE;
use App\Service\FiliereGetter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;


class UEType extends AbstractType
{
    private FiliereGetter $filiereGetter;

    public function __construct(FiliereGetter $filiereGetter)
    {
        $this->filiereGetter = $filiereGetter;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $filieres = $this->filiereGetter->getGroups();
        // ici on transforme le tableau de filières en tableau associatif
        // de la forme ['description' => 'cn']
        $choices = array_combine(
            array_column($filieres, 'description'),
            array_column($filieres, 'cn')
        );

            
        $builder
            ->add('name')
            ->add('startDate', DateType::class, ['label' => 'Start date'])
            ->add('nbStudent', TextType::class, ['label' => 'Number of students'])
            ->add('nbGroup', TextType::class, ['label' => 'Number of groups'])
            ->add('nbCours', TextType::class, ['label' => 'Number of courses'])
            ->add('nbTD', TextType::class, ['label' => 'Number of TD'])
            ->add('nbTP', TextType::class, ['label' => 'Number of TP'])
            ->add('semester', ChoiceType::class, [
                'choices'  => [
                    '1' => '1',
                    '2' => '2',
                ],
            ])
            ->add('filieres', ChoiceType::class, [
                'choices' => $choices,
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UE::class,
        ]);
    }
}
