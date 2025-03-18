<?php


namespace App\DataFixtures;

use App\Entity\Media;
use App\Entity\User;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RecipeFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new FakerPicsumImagesProvider($faker));
        for($i = 0; $i < 20; $i++) {
            $recipe = new Recipe();
            $recipe->setTitle($faker->title);
            $recipe->setDescription($faker->text(200));
            $recipe->setPreparationTime($faker->numberBetween(10, 200));
            $recipe->setServing($faker->numberBetween(1, 12));
            //author
            $author = new User;
            $author = $this->setPseudo();
            //media
            $img = new Media();
            $img->setFilename($faker->firstName);
            $img->setUrl($faker->imageUrl(100, 100));
            $manager->persist($img);
        }
        $manager->flush();
    }
}
