<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Pages;

class PageController extends AbstractController
{
    /**
     * Chargement d'une page créée en base de données.
     *
     * @param Pages $page
     * @return Response
     */
    #[Route('/page/{slug}', name: 'app_page_show')]
    public function viewPage(Pages $page): Response
    {
        return $this->render('page/view.html.twig', [
            'page' => $page,
        ]);
    }
}
