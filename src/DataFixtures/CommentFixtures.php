<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;

class CommentFixtures extends Fixture
{
    public function __construct(

        private readonly SluggerInterface $slugger
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $user = $manager->getRepository(User::class)->findAll();
        $recipe = $manager->getRepository(Recipe::class)->findAll();
        for ($i = 0; $i < 10; $i++) {
            $newComment = new Comment();
            $newComment->setRecipe($recipe[array_rand($recipe)]);
            $newComment->setUser($user[array_rand($user)]);
            $newComment->setContent($faker->text(200));
            $newComment->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
            $newComment->setSlug($this->slugger->slug($newComment->getContent()));
            $manager->persist($newComment);
        }
        $manager->flush();
    }
}