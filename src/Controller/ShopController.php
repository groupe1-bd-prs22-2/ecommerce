<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class ShopController extends AbstractController
{
    #[Route('/shop', name: 'app_shop')]
    public function index(ProductRepository $productRepository,CategoryRepository $categoryRepository): Response
    {
        return $this->render('shop/index.html.twig', [
            'products' => $productRepository->findAll(),
            'category' =>$categoryRepository->findAll(),
        ]);
    }
}
