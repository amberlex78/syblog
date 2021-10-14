<?php

namespace App\Form\Front\Auth;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Email Address',
                    'autofocus' => true,
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'required' => true,
                'type' => PasswordType::class,
                'invalid_message' => 'The passwords do not match.',
                'first_options' => [
                    'label' => 'Password',
                    'attr' => ['placeholder' => 'Password'],
                    'constraints' => [
                        new NotBlank(['message' => 'Please enter a password.']),
                        new Length(['min' => 6, 'max' => 128]),
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirm Password',
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
