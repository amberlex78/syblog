<?php

namespace App\Form\Admin;

use App\Controller\Admin\UserController;
use App\Entity\StaticStorage\UserRolesStorage;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'empty_data' => '',
                'attr' => ['autofocus' => true],
            ]);

        match ($builder->getOption('action_type')) {
            UserController::ACTION_TYPE_NEW => $this->addPlainPasswordNew($builder),
            UserController::ACTION_TYPE_EDIT => $this->addPlainPasswordEdit($builder),
        };

        $builder->add('first_name')
            ->add('last_name')
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'required' => false,
                'choices' => array_flip(UserRolesStorage::getRoleChoices()),
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'action_type' => UserController::ACTION_TYPE_NEW,
        ]);
    }

    private function addPlainPasswordNew(FormBuilderInterface $builder): FormBuilderInterface
    {
        return $builder->add('plainPassword', PasswordType::class, [
            'label' => 'Password',
            'required' => true,
            'mapped' => false,
            'constraints' => [
                new NotBlank(['message' => 'Please enter a password.']),
                new Length(['min' => 6, 'max' => 128]),
            ],
        ]);
    }

    private function addPlainPasswordEdit(FormBuilderInterface $builder): FormBuilderInterface
    {
        return $builder->add('plainPassword', PasswordType::class, [
            'label' => 'Password',
            'required' => false,
            'mapped' => false,
            'constraints' => [
                new Length(['min' => 6, 'max' => 128]),
            ],
        ]);
    }
}
