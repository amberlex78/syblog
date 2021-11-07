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
    private array $response = [
        'success' => false,
        'message' => 'HTTP 400 Bad Request!',
    ];

    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('/change/{id<\d+>}/status', name: 'admin_ajax_change_status', methods: ['PATCH'])]
    public function changeStatus(int $id, Request $request): JsonResponse
    {
        $entity = $this->em->getRepository($request->get('model'))->findOneBy(['id' => $id]);

        if (!$entity) {
            return $this->json($this->response = ['message' => 'HTTP 404 Not Found!'], 404);
        }

        if ($request->isXmlHttpRequest()) {
            $entity->setIsActive(!$entity->getIsActive());
            $this->em->flush();

            return $this->json($this->response = [
                'success' => true,
                'message' => $entity->getIsActive(),
            ]);
        }

        return $this->json($this->response, 400);
    }

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
