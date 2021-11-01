<?php

namespace App\Form\Admin\Blog;

use App\Entity\Blog\Category;
use App\Entity\Blog\Post;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('category', EntityType::class, [
                'placeholder' => 'Without category',
                'required' => false,
                'class' => Category::class,
            ])
            ->add('title', TextType::class, [
                'attr' => ['autofocus' => true],
            ])
            ->add('slug', TextType::class)
            ->add('preview', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'rows' => 3,
                ],
            ])
            ->add('content', CKEditorType::class, [
                'required' => false,
            ])
            ->add('image', FileType::class, array(
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Image(['maxSize' => '2M'])
                ]
            ))
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
            'data_class' => Post::class,
        ]);
    }
}
