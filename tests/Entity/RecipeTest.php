<?php

namespace App\Tests\Entity;

use App\Entity\Recipe;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\RecipeCategory;
use App\Entity\Step;
use App\Entity\RecipeIngredient;
use App\Entity\Ingredient;
use App\Entity\Unit;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class RecipeTest extends TestCase
{
    private Recipe $recipe;
    private User $user;
    private RecipeCategory $category;

    protected function setUp(): void
    {
        $this->recipe = new Recipe();
        $this->user = new User();
        $this->category = new RecipeCategory();
    }

    public function testRecipeBasicProperties(): void
    {
        $title = "Tarte aux pommes";
        $description = "Une délicieuse tarte aux pommes";
        $serving = 6;
        $preparationTime = new DateTimeImmutable('1970-01-01 01:30:00');

        $this->recipe->setTitle($title);
        $this->recipe->setDescription($description);
        $this->recipe->setServing($serving);
        $this->recipe->setPreparationTime($preparationTime);

        $this->assertEquals($title, $this->recipe->getTitle());
        $this->assertEquals($description, $this->recipe->getDescription());
        $this->assertEquals($serving, $this->recipe->getServing());
        $this->assertEquals($preparationTime, $this->recipe->getPreparationTime());
    }

    public function testRecipeWithCategory(): void
    {
        $categoryName = "Dessert";
        $this->category->setName($categoryName);

        $this->recipe->addCategory($this->category);

        $this->assertCount(1, $this->recipe->getCategory());
        $this->assertTrue($this->recipe->getCategory()->contains($this->category));
        $this->assertEquals($categoryName, $this->recipe->getCategory()->first()->getName());

        $this->recipe->removeCategory($this->category);
        $this->assertCount(0, $this->recipe->getCategory());
    }

    public function testAddAndRemoveStep(): void
    {
        $step = new Step();
        $step->setContent("Mélanger les ingrédients");
        $step->setStepNumber(1);

        $this->recipe->addStep($step);

        $this->assertCount(1, $this->recipe->getSteps());
        $this->assertTrue($this->recipe->getSteps()->contains($step));

        $this->recipe->removeStep($step);
        $this->assertCount(0, $this->recipe->getSteps());
    }

    public function testAddAndRemoveRecipeIngredient(): void
    {
        $ingredient = new Ingredient();
        $ingredient->setName("Pomme");

        $unit = new Unit();
        $unit->setName("gramme");

        $recipeIngredient = new RecipeIngredient();
        $recipeIngredient->setIngredient($ingredient);
        $recipeIngredient->setUnit($unit);
        $recipeIngredient->setQuantity(500);

        $this->recipe->addRecipeIngredient($recipeIngredient);

        $this->assertCount(1, $this->recipe->getRecipeIngredients());
        $this->assertTrue($this->recipe->getRecipeIngredients()->contains($recipeIngredient));

        $this->recipe->removeRecipeIngredient($recipeIngredient);
        $this->assertCount(0, $this->recipe->getRecipeIngredients());
    }

    public function testAddAndRemoveComment(): void
    {
        $comment = new Comment();
        $comment->setContent("Très bonne recette !");
        $comment->setUser($this->user);

        $this->recipe->addComment($comment);

        $this->assertCount(1, $this->recipe->getComments());
        $this->assertTrue($this->recipe->getComments()->contains($comment));

        $this->recipe->removeComment($comment);
        $this->assertCount(0, $this->recipe->getComments());
    }

    public function testRecipeSlugGeneration(): void
    {
        $title = "Tarte aux Pommes à la Cannelle";

        $this->recipe->setTitle($title);

        $this->assertEquals('tarte-aux-pommes-a-la-cannelle', $this->recipe->getSlug());
    }

    public function testRecipeDefaultValues(): void
    {
        $this->assertEmpty($this->recipe->getComments());
        $this->assertEmpty($this->recipe->getSteps());
        $this->assertEmpty($this->recipe->getCategory());
        $this->assertEmpty($this->recipe->getMedia());
        $this->assertEmpty($this->recipe->getRecipeIngredients());
        $this->assertNull($this->recipe->getAuthor());
        $this->assertNull($this->recipe->getTitle());
        $this->assertNull($this->recipe->getDescription());
        $this->assertNull($this->recipe->getPreparationTime());
        $this->assertNull($this->recipe->getServing());
        $this->assertNull($this->recipe->getCreateAt());
        $this->assertNull($this->recipe->getSlug());
    }
}