<?php

namespace App\Form;

use App\Entity\Opinions;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Rate', ChoiceType::class,[
                'expanded'=>true,
                'multiple'=>false,
                'choices'=>[
                    1,
                    2,
                    3,
                    4,
                    5
                ]
            ])
            ->add('Description', TextareaType::class, ['attr'=>['placeholder'=>'Opinion content']])
            ->add('Add', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Opinions::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'rate';
    }
}
