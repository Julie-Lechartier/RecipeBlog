<?php

namespace App\Form;

use App\Entity\Banner;
use App\Entity\Media;
use Doctrine\DBAL\Types\BooleanType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BannerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('link', TextType::class, [
                'label' => 'Lien',
                'attr' => [
                    'class' => 'form-control',
                ]
            ])
            ->add('button', ChoiceType::class, [
                'label' => 'Apparition du bouton ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
                'expanded' => true,
                'choice_attr' => [
                    'Oui' => ['class' => 'form-check-input'],
                    'Non' => ['class' => 'form-check-input']
                ]
            ])
            ->add('buttonText', TextType::class, [
                'label' => 'Texte du bouton',
                'attr' => [
                    'class' => 'form-control',
                ]

            ])
            ->add('isActive', ChoiceType::class, [
                'label' => 'La bannière est elle active ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false
                ],
                'expanded' => true,
                'choice_attr' => [
                    'Oui' => ['class' => 'form-check-input'],
                    'Non' => ['class' => 'form-check-input']
                ]
            ])
            ->add('image', FileType::class, [
                'label' => 'Image (JPG, PNG)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image JPG ou PNG',
                    ])
                ],
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Banner::class,
        ]);
    }
}
