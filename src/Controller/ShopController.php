<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Form\AddToCartType;
use App\Entity\Product;
use App\Service\Cart;
use Exception;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/shop')]
class ShopController extends AbstractController
{
    /**
     * Chargement de la boutique.
     *
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: 'app_shop', methods: ['GET'])]
    public function index(ProductRepository $productRepository,CategoryRepository $categoryRepository, Request $request): Response
    {
        // Récupération des filtres des produits
        $parametres = [
            'prixMax'=> $request->query->get('prixMax'),
            'category' => $request->query->get('categorie'),
            'prixMin' =>  $request->query->get('prixMin'),
            'name' => $request->query->get('search')
        ];

        return $this->render('shop/index.html.twig', [
            'products' => $productRepository->searchProductsByFilters($parametres),
            'category' => $categoryRepository->findAll(),
        ]);
    }

    /**
     * Chargement de la fiche d'un produit.
     *
     * @param Product $product
     * @param Request $request
     * @param Cart $cart
     * @return Response
     * @throws Exception
     */
    #[Route('/product/{slug}', name: 'app_shop_detail')]
    public function detail(Product $product, Request $request, Cart $cart, ProductRepository $productRepository, TranslatorInterface $translator): Response
    {
        // Chargement du formulaire d'ajout au panier
        $cartForm = $this->createForm(AddToCartType::class, $product);
        $cartForm->handleRequest($request);

        // Traitement du formulaire : ajout du produit et de la quantité voulue au panier
        if ($cartForm->isSubmitted() && $cartForm->isValid()) {
            if ($product->getQuantity() < $cartForm->get('quantity')->getData()) {
                $this->addFlash('danger', $translator->trans('cart.product.qty_not_enough'));
            } else {
                $cart->addProduct($product, $cartForm->get('quantity')->getData());
                $this->addFlash('success', $translator->trans('cart.product.added'));
            }
            return $this->redirectToRoute('app_cart');
        }

        return $this->render('shop/detail.html.twig', [
            'product' => $product,
            'products'=>$productRepository->findProductByMax(4),
            'cartForm' => $cartForm->createView()
        ]);
    }
}
