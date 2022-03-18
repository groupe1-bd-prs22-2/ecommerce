<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository,Request $request): Response
    {

        //dump ($request->attributes->get('_route'));
        return $this->render('home/index.html.twig', [
            'products' => $productRepository->findAll(),

        ]);
    }
}
