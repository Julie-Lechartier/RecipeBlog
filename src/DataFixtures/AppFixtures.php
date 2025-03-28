<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private static bool $loaded = false;

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly SluggerInterface $slugger
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        if (self::$loaded) {
            return;
        }

        // Load users first (needed for recipe authors)
        $userFixtures = new UserFixtures($this->passwordHasher, $this->slugger);
        $userFixtures->load($manager);

        // Load categories second
        $categoryFixtures = new RecipeCategoryFixtures($this->slugger);
        $categoryFixtures->load($manager);

        // Load recipes with their steps and media last
        $recipeFixtures = new RecipeFixtures($this->slugger);
        $recipeFixtures->load($manager);

        self::$loaded = true;
    }
}
