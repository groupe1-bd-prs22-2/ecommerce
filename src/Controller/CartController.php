<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderProduct;
use App\Entity\User;
use App\Service\Stripe;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use App\Service\Cart;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Stripe\Exception\ApiErrorException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
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
     * @throws ApiErrorException
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
            if ($product->getQuantity() < $quantity) {
                $this->addFlash('danger', new TranslatableMessage('cart.product.qty_not_enough'));
            } else {
                $cart->addProduct($product, $quantity);
                $this->addFlash('success', new TranslatableMessage('cart.product.added'));
            }
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
     * @param MailerInterface $mailer
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     * @throws TransportExceptionInterface
     */
    #[Route('/payment-confirm', name: 'app_cart_payment_confirm', methods: ['GET'])]
    public function paymentConfirm(
        Cart $cart, MailerInterface $mailer,
        Request $request, EntityManagerInterface $em
    ): Response
    {
        // Vérification que l'utilisateur soit bien authentifié
        if (!$this->isGranted('ROLE_USER')) {
            $this->addFlash('error', new TranslatableMessage('errors.payment.must_be_auth'));
            return $this->redirectToRoute('app_cart');
        }

        /** @var User $user */
        $user = $this->getUser(); // Récupération de l'utilisateur courant

        // Enregistrement de la commande en base de données
        $order = new Order();
        $order->setCustomer($user);
        $em->getRepository(Order::class)->add($order);

        // Création de la référence de la commande
        $order->setReference(md5($order->getId()));
        $em->getRepository(Order::class)->add($order);

        foreach ($cart->getProducts() as $element) {
            // Récupération de l'entité du produit
            $product = $em->getRepository(Product::class)->find($element['product']->getId());

            // Association des produits achetés à la commande
            $orderProduct = (new OrderProduct())
                ->setPurchase($order)
                ->setProduct($product)
                ->setQuantity($element['quantity'])
            ;
            $em->getRepository(OrderProduct::class)->add($orderProduct);

            // Mise à jour du stock du (des) produit(s) acheté(s)
            $newQty = $product->getQuantity() - $element['quantity'];
            $product->setQuantity($newQty);
            $em->getRepository(Product::class)->add($product);
        }

        // Génération du mail récapitulatif de l'achat
        $email = (new TemplatedEmail())
            ->to(new Address($user->getEmail(), $user->getFullName()))
            ->from('noreply@mangamania.com')
            ->subject('Merci pour votre achat')
            ->embedFromPath(__DIR__ . '/../../public/img/logo.png', 'logo', 'image/png')
            ->htmlTemplate('mails/order/purchase.html.twig')
            ->context([
                'cart' => $cart->getProducts(),
                'user' => $user,
                'order' => $order,
                'total' => $cart->getTotal()
            ])
        ;
        $mailer->send($email); // Envoi du mail

        // Vide le panier
        $cart->emptyCart();

        // Redirection vers la page d'accueil
        return $this->redirectToRoute('app_home');
    }
}
