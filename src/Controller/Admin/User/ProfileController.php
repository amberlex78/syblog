<?php

namespace App\Controller\Admin\User;

use App\Entity\User;
use App\Form\Admin\User\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user/profile')]
class ProfileController extends AbstractController
{
    #[Route('', name: 'admin_user_profile_show', methods: ['GET'])]
    public function show(): Response
    {
        return $this->render('admin/user/profile/show.html.twig');
    }

    #[Route('/edit', name: 'admin_user_profile_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $encodedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($encodedPassword);
            }
            $em->flush();

            $this->addFlash('success', 'Your changes have been saved!');

            return $this->redirectToRoute('admin_user_profile_show', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/user/profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
