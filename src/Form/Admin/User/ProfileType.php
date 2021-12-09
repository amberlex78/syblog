<?php

namespace App\Form\Admin\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('avatar', FileType::class, array(
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Image(['maxSize' => '2M'])
                ]
            ))
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'required' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'The passwords do not match.',
                'first_options' => [
                    'label' => 'New password',
                    'attr' => ['placeholder' => 'Password'],
                    'constraints' => [
                        new Length(['min' => 6, 'max' => 128]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirm password',
                    'attr' => ['placeholder' => 'Confirm Password']
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
