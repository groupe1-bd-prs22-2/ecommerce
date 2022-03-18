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
     * @param Request $request
     * @param Cart $cart
     * @return Response
     */
    #[Route('/', name: 'app_cart', methods: ['GET', 'POST'])]
    public function index(Request $request, Cart $cart): Response
    {
        dd($cart->getProducts());
        return $this->render('cart/index.html.twig');
    }
}
