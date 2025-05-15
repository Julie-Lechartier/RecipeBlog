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

        $unit = [
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

        foreach ($unit as $unitName) {
            $unit = new Unit();
            $unit->setName($unitName);
            $unit->setSlug($unitName);
            $manager->persist($unit);
        }
        $manager->flush();
    }
}