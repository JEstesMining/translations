<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\InviteCode;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', Type\EmailType::class, [
                'label' => 'email',
            ])
            ->add('plainPassword', Type\PasswordType::class, [
                'label' => 'password',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
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
            ])
            ->add('invite_code', Type\TextType::class, [
                'label' => 'invite code',
                'mapped' => false,
                'constraints' => [
                    new InviteCode(),
                ],
            ])
            //->add('agreeTerms', CheckboxType::class, [
            //    'mapped' => false,
            //    'constraints' => [
            //        new IsTrue([
            //            'message' => 'You should agree to our terms.',
            //        ]),
            //    ],
            //])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
