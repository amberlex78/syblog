<?php

namespace App\Form\Admin;

use App\Entity\StaticStorage\UserRolesStorage;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;

class UserType extends AbstractType
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $user */
        $user = $options['data'] ?? null;
        $isEdit = $user && $user->getId();

        $builder
            ->add('email', EmailType::class, [
                'empty_data' => '',
                'attr' => ['autofocus' => true],
            ])
            ->add('firstName')
            ->add('lastName');

        if ($this->security->isGranted('ROLE_SUPER_ADMIN')) {
            $builder->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'required' => false,
                'choices' => array_flip(UserRolesStorage::getRoleChoices()),
                'multiple' => true,
            ]);
        }

        $builder->add('plainPassword', RepeatedType::class, [
            'mapped' => false,
            'required' => !$isEdit,
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
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
