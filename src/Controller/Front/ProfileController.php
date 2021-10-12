<?php

namespace App\Controller\Front;

use App\Form\Front\ProfileEditFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('', name: 'front_profile', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('front/profile/index.html.twig', [
            'controller_name' => 'ProfileController',
        ]);
    }

    #[Route('/edit', name: 'front_profile_edit', methods: ['GET','POST'])]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileEditFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Your changes have been saved!');
            return $this->redirectToRoute('front_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('front/profile/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
