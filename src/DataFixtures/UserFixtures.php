<?php


namespace App\DataFixtures;

use App\Entity\Media;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new FakerPicsumImagesProvider($faker));
        for($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setPassword($faker->password);
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setSlug($faker->slug);
            $user->setBirthDate($faker->dateTimeBetween('-70 years', '-18 years'));
            //media
            $avatar = new Media();
            $avatar->setFilename($faker->firstName);
            $avatar->setUrl($faker->imageUrl(100, 100));
            $avatar->setAvatarId($avatar);
            $manager->persist($avatar);
        }
        $newUser = new User();
        $newUser->setFirstname('User');
        $newUser->setLastname('User');
        $newUser->setEmail('user@user.com');
        $newBirthdate = new \DateTime();
        $newUser->setPassword($this->passwordHasher->hashPassword($newUser, 'user'));

        $manager->persist($newUser);

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
