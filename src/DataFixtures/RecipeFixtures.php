<?php

namespace App\DataFixtures;

use App\Entity\Media;
use App\Entity\Recipe;
use App\Entity\RecipeCategory;
use App\Entity\Step;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecipeFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new FakerPicsumImagesProvider($faker));

        $users = $manager->getRepository(User::class)->findAll();
        $categories = $manager->getRepository(RecipeCategory::class)->findAll();

        if (empty($users)) {
            throw new \Exception('No users found! Cannot assign authors to recipes.');
        }

        $categoryMap = [];
        foreach ($categories as $category) {
            $categoryMap[$category->getName()] = $category;
        }

        $recipes = [
            // Your existing recipe data...
            [
                'title' => 'Bruschetta aux tomates',
                'categories' => ['Apéro'],
                'prepTime' => 20,
                'serving' => 4
            ],
            // ... other recipes ...
        ];

        foreach ($recipes as $recipeData) {
            $recipe = new Recipe();
            $recipe->setTitle($recipeData['title']);
            $recipe->setSlug($this->slugger->slug($recipeData['title'])->lower());
            $recipe->setDescription($faker->paragraph(3));

            // Convert minutes to TIME format (HH:MM:00)
            $minutes = $recipeData['prepTime'];
            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;

            $prepTime = new \DateTime();
            $prepTime->setTime($hours, $remainingMinutes, 0);
            $recipe->setPreparationTime($prepTime);

            $recipe->setServing($recipeData['serving']);
            $recipe->setAuthor($faker->randomElement($users));

            foreach ($recipeData['categories'] as $categoryName) {
                if (isset($categoryMap[$categoryName])) {
                    $recipe->addCategory($categoryMap[$categoryName]);
                }
            }

            // Persist the Recipe first
            $manager->persist($recipe);

            // Create and persist Steps
            $numberOfSteps = $faker->numberBetween(3, 8);
            for ($j = 1; $j <= $numberOfSteps; $j++) {
                $step = new Step();
                $step->setContent($faker->paragraph(1));
                $step->setStepNumber($j);
                $step->setRecipe($recipe);
                $recipe->addStep($step);
                $manager->persist($step);
            }

            // Create and persist Media
            $media = new Media();
            $media->setFilename($faker->slug);
            $media->setUrl($faker->imageUrl(800, 600));
            $media->setRecipe($recipe);
            $recipe->addMedia($media);
            $manager->persist($media);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            RecipeCategoryFixtures::class
        ];
    }
}