<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fileName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $path = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'media')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Recipe $recipe = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "avatars")]
    #[ORM\JoinColumn(name: "avatar_id", referencedColumnName: "id", nullable: true)]
    private ?User $avatar = null;

    /**
     * @var Collection<int, Banner>
     */
    #[ORM\ManyToMany(targetEntity: Banner::class, mappedBy: 'image')]
    private Collection $banners;

    public function __construct()
    {
        $this->banners = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): static
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getAvatar(): ?User
    {
        return $this->avatar;
    }

    public function setAvatar(?User $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return Collection<int, Banner>
     */
    public function getBanners(): Collection
    {
        return $this->banners;
    }

    public function addBanner(Banner $banner): static
    {
        if (!$this->banners->contains($banner)) {
            $this->banners->add($banner);
            $banner->addImage($this);
        }

        return $this;
    }

    public function removeBanner(Banner $banner): static
    {
        if ($this->banners->removeElement($banner)) {
            $banner->removeImage($this);
        }

        return $this;
    }
}
