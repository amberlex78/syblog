<?php

namespace App\Controller\Admin;

use App\Entity\StaticStorage\UserRolesStorage;
use App\Entity\User;
use App\Form\Admin\UserType;
use App\Repository\UserRepository;
use App\Service\Uploader\UserAvatarUploader;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user', name: 'admin_user_')]
class UserController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $users = $paginator->paginate(
            $userRepository->findAllOrderedByNewest(),
            $request->query->getInt('page', 1)
        );

        return $this->render('admin/user/index.html.twig', compact('users'));
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($plainPassword = $form->get('plainPassword')->getData()) {
                $user->setPassword($hasher->hashPassword($user, $plainPassword));
            }
            if (!$this->isGranted(UserRolesStorage::ROLE_SUPER_ADMIN)) {
                $user->setRoles([UserRolesStorage::ROLE_USER]);
            }

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'User created!');

            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/new.html.twig', compact('user', 'form'));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/user/show.html.twig', compact('user'));
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(EntityManagerInterface $em, Request $request, User $user, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($plainPassword = $form->get('plainPassword')->getData()) {
                $user->setPassword($hasher->hashPassword($user, $plainPassword));
            }

            $em->flush();

            $this->addFlash('success', 'Changes saved!');

            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/edit.html.twig', compact('user', 'form'));
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $em, Request $request, User $user): Response
    {
        if ($user->getPosts()->count()) {
            $this->addFlash('warning', 'The user has posts!');

            return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();

            $this->addFlash('success', 'User deleted!');
        } else {
            $this->addFlash('warning', 'Something went wrong! Try again.');
        }

        return $this->redirectToRoute('admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/avatar/{id}', name: 'delete_avatar', methods: ['DELETE'])]
    public function deleteImage(EntityManagerInterface $em, UserAvatarUploader $uploader, Request $request, User $user): JsonResponse
    {
        if ($request->isXmlHttpRequest()
            && $this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))
        ) {
            $uploader->removeImage($user->getAvatar());
            $user->setAvatar(null);
            $em->flush();

            return $this->json(['success' => true]);
        } else {
            return $this->json(['error' => 'Bad Request!'], 400);
        }
    }
}
