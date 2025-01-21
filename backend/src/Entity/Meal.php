<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\MealRepository;
use App\State\MealCollectionProvider;
use App\State\MealGetProvider;
use App\State\MealPostProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: MealRepository::class)]
#[ApiResource]
#[Post(
    processor: MealPostProcessor::class,
    security: "is_granted('ROLE_USER')"
)]
#[GetCollection(
    provider: MealCollectionProvider::class,
    security: "is_granted('ROLE_USER')"
)]
#[Get(
    provider: MealGetProvider::class,
    security: "is_granted('ROLE_USER')"
)]
#[Patch(
    security: "is_granted('ROLE_USER') and object.getUser() === user"
)]
#[Delete(
    security: "is_granted('ROLE_USER') and object.getUser() === user"
)]
class Meal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['item:user:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['item:user:read'])]
    private ?string $type = null;

    #[ORM\Column]
    #[Groups(['item:user:read'])]
    private ?float $calories = null;

    /**
     * @var Collection<int, Food>
     */
    #[ORM\OneToMany(targetEntity: Food::class, mappedBy: 'meal', orphanRemoval: true)]
    private Collection $food;

    #[ORM\ManyToOne(inversedBy: 'meals')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    #[Groups(['item:user:read'])]
    private ?\DateTimeImmutable $created_at = null;


    public function __construct()
    {
        $this->food = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCalories(): ?float
    {
        return $this->calories;
    }

    public function setCalories(float $calories): static
    {
        $this->calories = $calories;

        return $this;
    }

    public function addCalories(float $calories): void
    {
        $this->calories += $calories;
    }

    /**
     * @return Collection<int, Food>
     */
    public function getFood(): Collection
    {
        return $this->food;
    }

    public function addFood(Food $food): static
    {
        if (!$this->food->contains($food)) {
            $this->food->add($food);
            $food->setMeal($this);
        }

        return $this;
    }

    public function removeFood(Food $food): static
    {
        if ($this->food->removeElement($food)) {
            // set the owning side to null (unless already changed)
            if ($food->getMeal() === $this) {
                $food->setMeal(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }


}
