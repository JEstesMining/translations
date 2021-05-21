<?php declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints;

class SettingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locale', Type\LocaleType::class, [
                'label' => 'locale',
                'placeholder' => 'please choose locale',
                'preferred_choices' => ['en'],
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'please enter your locale',
                    ]),
                    new Constraints\Locale(),
                ]
            ])
            ->add('timezone', Type\TimezoneType::class, [
                'label' => 'timezone',
                'placeholder' => 'please choose timezone',
                'preferred_choices' => ['America/New_York'],
                'constraints' => [
                    new Constraints\NotBlank([
                        'message' => 'please enter your timezone',
                    ]),
                    new Constraints\Timezone(),
                ]
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
