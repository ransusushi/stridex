<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<User>
 */
class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var bool $isEdit */
        $isEdit = (bool) $options['is_edit'];
        /** @var User|null $user */
        $user = $builder->getData();
        $defaultRole = $user instanceof User ? $user->getPrimaryRole() : User::ROLE_STAFF;

        $passwordConstraints = [
            new Length(
                min: 6,
                max: 4096,
                minMessage: 'Password must be at least {{ limit }} characters long.'
            ),
        ];

        if (!$isEdit) {
            array_unshift($passwordConstraints, new NotBlank(message: 'Please enter a password.'));
        }

        $builder
            ->add('name', TextType::class, [
                'label' => 'Full Name',
                'attr' => [
                    'placeholder' => 'e.g. Maria Santos',
                    'autocomplete' => 'name',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email Address',
                'attr' => [
                    'placeholder' => 'user@example.com',
                    'autocomplete' => 'email',
                ],
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'System Role',
                'mapped' => false,
                'data' => $defaultRole,
                'choices' => [
                    'Staff (Standard Access)' => User::ROLE_STAFF,
                    'Administrator (Full Access)' => User::ROLE_ADMIN,
                ],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Account Status',
                'choices' => [
                    'Active' => User::STATUS_ACTIVE,
                    'Disabled' => User::STATUS_DISABLED,
                    'Archived' => User::STATUS_ARCHIVED,
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'mapped' => false,
                'required' => !$isEdit,
                'first_options' => [
                    'label' => $isEdit ? 'New Password (leave blank to keep current)' : 'Password',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => $isEdit ? 'Leave blank unless resetting password' : 'Minimum 6 characters',
                    ],
                ],
                'second_options' => [
                    'label' => $isEdit ? 'Confirm New Password' : 'Confirm Password',
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'placeholder' => 'Repeat password',
                    ],
                ],
                'invalid_message' => 'The password fields must match.',
                'constraints' => $passwordConstraints,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'is_edit' => false,
        ]);
        $resolver->setAllowedTypes('is_edit', 'bool');
    }
}
