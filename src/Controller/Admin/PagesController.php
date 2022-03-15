<?php

namespace App\Controller\Admin;

use App\Entity\Pages;
use App\Form\PagesType;
use App\Repository\PagesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/pages')]
class PagesController extends AbstractController
{
    /**
     * Chargement de la liste des pages créées.
     *
     * @param PagesRepository $pagesRepository
     * @return Response
     */
    #[Route('/', name: 'admin_pages_index', methods: ['GET'])]
    public function index(PagesRepository $pagesRepository): Response
    {
        return $this->render('admin/pages/index.html.twig', [
            'pages' => $pagesRepository->findAll(),
        ]);
    }

    /**
     * Création d'une nouvelle page.
     *
     * @param Request $request
     * @param PagesRepository $pagesRepository
     * @return Response
     */
    #[Route('/new', name: 'admin_pages_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PagesRepository $pagesRepository): Response
    {
        $page = new Pages();
        $form = $this->createForm(PagesType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pagesRepository->add($page);
            return $this->redirectToRoute('admin_pages_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/pages/new.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }

    /**
     * Visualisation d'une page.
     *
     * @param Pages $page
     * @return Response
     */
    #[Route('/{id}', name: 'admin_pages_show', methods: ['GET'])]
    public function show(Pages $page): Response
    {
        return $this->render('admin/pages/show.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * Édition d'une page.
     *
     * @param Request $request
     * @param Pages $page
     * @param PagesRepository $pagesRepository
     * @return Response
     */
    #[Route('/{id}/edit', name: 'admin_pages_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Pages $page, PagesRepository $pagesRepository): Response
    {
        $form = $this->createForm(PagesType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pagesRepository->add($page);
            return $this->redirectToRoute('admin_pages_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/pages/edit.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }

    /**
     * Suppression d'une page.
     *
     * @param Request $request
     * @param Pages $page
     * @param PagesRepository $pagesRepository
     * @return Response
     */
    #[Route('/{id}', name: 'admin_pages_delete', methods: ['POST'])]
    public function delete(Request $request, Pages $page, PagesRepository $pagesRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$page->getId(), $request->request->get('_token'))) {
            $pagesRepository->remove($page);
        }

        return $this->redirectToRoute('admin_pages_index', [], Response::HTTP_SEE_OTHER);
    }
}
