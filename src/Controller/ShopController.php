<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;


#[Route('/shop')]

class ShopController extends AbstractController
{
    #[Route('/', name: 'app_shop', methods: ['GET'])]
    public function index(ProductRepository $productRepository,CategoryRepository $categoryRepository): Response
    {
        return $this->render('shop/index.html.twig', [
            'products' => $productRepository->findAll(),
            'category' => $categoryRepository->findAll(),
        ]);
    }

        #[Route('/detail/{slug}', name: 'app_shop_detail')]
    public function detail(Product $product): Response
    {
        return $this->render('shop/detail.html.twig', [
            'product' => $product,
        ]);
    }

}
