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

        // Check if admin user already exists
        $existingAdmin = $manager->getRepository(User::class)->findOneBy(['email' => 'julie@admin.com']);
        if (!$existingAdmin) {
            // Create admin user
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

        // Check if regular user already exists
        $existingUser = $manager->getRepository(User::class)->findOneBy(['email' => 'user@user.com']);
        if (!$existingUser) {
            // Create regular user
            $user = new User();
            $user->setFirstname('User');
            $user->setLastname('User');
            $user->setEmail('user@user.com');
            $user->setUsername('user');
            $user->setRoles(['ROLE_USER']);
            $user->setBirthDate($faker->dateTimeBetween('-70 years', '-18 years'));
            $user->setNewsletter(false);
            $user->setSlug($this->slugger->slug($user->getFirstname() . ' ' . $user->getLastname())->lower());
            $user->setPassword($this->passwordHasher->hashPassword($user, 'user'));
            
            // Create user avatar
            $userAvatar = new Media();
            $userAvatar->setFilename('user-avatar');
            $userAvatar->setUrl($faker->imageUrl(100, 100));
            $userAvatar->setAvatar($user);
            $manager->persist($userAvatar);
            $manager->persist($user);
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
            $newUser->setSlug($this->slugger->slug($newUser->getFirstname() . ' ' . $newUser->getLastname())->lower());
            $newUser->setPassword($this->passwordHasher->hashPassword($newUser, 'password'));

            // Create user avatar
            $avatar = new Media();
            $avatar->setFilename($faker->slug);
            $avatar->setUrl($faker->imageUrl(100, 100));
            $avatar->setAvatar($newUser);
            $manager->persist($avatar);
            $manager->persist($newUser);
        }

        $manager->flush();
    }
}
