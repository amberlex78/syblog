<?php

namespace App\Form\Admin;

use App\Entity\Page;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'empty_data' => '',
                'attr' => ['autofocus' => true],
            ])
            ->add('parent', EntityType::class, [
                'class' => Page::class,
//                'group_by' => 'parent',
//                'query_builder' => function (PageRepository $r) {
//                    $queryBuilder = $r->createQueryBuilder('c');
//                    return $queryBuilder->where($queryBuilder->expr()->isNotNull('c.parent'));
//                },
            ])
            ->add('slug', TextType::class, [
                'empty_data' => '',
            ])
            ->add('content', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 5,
                ],
            ])
            ->add('isActive')
            ->add('seoTitle')
            ->add('seoKeywords')
            ->add('seoDescription', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 2,
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Page::class,
        ]);
    }
}
