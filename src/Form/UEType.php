<?php

namespace App\Form;

use App\Entity\UE;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UEType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('startDate', TextType::class, ['label' => 'Start date'])
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
            ->add('filiere')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UE::class,
        ]);
    }
}
