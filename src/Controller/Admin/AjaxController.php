<?php

namespace App\Controller\Admin;

use App\Service\Uploader\BlogUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/ajax', name: 'admin_ajax_')]
class AjaxController extends AbstractController
{
    const FIELD_IS_ACTIVE = 'isActive';

    private array $response = [
        'success' => false,
        'message' => 'Bad Request!',
    ];

    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    private static function getAllowedFields(): array
    {
        return [
            self::FIELD_IS_ACTIVE,
        ];
    }

    #[Route('/change/{id<\d+>}/boolean', name: 'change_boolean', methods: ['PATCH'])]
    public function changeBoolean(int $id, Request $request): JsonResponse
    {
        $field = $request->get('field') ?? self::FIELD_IS_ACTIVE;
        if (in_array($field, self::getAllowedFields())) {
            $fieldSet = 'set' . $field;
            $fieldGet = 'get' . $field;
        } else {
            $this->response['message'] = 'Invalid field to change!';
            return $this->json($this->response, 500);
        }

        $entity = $this->em->getRepository($request->get('entity'))->findOneBy(['id' => $id]);
        if (!$entity) {
            $this->response['message'] = 'Not Found!';
            return $this->json($this->response, 404);
        }

        if ($request->isXmlHttpRequest()) {
            $entity->$fieldSet(!$entity->$fieldGet());
            $this->em->flush();

            return $this->json($this->response = [
                'success' => true,
                'message' => $entity->getIsActive(),
            ]);
        }

        return $this->json($this->response, 400);
    }

    #[Route('/image_delete/{id}', name: 'image_delete', methods: ['PATCH'])]
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
