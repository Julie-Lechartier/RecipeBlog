<?php

namespace App\DataFixtures;

use App\Entity\Comment;
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

        // Load recipes with their steps and media third
        $recipeFixtures = new RecipeFixtures($this->slugger);
        $recipeFixtures->load($manager);

        //load comments last
        $commentsFixtures = new CommentFixtures($this->slugger);
        $commentsFixtures->load($manager);

        self::$loaded = true;
    }
}
