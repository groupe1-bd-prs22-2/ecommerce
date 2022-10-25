<?php

namespace App\Controller;

use App\Entity\Pages;
use App\Service\Cart;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NavbarController extends AbstractController
{
    /**
     * Rendu de la navbar.
     *
     * @param EntityManagerInterface $em
     * @param string $route
     * @param Cart $cart
     * @return Response
     */
    public function renderNav(EntityManagerInterface $em, string $route, Cart $cart): Response
    {
        return $this->render('layouts/navbar.html.twig', [
            'pages' => $em->getRepository(Pages::class)->findAll(),
            'route' => $route,
            'cartAmount' => $cart->getTotal()
        ]);
    }
}
