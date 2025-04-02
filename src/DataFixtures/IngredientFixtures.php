<?php
namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class IngredientFixtures extends Fixture
{
    public function __construct(
        private readonly SluggerInterface $slugger
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        $manager->createQuery('DELETE FROM App\Entity\RecipeIngredient')->execute();
        $manager->createQuery('DELETE FROM App\Entity\Ingredient')->execute();

        $ingredients = [
            'Farine',
            'Sucre',
            'Sel',
            'Poivre',
            'Huile d\'olive',
            'Beurre',
            'Œufs',
            'Lait',
            'Eau',
            'Levure',
            'Pomme de terre',
            'Carotte',
            'Oignon',
            'Ail',
            'Tomate',
            'Poulet',
            'Bœuf',
            'Porc',
            'Poisson',
            'Crevettes',
            'Riz',
            'Pâtes',
            'Fromage',
            'Crème fraîche',
            'Moutarde',
            'Vinaigre',
            'Cannelle',
            'Paprika',
            'Cumin',
            'Thym',
            'Basilic',
            'Persil',
            'Citron',
            'Orange',
            'Pomme',
            'Banane',
            'Chocolat',
            'Vanille',
            'Miel',
            'Noix',
        ];

        foreach ($ingredients as $ingredientName) {
            $ingredient = new Ingredient();
            $ingredient->setName($ingredientName);
            $ingredient->setSlug($this->slugger->slug($ingredient->getName())->lower());
            $manager->persist($ingredient);
        }

        $manager->flush();
    }
}