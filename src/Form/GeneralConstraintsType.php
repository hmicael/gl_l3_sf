<?php

namespace App\Form;

use App\Entity\GeneralConstraints;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class GeneralConstraintsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startHour', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
                'hours' => range(8, 18),
                'minutes' => range(0, 45, 15),
            ])
            ->add('endHour', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
                'hours' => range(7, 18),
                'minutes' => range(0, 45, 15),
            ])
            ->add('breakDuration', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
                'hours' => range(0, 2),
                'minutes' => range(0, 45, 15),
            ])
            ->add('courseMaxDuration', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
                'hours' => range(1, 3),
                'minutes' => range(0, 45, 15),
            ])
            ->add('courseMinDuration', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
                'hours' => [1],
                'minutes' => range(0, 45, 15),
            ])
            ->add('semesterOneStart', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('semesterOneEnd', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('semesterTwoStart', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('semesterTwoEnd', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GeneralConstraints::class,
        ]);
    }
}
