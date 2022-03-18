<?php

namespace App\Controller;

use App\Entity\Pages;
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
     * @return Response
     */
    public function renderNav(EntityManagerInterface $em, $route =''): Response
    {

        //dump ($request->attributes->get('_route'));
        return $this->render('layouts/navbar.html.twig', [
            'pages' => $em->getRepository(Pages::class)->findAll(),
            'route' => $route,
        ]);
    }
}
