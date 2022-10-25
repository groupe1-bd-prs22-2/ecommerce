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

#[Route('/shop')]
class ShopController extends AbstractController
{
    /**
     * Chargement de la boutique.
     *
     * @param ProductRepository $productRepository
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    #[Route('/', name: 'app_shop', methods: ['GET'])]
    public function index(ProductRepository $productRepository,CategoryRepository $categoryRepository, Request $request): Response
    {


        $category = $request->query->get('categorie');


        if (!is_null($category)){
            $c = $categoryRepository->findOneBy(['slug'=> $category]);
            //dd($c);
            $product = $c->getProducts();

        }else{
            $product = $productRepository->findAll();
        }

        return $this->render('shop/index.html.twig', [

            'products' => $product,
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
    public function detail(Product $product, Request $request, Cart $cart): Response
    {
        // Chargement du formulaire d'ajout au panier
        $cartForm = $this->createForm(AddToCartType::class, $product);
        $cartForm->handleRequest($request);

        // Traitement du formulaire : ajout du produit et de la quantité voulue au panier
        if ($cartForm->isSubmitted() && $cartForm->isValid()) {
            $cart->addProduct($product, $cartForm->get('quantity')->getData());

            $this->addFlash('success', new TranslatableMessage('cart.product.added'));
            return $this->redirectToRoute('app_cart');
        }

        return $this->render('shop/detail.html.twig', [
            'product' => $product,
            'cartForm' => $cartForm->createView()
        ]);
    }

}
