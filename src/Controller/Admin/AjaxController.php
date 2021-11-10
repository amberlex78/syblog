<?php

namespace App\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/ajax', name: 'admin_ajax_')]
class AjaxController extends AbstractController
{
    public const FIELD_IS_ACTIVE = 'isActive';

    public const ALLOWED_FIELDS = [
        self::FIELD_IS_ACTIVE,
    ];

    private array $response = [
        'success' => false,
        'message' => 'Bad Request!',
    ];

    public function __construct(private EntityManagerInterface $em)
    {
    }

    #[Route('/change/{id<\d+>}/boolean', name: 'change_boolean', methods: ['PATCH'])]
    public function changeBoolean(int $id, Request $request): JsonResponse
    {
        $entity = $request->get('entity');
        $token = $request->request->get('_token');
        $field = $request->request->get('field') ?? self::FIELD_IS_ACTIVE;

        if (in_array($field, self::ALLOWED_FIELDS)) {
            $fieldSet = 'set' . $field;
            $fieldGet = 'get' . $field;
        } else {
            $this->response['message'] = 'Invalid field to change!';
            return $this->json($this->response, 500);
        }

        $entity = $this->em->getRepository($entity)->findOneBy(['id' => $id]);
        if (!$entity) {
            $this->response['message'] = 'Not Found!';
            return $this->json($this->response, 404);
        }

        if ($request->isXmlHttpRequest() && $this->isCsrfTokenValid('check' . $id, $token)) {
            $entity->$fieldSet(!$entity->$fieldGet());
            $this->em->flush();

            return $this->json(
                $this->response = [
                    'success' => true,
                    'message' => $entity->getIsActive(),
                ]
            );
        }

        return $this->json($this->response, 400);
    }
}
