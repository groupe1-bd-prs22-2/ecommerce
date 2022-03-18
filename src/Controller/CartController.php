<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Product;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * Gestion du panier.
 */
#[Route('/cart')]
class CartController extends AbstractController
{
    /**
     * Ajout d'un produit au panier.
     *
     * @param Product $product
     * @return Response
     */
    public function addToCart(Product $product): Response
    {
        // Création du formulaire
        $form = $this->createFormBuilder()
            ->add('product', HiddenType::class, ['data' => $product->getId()])
            ->add('quantity', NumberType::class, [
                'label' => false,
                'scale' => 0,
                'attr' => [
                    'max' => $product->getQuantity(),
                    'value' => 0
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => new TranslatableMessage('cart.addToCart'),
                'attr' => [
                    'class' => 'primary-btn'
                ]
            ])
            ->getForm()
        ;

        // todo: récupération de la quantité voulue et le produit concerné
        // todo: vérifier la disponibilité deu produit
        // todo: calcul du sous-total pour chaque produit ajouté

        return $this->render('cart/_add_to_cart_form.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
