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

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        if (self::$loaded) {
            return;
        }

        $userFixtures = new UserFixtures($this->passwordHasher, $this->slugger);
        $userFixtures->load($manager);

        $categoryFixtures = new RecipeCategoryFixtures($this->slugger);
        $categoryFixtures->load($manager);

        $recipeFixtures = new RecipeFixtures($this->slugger);
        $recipeFixtures->load($manager);

        $commentsFixtures = new CommentFixtures($this->slugger);
        $commentsFixtures->load($manager);

        $ingredientsFixtures = new IngredientFixtures($this->slugger);
        $ingredientsFixtures->load($manager);

        $unitFixtures = new UnitFixtures();
        $unitFixtures->load($manager);

        $recipeIngredientFixtures = new RecipeIngredientFixtures();
        $recipeIngredientFixtures->load($manager);

        $manager->flush();

        self::$loaded = true;
    }
}
