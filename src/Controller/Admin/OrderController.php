<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/order')]
class OrderController extends AbstractController
{
    /**
     * Chargement de la liste des commandes effectuées.
     * @param OrderRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: 'admin_order_index')]
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

    /**
     * Changement du statut d'une commande.
     * @param Request $request
     * @param Order $order
     * @param OrderRepository $repository
     * @return Response
     */
    #[Route('{id}/edit-status', name: 'admin_order_edit_status', methods: ['POST'])]
    public function editStatus(Request $request, Order $order, OrderRepository $repository): Response
    {
        // Vérification du token
        if ($this->isCsrfTokenValid('edit-status-'.$order->getId(), $request->request->get('_token'))) {
            // Mise à jour du statut
            $newStatus = $request->request->get('status');

            if (in_array($newStatus, Order::STATUSES)) {
                $order->setStatus($newStatus);
                $repository->add($order);
            }
        }

        // Redirection vers la liste des commandes
        return $this->redirectToRoute('admin_order_index');
    }
}
