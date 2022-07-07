<?php

namespace App\Service;

/**
 * @class Stripe
 * @link https://stripe.com/docs/api
 *
 * Service de gestion des paiements avec l'API Stripe.
 * NOTE : Tous les paiements sont réalisés avec l'environnement de test Stripe.
 */
class Stripe
{
    /**
     * Private Attributes
     */
    private \Stripe\Stripe $stripe;

    /**
     * Constants
     */
    private const API_PRIVATE_KEY = 'sk_test_51LIuofHEu3pjKYHbCBdF45ID5zKNJwfC0wRn53o37qYJKgBggXNKWOjTsGMlEx1ZRyXbrMTQjyDilJC1U9dtAllG00Y0or74pD';

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->stripe = new \Stripe\Stripe(self::API_PRIVATE_KEY);
    }

    /**
     * Enregistrement d'un paiement.
     * @return void
     */
    public function pay() {}
}