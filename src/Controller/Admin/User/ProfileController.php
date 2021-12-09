<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use App\Form\Admin\User\ProfileType;
use App\Service\Uploader\UserAvatarUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user/profile', name: 'admin_user_profile_')]
class ProfileController extends AbstractController
{
    #[Route('', name: 'show', methods: ['GET'])]
    public function show(): Response
    {
        return $this->render('admin/user/profile/show.html.twig');
    }

    #[Route('/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, UserAvatarUploader $uploader): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($avatar = $form->get('avatar')->getData()) {
                $filename = $uploader->uploadImage($avatar);
                $user->setAvatar($filename);
            }
            if ($plainPassword = $form->get('plainPassword')->getData()) {
                $user->setPassword($hasher->hashPassword($user, $plainPassword));
            }
            $em->flush();

            $this->addFlash('success', 'Your changes have been saved!');

            return $this->redirectToRoute('admin_user_profile_show', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/profile/edit.html.twig', compact('user', 'form'));
    }
}
