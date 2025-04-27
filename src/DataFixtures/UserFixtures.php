<?php

namespace App\DataFixtures;

use App\Entity\Media;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Smknstd\FakerPicsumImages\FakerPicsumImagesProvider;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly SluggerInterface $slugger
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new FakerPicsumImagesProvider($faker));

        // Admin User
        $existingAdmin = $manager->getRepository(User::class)->findOneBy(['email' => 'julie@admin.com']);
        if (!$existingAdmin) {
            $admin = new User();
            $admin->setFirstname('Julie');
            $admin->setLastname('LCH');
            $admin->setEmail('julie@admin.com');
            $admin->setUsername('J.lch');
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setBirthDate($faker->dateTimeBetween('-70 years', '-18 years'));
            $admin->setNewsletter(false);
            $admin->setSlug($this->slugger->slug($admin->getFirstname() . ' ' . $admin->getLastname())->lower());
            $admin->setPassword($this->passwordHasher->hashPassword($admin, 'root'));
            
            // Create admin avatar
            $adminAvatar = new Media();
            $adminAvatar->setFilename('admin-avatar');
            $adminAvatar->setUrl($faker->imageUrl(100, 100));
            $adminAvatar->setAvatar($admin);
            $manager->persist($adminAvatar);
            $manager->persist($admin);
        }

        // Regular User
        $existingUser = $manager->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);
        if (!$existingUser) {
            $user = new User();
            $user->setFirstname('User');
            $user->setLastname('User');
            $user->setEmail('user@user.com');
            $user->setUsername('user');
            $user->setRoles(['ROLE_USER']);
            $user->setBirthDate($faker->dateTimeBetween('-70 years', '-18 years'));
            $user->setNewsletter(false);
            $user->setPresentation($faker->text);
            $user->setSlug($this->slugger->slug($user->getFirstname() . ' ' . $user->getLastname())->lower());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'user'));
        }

        // Create random users
        for ($i = 0; $i < 20; $i++) {
            $newUser = new User();
            $newUser->setEmail($faker->unique()->email);
            $newUser->setUsername($faker->userName);
            $newUser->setFirstname($faker->firstName);
            $newUser->setLastname($faker->lastName);
            $newUser->setRoles(['ROLE_USER']);
            $newUser->setBirthDate($faker->dateTimeBetween('-70 years', '-18 years'));
            $newUser->setNewsletter($faker->boolean);
            $newUser->setPresentation($faker->text);
            $newUser->setSlug($this->slugger->slug($newUser->getFirstname() . ' ' . $newUser->getLastname())->lower());
            $newUser->setPassword($this->passwordHasher->hashPassword($newUser, 'password'));
        }
        $manager->flush();
    }
}
