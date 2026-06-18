<?php

namespace App\Form\Type;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType as SymfonyPasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder->add(
            'email',
            EmailType::class,
            [
                'label' => 'label.email',
                'required' => true,
                'attr' => ['max_length' => 64],
            ]
        );
        $builder->add(
            'name',
            TextType::class,
            [
                'label' => 'label.name',
                'required' => true,
                'attr' => ['max_length' => 64],
            ]
        );
        $builder->add('password', RepeatedType::class, [
            'type' => SymfonyPasswordType::class,
            'first_options'  => [
                'label' => 'label.new_password',
                'required' => true,
            ],
            'second_options' => [
                'label' => 'label.confirm_password',
                'required' => true,
            ],

            'invalid_message' => 'form.invalid_password_error',
        ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => User::class]);
    }
}
