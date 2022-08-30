<?php

namespace App\Controller;

use App\Service\Stripe;
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
     * @param Stripe $stripe
     * @param Request $request
     * @return Response
     * @throws \Stripe\Exception\ApiErrorException
     */
    #[Route('/', name: 'app_cart', methods: ['GET', 'POST'])]
    public function index(Cart $cart, Stripe $stripe, Request $request): Response
    {
        // Création de l'intention de paiement au clic sur "Paiement"
        if ($request->getMethod() === 'POST') {
            $clientSecret = $stripe->createPaymentIntent($cart->getTotal());
            $cart->setClientSecret($clientSecret);

            // Redirection vers la page de paiement
            return $this->redirectToRoute('app_cart_payment');
        }

        // Chargement de la page récapitulative en méthode GET
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
    #[Route('/add', name: 'app_cart_add_product', methods: ['POST'])]
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

    /**
     * Paiement du panier.
     *
     * @param Cart $cart
     * @return Response
     */
    #[Route('/payment', name: 'app_cart_payment', methods: ['GET', 'POST'])]
    public function payment(Cart $cart): Response
    {
        // Redirection vers le panier si aucun "client_secret" Stripe trouvé
        if (is_null($cart->getClientSecret())) {
            return $this->redirectToRoute('app_cart');
        }

        return $this->render('cart/payment.html.twig', [
            'stripe_client_secret' => $cart->getClientSecret(),
            'cart' => $cart->getProducts(),
            'total' => $cart->getTotal()
        ]);
    }

    /**
     * Confirmation et enregistrement du paiement.
     *
     * @param Cart $cart
     * @param Request $request
     * @return Response
     */
    #[Route('/payment-confirm', name: 'app_cart_payment_confirm', methods: ['GET'])]
    public function paymentConfirm(Cart $cart, Request $request): Response
    {
        $routeParams = $request->attributes->get('_route_params');

        // TODO: Enregistrer la commande en base de données

        // TODO: Envoi du mail récapitulatif de l'achat

        // TODO: Mise à jour du stock du (des) produit(s) acheté(s)

        // Vide le panier
        $cart->emptyCart();

        // Redirection vers la page d'accueil
        return $this->redirectToRoute('app_home');
    }
}
