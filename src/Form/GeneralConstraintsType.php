<?php

namespace App\Form;

use App\Entity\GeneralConstraints;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneralConstraintsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startHour')
            ->add('endHour')
            ->add('breakDuration')
            ->add('courseMaxDuration')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GeneralConstraints::class,
        ]);
    }
}
