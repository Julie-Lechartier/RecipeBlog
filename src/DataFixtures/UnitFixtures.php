<?php

namespace App\DataFixtures;

use App\Entity\Unit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class UnitFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Clear existing categories
        $manager->createQuery('DELETE FROM App\Entity\Unit')->execute();

        $units = [
            'g',
            'kg',
            'mg',
            'ml',
            'l',
            'cl',
            'dl',
            'cuillère à soupe',
            'cuillère à café',
            'pincée',
            'pièce',
            'tranche',
            'tasse',
            'verre',
            'goutte',
        ];

        foreach ($units as $unitName) {
            $units = new Unit();
            $units->setName($unitName);
            $units->setSlug($unitName);
            $manager->persist($units);
        }
        $manager->flush();
    }
}