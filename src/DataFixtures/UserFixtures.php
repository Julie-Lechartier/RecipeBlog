<?php


namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
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
