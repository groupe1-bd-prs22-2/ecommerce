<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// TODO: Bloquer le controller uniquement aux administrateurs
#[Route('/admin/dashboard')]
class DashboardController extends AbstractController
{
    /**
     * Chargement du tableau de bord.
     *
     * @return Response
     */
    #[Route('/', name: 'admin_dashboard_index')]
    public function index(): Response
    {
        return $this->render('admin/dashboard/index.html.twig', [
            'controller_name' => 'Tableau de bord',
        ]);
    }
}
