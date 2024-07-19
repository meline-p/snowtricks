<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ProfilePictureFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('profilPicture', FileType::class, [
            'label' => 'Modifier ma photo de profil',
            'multiple' => false,
            'mapped' => false,
            'required' => false,
            'attr' => [
                'class' => 'form-control',
            ],
            'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                            'image/webp',
                            'image/avif',
                        ],
                        'mimeTypesMessage' => 'Veuillez envoyer une image au format png, jpg, jpeg, webp ou avif.',
                    ]),
                ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}
