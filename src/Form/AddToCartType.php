<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class AddToCartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Product $entity */
        $entity = $builder->getData();

        $builder
            ->add('product', HiddenType::class, [
                'mapped' => false,
                'data' => $entity->getId()
            ])
            ->add('quantity', NumberType::class, [
                'mapped' => false,
                'label' => false,
                'scale' => 0,
                'attr' => [
                    'max' => $entity->getQuantity(),
                    'value' => 0
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => new TranslatableMessage('cart.addToCart'),
                'attr' => [
                    'class' => 'primary-btn'
                ]
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
