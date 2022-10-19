<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatableMessage;

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
            $earnigs += $order->getTotalAmount();
        }

        return $this->render('admin/dashboard/index.html.twig', [
            'controller_name' => 'Tableau de bord',
            'sales' => count($orders),
            'earnings' => $earnigs,
            'orders' => $em->getRepository(Order::class)->count([]),
            'users' => $em->getRepository(User::class)->count([])
        ]);
    }

    /**
     * Récupération des informations du graphique du chiffre d'affaires.
     */
    #[Route('/ajax/earnings', name: 'admin_dashboard_ajax_earnings', methods: ['POST'])]
    public function ajaxGetEarnings(OrderRepository $repository): JsonResponse
    {
        // Initialisation de la réponse JSON
        $data = [
            'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            'earnings' => []
        ];

        // Initialisation des valeurs par défaut
        $earnings = [];
        foreach ($data['labels'] as $label)
            $earnings[$label] = 0;

        // Récupération du chiffre d'affaires mensuel.
        $orders = $repository->getOrdersFromYear(date('Y'));
        foreach ($orders as $order) {
            $month = $order->getCreatedAt()->format('M');
            $earnings[$month] += $order->getTotalAmount();
        }

        foreach ($earnings as $month => $earning)
            $data['earnings'][] = $earning;

        return $this->json($data);
    }
}
