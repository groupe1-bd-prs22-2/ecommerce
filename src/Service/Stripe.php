<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

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
     * Constants
     */
    private const API_URL = 'https://api.stripe.com';
    private const API_PRIVATE_KEY = 'sk_test_51LIuofHEu3pjKYHbCBdF45ID5zKNJwfC0wRn53o37qYJKgBggXNKWOjTsGMlEx1ZRyXbrMTQjyDilJC1U9dtAllG00Y0or74pD';
    private const API_PUBLIC_KEY = 'pk_test_51LIuofHEu3pjKYHbbpcZvtJLcUvYFWBPHgODtdi9qn3nskuD2WCywmfIWACKLqLWA8h2jqew4H8D3mdWzRzFiMBr00P1INvBQD';

    private const CURRENCY = 'eur';

    /**
     * Constructeur
     */
    public function __construct(RequestStack $stack)
    {
        \Stripe\Stripe::setApiKey(self::API_PRIVATE_KEY);
    }

    /**
     * Création d'une intention de paiement.
     * @param float $amount Montant de la transaction
     * @return string
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createPaymentIntent(float $amount): string
    {
        $intent = \Stripe\PaymentIntent::create([
            'amount' => $amount * 100,
            'currency' => self::CURRENCY,
            'payment_method_types' => ['card']
        ]);

        return $intent->client_secret;
    }
}