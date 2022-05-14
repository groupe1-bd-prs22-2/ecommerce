<?php

namespace App\Controller;

use Exception;
use App\Service\Cart;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Gestion du panier.
 * 
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
            'cart' => $cart->getProducts(),
            'total' => $cart->getTotal()
        ]);
    }

    /**
     * Ajout d'un produit au panier.
     *
     * @param Request $request
     * @param Cart $cart
     * @param ProductRepository $repository
     * @return Response
     * @throws Exception
     */
    #[Route('/', name: 'app_cart_add_product', methods: ['POST'])]
    public function add(Request $request, Cart $cart, ProductRepository $repository): Response
    {
        // Récupération du produit ajouté ainsi que sa quantité
        $product = $repository->find($request->get('product'));
        $quantity = (int) $request->get('quantity');

        if (!empty($product) && !empty($quantity)) {
            $cart->addProduct($product, $quantity);
        }

        return $this->redirectToRoute('app_cart');
    }

    /**
     * Suppression d'un produit du panier.
     *
     * @param Product $product
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
