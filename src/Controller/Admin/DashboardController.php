<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
     * @param EntityManagerInterface $em
     * @return Response
     */
    #[Route('/', name: 'admin_dashboard_index')]
    public function index(EntityManagerInterface $em): Response
    {
        // Récupération des différentes informations
        $orders = $em->getRepository(Order::class)->findBy(['status' => [
            Order::STATUS_SHIPPED, Order::STATUS_PREPARATION, Order::STATUS_DELIVERED]
        ]); // Total des ventes

        // Récupération du chiffre d'affaires
        $earnigs = 0;
        foreach ($orders as $order) {
            foreach ($order->getProducts() as $purchase) {
                $earnigs += $purchase->getProduct()->getPrice() * $purchase->getQuantity();
            }
        }

        return $this->render('admin/dashboard/index.html.twig', [
            'controller_name' => 'Tableau de bord',
            'sales' => count($orders),
            'earnings' => $earnigs,
            'orders' => $em->getRepository(Order::class)->count([]),
            'users' => $em->getRepository(User::class)->count([])
        ]);
    }
}
