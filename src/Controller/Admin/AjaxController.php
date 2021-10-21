<?php

namespace App\Controller\Admin;

use App\Service\Uploader\BlogUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/ajax')]
class AjaxController extends AbstractController
{
    #[Route('/image_delete/{id}', name: 'admin_ajax_image_delete', methods: ['PATCH'])]
    public function imageDelete(int $id, Request $request, EntityManagerInterface $em, BlogUploader $uploader): JsonResponse
    {
        $model = $request->query->get('_model');
        $token = $request->query->get('_token');

        $object = $em->getRepository($model)->find($id);

        if ($request->isXmlHttpRequest() && $this->isCsrfTokenValid('delete' . $object->getId(), $token)) {
            $image = $object->getImage();
            // todo: refactoring for any images
            $uploader->removePostImage($image);
            $uploader->removeCategoryImage($image);
            $object->setImage(null);
            $em->flush();
            $data = [
                'status' => true,
                'message' => sprintf('Image %s deleted', $image),
            ];
        } else {
            $data = [
                'status' => false,
                'message' => 'Something went wrong! Try again later.',
            ];
        }

        return $this->json($data);
    }
}
