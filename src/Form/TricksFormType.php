<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Trick;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class TricksFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TypeTextType::class, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Nom de la figure',
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'style' => 'min-height: 50vh;',
                ],
                'label' => 'Description',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form-control',
                ],
                'label' => 'Catégorie',
                'query_builder' => function (CategoryRepository $categoryRepository) {
                    return $categoryRepository->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
            ])
            ->add('promoteImage', FileType::class, [
                'label' => "Modifier l'image à la une",
                'multiple' => false,
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
                // 'constraints' => [
                //     new File([
                //         'mimeTypes' => [
                //             'image/png',
                //             'image/jpg',
                //             'image/jpeg',
                //             'image/webp',
                //             'image/avif',
                //         ],
                //         'mimeTypesMessage' => 'Veuillez envoyer une image au format png, jpg, jpeg, webp ou avif.',
                //         'maxSize' => '10M', // Limite de taille maximale à 10 mégaoctets
                //         'maxSizeMessage' => 'La taille maximale du fichier est de 10 Mo.',
                //     ]),
                // ]
            ])
            ->add('images', FileType::class, [
                'label' => 'Ajouter des images',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
                // 'constraints' => [
                //     new File([
                //         'mimeTypes' => [
                //             'image/png',
                //             'image/jpg',
                //             'image/jpeg',
                //             'image/webp',
                //             'image/avif',
                //         ],
                //         // 'mimeTypesMessage' => 'Veuillez envoyer une image au format png, jpg, jpeg, webp ou avif de 10 mégas octets maximum',
                //     ]),
                // ],
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideosType::class,
                'label' => 'Ajouter des vidéos',
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}
