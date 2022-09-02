<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/article')]
class ArticleController extends AbstractController
{
    /**
     * Chargement de la liste des articles.
     *
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    #[Route('/', name: 'admin_article_index', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('admin/article/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    /**
     * Création d'un nouveau article.
     *
     * @param Request $request
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    #[Route('/create', name: 'admin_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ArticleRepository $articleRepository): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Upload de l'image du article
            $picture = $form->get('picture')->getData();
            if ($picture) {
                // Upload du fichier sur serveur
                $uploader = new FileUploader($this->getParameter('article_pictures_directory'));
                $fileName = $uploader->upload($picture);

                // Set le nom du fichier
                $article->setPicture($fileName);
            }

            $articleRepository->add($article);
            return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }


    /**
     * Édition d'une fiche article.
     *
     * @param Request $request
     * @param Article $article
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    #[Route('/{id}/edit', name: 'admin_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mise à jour de l'image du article
            $picture = $form->get('picture')->getData();
            if ($picture) {
                // Upload du fichier sur serveur
                $uploader = new FileUploader($this->getParameter('article_pictures_directory'));
                $fileName = $uploader->upload($picture);

                // Set le nom du fichier
                $article->setPicture($fileName);
            }

            $articleRepository->add($article);
            return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    /**
     * Suppression d'une fiche article.
     *
     * @param Request $request
     * @param Article $article
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    #[Route('/{id}', name: 'admin_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, ArticleRepository $articleRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->request->get('_token'))) {
            $articleRepository->remove($article);
        }

        return $this->redirectToRoute('admin_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
