<?php

namespace App\Form;

use App\Entity\Producto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Controller\CategoriaController;
use App\Entity\Categoria;
use App\Repository\CategoriaRepository;
use Symfony\Component\Validator\Constraints\File;

class ProductoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class)
            ->add('descripcion', TextType::class)
            ->add('precio', NumberType::class)
            ->add('categoria', ChoiceType::class, [
                'choices' => $options['categorias'],
                'placeholder' => 'Selecciona una categoria'
            ])
            ->add('foto', FileType::class, [
                'label' => 'Image (JPEG file)',
                'mapped' => false, // Esto es importante si el campo no está mapeado a una propiedad de la entidad
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image file',
                    ])
                ],
            ])
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Producto::class,
            'categorias' => []
        ]);
        $resolver->setAllowedTypes('categorias', 'array');
    }


}
