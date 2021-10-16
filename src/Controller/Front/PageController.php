<?php

namespace App\Controller\Front;

use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/page/{slug}', name: 'front_page', methods: ['GET'])]
    public function index(string $slug, PageRepository $page): Response
    {
        $page = $page->findOneBy(['slug' => $slug, 'isActive' => 1]);
        if (!$page) {
            throw new NotFoundHttpException();
        }

        return $this->render('front/page/index.html.twig', [
            'page' => $page,
        ]);
    }
}
