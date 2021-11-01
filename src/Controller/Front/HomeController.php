<?php

namespace App\Controller\Front;

use App\Repository\Blog\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('', name: 'front_home', methods: ['GET'])]
    public function index(Request $request, PostRepository $postRepository, PaginatorInterface $paginator): Response
    {
        $posts = $paginator->paginate(
            $postRepository->findAllActive(),
            $request->query->getInt('page', 1)
        );

        return $this->render('front/home/index.html.twig', [
            'posts' => $posts,
        ]);
    }
}
