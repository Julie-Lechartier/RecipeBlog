<?php

namespace App\DataFixtures;

use App\Entity\RecipeCategory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeCategoryFixtures extends Fixture
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        // Clear existing categories
        $manager->createQuery('DELETE FROM App\Entity\RecipeCategory')->execute();

        $categories = [
            'Apéro',
            'Entrées',
            'Plats',
            'Desserts',
            'Soupes',
            'Salades',
            'Pâtes',
            'Viandes',
            'Poissons',
            'Végétarien',
            'Végétalien',
            'Petits-déjeuners',
            'Boissons',
            'Sauces',
            'Snacks',
            'Pâtisseries'
        ];

        foreach ($categories as $categoryName) {
            $category = new RecipeCategory();
            $category->setName($categoryName);
            $category->setSlug($this->slugger->slug($categoryName)->lower());
            $manager->persist($category);
        }

        $manager->flush();
    }
} 