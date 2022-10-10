<?php

namespace App\Controller\Admin;

use App\Repository\OrderRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * Chargement de la liste des commandes effectuées.
     * @param OrderRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/admin/order', name: 'admin_order_index')]
    public function index(OrderRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        // Chargement de la liste des commandes passées
        $page = $request->query->get('page', 1); // Récupération de la page de résultat à afficher
        $orders = $paginator->paginate(
            $repository->getPaginator(), $page, 10
        );

        return $this->render('admin/order/index.html.twig', [
            'pagination' => $orders
        ]);
    }
}
