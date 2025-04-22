<?php

namespace App\Form;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\Unit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecipeIngredientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ingredient', EntityType::class, [
                'label' => 'Ingrédient',
                'class' => Ingredient::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner un ingrédient',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('quantity', NumberType::class, [
                'label' => 'Quantité',
                'attr' => ['class' => 'form-control', 'min' => 0, 'step' => '0.01'],
                'html5' => true,
            ])
            ->add('unit', EntityType::class, [
                'label' => 'Unité',
                'class' => Unit::class,
                'choice_label' => 'name',
                'placeholder' => 'Sélectionner une unité',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RecipeIngredient::class,
        ]);
    }
}
