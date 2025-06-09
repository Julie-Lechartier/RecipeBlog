<?php

namespace App\DataFixtures;

use App\Entity\RecipeIngredient;
use App\Entity\Recipe;
use App\Entity\Ingredient;
use App\Entity\Unit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class RecipeIngredientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $recipes = $manager->getRepository(Recipe::class)->findAll();
        $ingredients = $manager->getRepository(Ingredient::class)->findAll();
        $units = $manager->getRepository(Unit::class)->findAll();
        foreach ($recipes as $recipe) {
            $ingredientCount = rand(3, 6);
            shuffle($ingredients);
            for ($i = 0; $i < $ingredientCount; $i++) {
                $recipeIngredient = new RecipeIngredient();

                $recipeIngredient->setRecipe($recipe);
                $recipeIngredient->setIngredient($ingredients[$i]);
                $randomUnit = $units[array_rand($units)];
                $recipeIngredient->setUnit($randomUnit);

                $unitName = strtolower($randomUnit->getName());
                if ($unitName === 'g') {
                    $quantity = rand(10, 500);
                } elseif ($unitName === 'kg') {
                    $quantity = rand(1, 5);
                } elseif ($unitName === 'l') {
                    $quantity = rand(1, 3);
                } elseif ($unitName === 'ml') {
                    $quantity = rand(10, 250);
                } elseif ($unitName === 'cl') {
                    $quantity = rand(10, 250);
                } elseif ($unitName === 'cuillère à soupe') {
                    $quantity = rand(1, 5);
                } elseif ($unitName === 'cuillère à café') {
                    $quantity = rand(1, 5);
                } elseif ($unitName === 'pincée') {
                    $quantity = 1;
                } else {
                    $quantity = rand(1, 10);
                }

                $recipeIngredient->setQuantity($quantity);
                $manager->persist($recipeIngredient);
            }
        }

        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            RecipeFixtures::class,
            IngredientFixtures::class,
            UnitFixtures::class,
        ];
    }
}