<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\RecipeCategory;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Time;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
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
            ->add('authorId', HiddenType::class, [
                'mapped' => false,
                'data' => $options['author_username']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('preparationTime', TimeType::class, [
                'label' => 'Temps de préparation',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'HH:MM'
                ],
                'widget' => 'single_text',
            ])
            ->add('serving', IntegerType::class, [
                'label' => 'Nombres de personnes',
                'attr' => [
                    'class' => 'form-control',
                    'min' => "1"
                ],
                'constraints' => [
                    new Positive([
                        'message' => 'Le nombre de personnes doit être supérieur à 0.',
                    ]),
                ],
            ])
            ->add('category', EntityType::class, [
                'label' => 'Type de préparation',
                'class' => RecipeCategory::class,
                'choice_label' => 'name',
                'multiple' => true,
                'expanded' => true,
                'attr' => ['class' => 'category-checkboxes']
            ])
            ->add('steps', CollectionType::class, [
                'label' => 'Liste des étapes de la recette',
                'entry_type' => StepType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            'author_username' => null
        ]);
    }
}
