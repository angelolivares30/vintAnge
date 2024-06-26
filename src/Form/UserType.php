<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Ingrese su correo electrónico']
        ])
        ->add('password', PasswordType::class, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Ingrese su contraseña']
        ])
        ->add('nombre', TextType::class, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Ingrese su nombre']
        ])
        ->add('apellidos', TextType::class, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Ingrese sus apellidos']
        ])
        ->add('telefono', TelType::class, [
            'attr' => ['class' => 'form-control', 'placeholder' => 'Ingrese sus apellidos']
        ])
        ->add('registrarse', SubmitType::class, [
            'attr' => ['class' => 'btn btn-custom btn-block']
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
