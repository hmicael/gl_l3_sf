<?php

namespace App\Form;

use App\Entity\Cours;
use App\Repository\GeneralConstraintsRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;


class CoursType extends AbstractType
{
    private $maxCourseDuration;
    
    public function __construct(GeneralConstraintsRepository $generalConstraintsRepository)
    {
        $this->maxCourseDuration = $generalConstraintsRepository->findOneBy([])->getCourseMaxDuration();
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Cours' => 'Cours',
                    'TD' => 'TD',
                    'TP' => 'TP',
                ],
            ])
            ->add('duration', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice',
                'hours' => range(1, $this->maxCourseDuration->format('H')),
                'minutes' => range(0, 45, 15),
                
            ])
            ->add('position', IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => $options['data']->getUE()->getNumberOfCourse(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cours::class,
        ]);
    }
}
