<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/blog')]
class ArticleController extends AbstractController
{
    #[Route('/', name: 'app_blog', methods: ['GET'])]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('blog/index.html.twig', [
            'articles' => $articleRepository->findAll(),
        ]);
    }

    #[Route('/{slug}', name: 'app_article_detail')]
    public function show(Article $article): Response
    {
        return $this->render('blog/detail.html.twig', [
            'article' => $article,
        ]);
    }


}
