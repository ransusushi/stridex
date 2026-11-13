<?php

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
use Symfony\Component\Validator\Constraints\Regex;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User|null $user */
        $user = $options['data'];
        $roleChoices = [
            'Admin' => User::ROLE_ADMIN,
            'Staff' => User::ROLE_STAFF,
        ];

        if ($options['is_edit']) {
            $roleChoices['Customer'] = User::ROLE_USER;
        }

        $builder
            ->add('name', TextType::class, ['label' => 'Full Name'])
            ->add('email', EmailType::class, ['label' => 'Email Address'])
            ->add('primaryRole', ChoiceType::class, [
                'label' => 'System Role',
                'mapped' => false,
                'choices' => $roleChoices,
                'data' => $user?->getPrimaryRole() ?? User::ROLE_STAFF,
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
                'invalid_message' => 'The password fields must match.',
                'mapped' => false,
                'required' => !$options['is_edit'],
                'first_options' => [
                    'label' => $options['is_edit'] ? 'New Password' : 'Password',
                    'constraints' => $options['is_edit']
                        ? [new Regex(pattern: '/^(?:.{8,})?$/', message: 'A new password must be at least 8 characters long.')]
                        : [
                            new NotBlank(message: 'Please enter a password.'),
                            new Length(min: 8, minMessage: 'Password must be at least {{ limit }} characters long.'),
                        ],
                ],
                'second_options' => [
                    'label' => 'Confirm Password',
                ],
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
