<?php

namespace App\Entity;

use App\Repository\RecipeIngredientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeIngredientRepository::class)]
class RecipeIngredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipe $recipeId = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ingredient $ingredientId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $quantity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipeId(): ?Recipe
    {
        return $this->recipeId;
    }

    public function setRecipeId(?Recipe $recipeId): static
    {
        $this->recipeId = $recipeId;

        return $this;
    }

    public function getIngredientId(): ?ingredient
    {
        return $this->ingredientId;
    }

    public function setIngredientId(?ingredient $ingredientId): static
    {
        $this->ingredientId = $ingredientId;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(?string $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }
}
