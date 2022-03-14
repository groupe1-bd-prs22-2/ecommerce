<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('category')
            ->add('picture', FileType::class, [
                'mapped' => false,
                'constraints' => [
                    new File(
                        maxSize: '1024k', mimeTypes: ['image/*'],
                        maxSizeMessage: "L'image ne doit pas faire plus de 1Mo",
                        mimeTypesMessage: "Veuillez uploader une image"
                    )
                ]
            ])
            ->add('price', NumberType::class, [
                'scale' => 2
            ])
            ->add('quantity', NumberType::class, [
                'scale' => 0
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
