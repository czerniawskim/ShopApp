<?php

namespace App\Form;

use App\Entity\User;
use Form\Types\AddressType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Username',
                'required' => true,
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
                'required' => true,
            ])
            ->add('email', TextType::class, [
                'label' => 'E-mail',
                'required' => true,
            ])
            ->add('address', AddressType::class, [
                'required' => false,
            ])
            ->add("submit", SubmitType::class, [
                'label' => 'Join in',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return 'register';
    }
}
