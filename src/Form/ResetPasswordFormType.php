<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ResetPasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les deux mots de passe ne correspondent pas.',
            'first_options' => [
                'label' => 'Nouveau mot de passe',
                'attr' => ['class' => 'password-field form-control'],
            ],
            'second_options' => [
                'label' => 'Confirmation du mot de passe',
                'attr' => ['class' => 'password-field form-control'],
            ],
            'required' => true,
            'data' => $options['data'] ?? null,
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer un mot de passe',
                ]),
                new Length([
                    'min' => 8,
                    'minMessage' => 'Votre mot de passe doit contenir au moins {{ limit }} caractères',
                    'max' => 4096,
                ]),
                new Assert\Regex([
                    'pattern' => '/[a-z]/',
                    'message' => 'Votre mot de passe doit contenir au moins une lettre minuscule',
                ]),
                new Assert\Regex([
                    'pattern' => '/[A-Z]/',
                    'message' => 'Votre mot de passe doit contenir au moins une lettre majuscule',
                ]),
                new Assert\Regex([
                    'pattern' => '/\d/',
                    'message' => 'Votre mot de passe doit contenir au moins un chiffre',
                ]),
                new Assert\Regex([
                    'pattern' => '/[^a-zA-Z\d]/',
                    'message' => 'Votre mot de passe doit contenir au moins un caractère spécial',
                ]),
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
