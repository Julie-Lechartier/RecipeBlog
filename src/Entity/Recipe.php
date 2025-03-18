<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $preparationTime = null;

    #[ORM\Column]
    private ?int $serving = null;

    #[ORM\ManyToOne(inversedBy: 'recipeId')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    /**
     * @var Collection<int, RecipeCategory>
     */
    #[ORM\ManyToMany(targetEntity: RecipeCategory::class)]
    private Collection $category;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'recipeId', orphanRemoval: true)]
    private Collection $commentId;

    /**
     * @var Collection<int, Media>
     */

    #[ORM\OneToMany(targetEntity: Step::class, mappedBy: 'recipe', cascade: ['persist', 'remove'])]
    private ?Collection $step;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->commentId = new ArrayCollection();
        $this->step = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPreparationTime(): ?int
    {
        return $this->preparationTime;
    }

    public function setPreparationTime(?int $preparationTime): static
    {
        $this->preparationTime = $preparationTime;

        return $this;
    }


    public function getServing(): ?int
    {
        return $this->serving;
    }

    public function setServing(int $serving): static
    {
        $this->serving = $serving;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, RecipeCategory>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(RecipeCategory $category): static
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(RecipeCategory $category): static
    {
        $this->category->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getCommentId(): Collection
    {
        return $this->commentId;
    }

    public function addCommentId(Comment $commentId): static
    {
        if (!$this->commentId->contains($commentId)) {
            $this->commentId->add($commentId);
            $commentId->setRecipeId($this);
        }

        return $this;
    }

    public function removeCommentId(Comment $commentId): static
    {
        if ($this->commentId->removeElement($commentId)) {
            // set the owning side to null (unless already changed)
            if ($commentId->getRecipeId() === $this) {
                $commentId->setRecipeId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */

    public function getStep(): Collection
    {
        return $this->step;
    }

}
