<?php

namespace App\Form;

use App\Entity\Recipe;
use App\Entity\RecipeCategory;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'attr' => ['class' => 'form-control']
            ])
            ->add('authorId', TextType::class, [
                'label' => 'Auteur',
                'disabled' => true,
                'required' => $options['author_username']
            ])
            ->add('description', TextType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('preparationTime', IntegerType::class, [
                'label' => 'Temps de préparation',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('serving', IntegerType::class, [
                'label' => 'Nombres de personnes',
                'attr' => ['class' => 'form-control']
            ])
            ->add('category', EntityType::class, [
                'label' => 'Type de préparation',
                'class' => RecipeCategory::class,
                'choice_label' => 'id',
                'multiple' => true,
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
            'author_username' => null
        ]);
    }
}
