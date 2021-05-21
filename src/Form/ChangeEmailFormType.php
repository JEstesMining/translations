<?php declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;

class ChangeEmailFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', Type\EmailType::class, [
                'label' => 'new email address',
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'please enter new email',
                    ]),
                    new Constraints\Email([
                        'message' => 'invalid email address',
                    ]),
                    //new UniqueEntity([
                    //    'entityClass' => User::class,
                    //    'fields'      => 'email',
                    //    'message'     => 'email already in use',
                    //]),
                ]
            ])
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
        ;

        $builder->get('email')->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
            $form = $event->getForm();
            $data = strtolower($event->getData()); // normalize
            $entity = $options['em']->getRepository(User::class)->findOneBy([
                'email' => $data,
            ]);
            if ($entity && $entity->getId() != $options['user']->getId()) {
                $form->addError(new FormError('email in use'));
            }
            if ($data == $options['user']->getEmail()) {
                $form->addError(new FormError('email was not modified'));
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            //'data_class' => User::class,
            //'translation_domain' => 'forms',
        ]);
        $resolver->setRequired([
            'em', 'user',
        ]);
    }
}
