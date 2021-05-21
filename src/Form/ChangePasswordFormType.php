<?php declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('current_password', Type\PasswordType::class, [
                'label' => 'current password',
                'mapped' => false,
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'Please enter your current password',
                    ]),
                    new UserPassword([
                        'message' => 'Current Password is invalid',
                    ]),
                ]
            ])
            ->add('plain_password', Type\RepeatedType::class, [
                'type' => Type\PasswordType::class,
                'options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                ],
                'first_options' => [
                    'label' => 'password',
                    'constraints' => [
                        new Constraints\NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Constraints\Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ],
                'second_options' => [
                    'label' => 'repeat password',
                    'constraints' => [
                        new Constraints\NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Constraints\Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                ],

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'data_class' => User::class,
            //'translation_domain' => 'forms',
        ]);
    }
}
