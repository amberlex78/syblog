<?php

namespace App\Form\Admin\Blog;

use App\Entity\Blog\Category;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'empty_data' => '',
                'attr' => ['autofocus' => true],
            ])
            ->add('slug', TextType::class, [
                'empty_data' => '',
            ])
            ->add('description', CKEditorType::class, [
                'required' => false,
            ])
            ->add('image')
            ->add('seo_title')
            ->add('seo_keywords')
            ->add('seo_description', TextareaType::class, [
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
            'data_class' => Category::class,
        ]);
    }
}
