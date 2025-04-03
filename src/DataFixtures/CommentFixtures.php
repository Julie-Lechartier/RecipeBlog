<?php
// CommentFixtures.php
namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Recipe;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function __construct(
        private readonly SluggerInterface $slugger
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $manager->createQuery('DELETE FROM App\Entity\Comment')->execute();

        $faker = Factory::create('fr_FR');
        $users = $manager->getRepository(User::class)->findAll();
        $recipes = $manager->getRepository(Recipe::class)->findAll();


        if (empty($users) || empty($recipes)) {
            throw new \Exception('No users or recipes found.');
        }

        for ($i = 0; $i < 10; $i++) {
            $newComment = new Comment();
            $newComment->setRecipe($recipes[array_rand($recipes)]);
            $newComment->setUser($users[array_rand($users)]);
            $newComment->setContent($faker->text(200));
            $newComment->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 year', 'now')));
            $newComment->setSlug($this->slugger->slug($newComment->getContent()));
            $manager->persist($newComment);
        }
        $manager->flush();
    }
    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            RecipeFixtures::class
        ];
    }
}