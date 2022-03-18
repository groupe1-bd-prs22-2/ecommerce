<?php

namespace App\Controller;

use App\Service\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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
     * Chargement du panier
     *
     * @param Cart $cart
     * @return Response
     */
    #[Route('/', name: 'app_cart', methods: ['GET'])]
    public function index(Cart $cart): Response
    {
        return $this->render('cart/index.html.twig', [
            'cart' => $cart->getProducts()
        ]);
    }

    /**
     * Suppression d'un produit du panier.
     *
     * @param Product $produit
     * @param Request $request
     * @param Cart $cart
     * @return Response
     */
    #[Route('/{id}/remove', name: 'app_cart_remove_product', methods: ['POST'])]
    public function delete(Product $product, Request $request, Cart $cart): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $products = $cart->getProducts();
            if (!empty($products)) {
                // Recherche des éléments à supprimer
                foreach ($products as $element) {
                    /** @var Product $productToRemove */
                    $productToRemove = $element['product'];

                    if ($productToRemove->getId() === $product->getId()) {
                        $cart->removeProduct($product);
                    }
                }

                $this->addFlash('success', new TranslatableMessage('cart.productRemoved'));
            }
        }

        return $this->redirectToRoute('app_cart');
    }
}
