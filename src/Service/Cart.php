<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\Product;
use Exception;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * Gestion du panier de l'utilisateur.
 *
 */
class Cart
{
    /**
     * Private attributes.
     */
    /** @var SessionInterface $session Interface permettant d'interagir avec les données de session */
    private SessionInterface $session;

    /** @var ?string "Client_secret" Stripe permettant le paiement du panier */
    private ?string $clientSecret = null;

    /**
     * Constructeur.
     */
    public function __construct(RequestStack $stack)
    {
        $this->session = $stack->getSession();
    }

    /**
     * Récupère la liste des produits du panier en cours.
     *
     * @return array
     */
    public function getProducts(): array
    {
        return $this->session->get('products', []);
    }

    /**
     * Ajout d'un produit au panier.
     *
     * @param Product $product
     * @param int $quantity
     * @return void
     * @throws Exception
     */
    public function addProduct(Product $product, int $quantity)
    {
        // Vérification de la quantité ajoutée
        if ($quantity <= 0) {
            throw new Exception(new TranslatableMessage('cart.emptyQty'));
        } else {
            // Récupération de la liste des produits du panier
            $products = $this->getProducts();

            // Recherche du produit que l'on veut ajouter
            $isAlreadyInCart = false;
            if (!empty($products)) {
                foreach ($products as &$elem) {
                    if ($elem['product']->getId() === $product->getId()) {
                        // Si le produit est déjà dans le panier, on incrémente uniquement la quantité de ce dernier
                        $isAlreadyInCart = true;
                        $elem['quantity'] += $quantity;

                        break;
                    }
                }
            }

            // Si non trouvé dans le panier, on crée la ligne
            if (!$isAlreadyInCart) {
                $products[] = [
                    'product' => $product,
                    'quantity' => $quantity
                ];
            }

            // Mise à jour du panier
            $this->session->set('products', $products);
        }
    }

    /**
     * Suppression d'un produit du panier.
     *
     * @param Product $product
     * @return void
     */
    public function removeProduct(Product $product)
    {
        $products = $this->getProducts();
        if (!empty($products)) {
            foreach ($products as $k => $elem) {
                if ($elem['product']->getId() === $product->getId()) {
                    unset($products[$k]);
                    $this->session->set('products', $products);
                }
            }
        }
    }

    /**
     * Vide le panier en cours.
     * @return void
     */
    public function emptyCart()
    {
        $this->session->set('products', []);
    }

    /**
     * Calcul du total du panier.
     *
     * @return float
     */
    public function getTotal(): float
    {
        // Initialisation
        $products = $this->getProducts();
        $total = 0.00;

        // Cumul des sous-totaux de chaque élément du panier
        if (!empty($products)) {
            foreach ($products as $elem) {
                /** @var Product $product */
                $product = $elem['product'];

                // Calcul du sous-total et ajout au total
                $total += $product->getPrice() * $elem['quantity'];
            }
        }

        return $total;
    }

    /**
     * Set le "client_secret" Stripe pour l'intention de paiement du paiement du panier.
     * @param string $secret
     * @return void
     */
    public function setClientSecret(string $secret): void
    {
        $this->session->set('stripe_client_secret', $secret);
    }

    /**
     * @return string "Client_secret" pour procéder au paiement.
     */
    public function getClientSecret(): string
    {
        return $this->session->get('stripe_client_secret');
    }
}