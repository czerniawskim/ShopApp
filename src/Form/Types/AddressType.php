<?php

namespace Form\Types;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("street", TextType::class, [
                'label' => 'Street',
            ])
            ->add("city", TextType::class, [
                'label' => 'City',
            ])
            ->add("zip", TextType::class, [
                'label' => 'Zip code',
            ]);
    }
}
